<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\AppConstants;
use Doctrine\ORM\EntityManagerInterface;
use Svc\LogBundle\Service\EventLog;
use Svc\ParamBundle\Repository\ParamsRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends _BaseController
{
  public function __construct(private readonly EmailVerifier $emailVerifier, private readonly EventLog $eventLog)
  {
  }

  #[Route(path: '/register', name: 'app_register')]
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ParamsRepository $paramsRep): Response
  {
    try {
      $registerEnabled = $paramsRep->getBool(AppConstants::PARAM_REGISTER_ENABLED);
    } catch (\Exception) {
      $registerEnabled = false;
    }

    if (null === $registerEnabled) {
      $paramsRep->setBool(AppConstants::PARAM_REGISTER_ENABLED, true, 'Is registration enabled?');
      $registerEnabled = true;
    }
    if (!$registerEnabled) {
      $this->addFlash('warning', 'Registration is not available at the moment.');

      return $this->redirectToRoute('home');
    }

    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user, ['dataprotectURL' => $this->generateUrl('dataprotection')]);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      // encode the plain password
      $user->setPassword(
        $userPasswordHasher->hashPassword(
          $user,
          $form->get('plainPassword')->getData()
        )
      );

      $entityManager->persist($user);
      $entityManager->flush();

      if ($this->sendRegMail($user)) {
        $this->eventLog->log($user->getId(), AppConstants::LOG_TYPE_REGISTER, ['level' => EventLog::LEVEL_DATA, 'message' => 'New user registered, mail sent.']);
        $this->addFlash('warning', 'An email is send to you. Please click on the link to verify your account.');

        return $this->redirectToRoute('home');
      } else {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->eventLog->log(0, AppConstants::LOG_TYPE_REGISTER, ['level' => EventLog::LEVEL_ERROR, 'message' => 'Cannot send mail for user ' . $user->getUserIdentifier()]);
        $this->addFlash('danger', 'Email cannot send. Please check data and try it again.');
      }
    }

    return $this->render('registration/register.html.twig', [
      'registrationForm' => $form,
    ]);
  }

  #[Route(path: '/verify/email', name: 'app_verify_email')]
  public function verifyUserEmail(Request $request, UserRepository $userRepository, ParamsRepository $paramsRep): Response
  {
    if (!$paramsRep->getBool(AppConstants::PARAM_REGISTER_ENABLED)) {
      $this->addFlash('warning', 'Registration is not available at the moment.');

      return $this->redirectToRoute('home');
    }

    $id = $request->get('id');
    if (null === $id) {
      return $this->redirectToRoute('app_register');
    }

    $user = $userRepository->find($id);
    if (null === $user) {
      return $this->redirectToRoute('app_register');
    }
    // validate email confirmation link, sets User::isVerified=true and persists
    try {
      $this->emailVerifier->handleEmailConfirmation($request, $user);
    } catch (VerifyEmailExceptionInterface $exception) {
      $this->addFlash('verify_email_error', $exception->getReason());

      return $this->redirectToRoute('app_register');
    }
    $this->addFlash('success', 'Your email address has been verified.');
    $this->eventLog->log($user->getId(), AppConstants::LOG_TYPE_REGISTER, ['level' => EventLog::LEVEL_DATA, 'message' => 'User verified.']);

    return $this->redirectToRoute('app_login');
  }

  private function sendRegMail(User $user): bool
  {
    try {
      // generate a signed url and email it to the user
      $this->emailVerifier->sendEmailConfirmation(
        'app_verify_email',
        $user,
        (new TemplatedEmail())
          ->from(new Address('register@seli.li', $this->getAppName() . ' Registration'))
          ->to($user->getEmail())
          ->bcc('register@seli.li')
          ->subject('Please Confirm your Email for ' . $this->getAppName())
          ->htmlTemplate('registration/confirmation_email.html.twig')
      );
    } catch (\Exception) {
      return false;
    }

    return true;
  }
}
