<?php

namespace App\Security;

use App\Service\AppConstants;
use ReCaptcha\ReCaptcha;
use Svc\LogBundle\Service\EventLog;
use Svc\UtilBundle\Service\NetworkHelper;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class CustomAuthenticator extends AbstractLoginFormAuthenticator
{
  use TargetPathTrait;

  final public const LOGIN_ROUTE = 'app_login';

  public function __construct(
    private readonly UrlGeneratorInterface $urlGenerator,
    private readonly EventLog $eventLog,
    private readonly Security $security
  ) {
  }

  public function authenticate(Request $request): Passport
  {
    $email = $request->request->get('email', '');

    $this->checkCaptcha($request->request->get('g-recaptcha-response', ''));

    $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

    return new Passport(
      new UserBadge($email),
      new PasswordCredentials($request->request->get('password', '')),
      [
        new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
        new RememberMeBadge(),
      ]
    );
  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    // write to log file
    $this->eventLog->log($token->getUser()->getId(), AppConstants::LOG_TYPE_LOGIN, ['level' => $this->eventLog::LEVEL_DATA, 'message' => 'Login successful']);

    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
      return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->urlGenerator->generate('home'));
  }

  public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
  {
    $this->eventLog->log(0, AppConstants::LOG_TYPE_LOGIN_FAILED, ['level' => $this->eventLog::LEVEL_WARN, 'message' => 'Login failed (email="' . $request->request->get('email', '') . '")']);
    if ($request->hasSession()) {
      $this->security->logout(false);
    }

    $url = $this->getLoginUrl($request);

    return new RedirectResponse($url);
  }

  protected function getLoginUrl(Request $request): string
  {
    return $this->urlGenerator->generate(self::LOGIN_ROUTE);
  }

  /**
   * private function to check if ReCaptch3 check is successful.
   */
  private function checkCaptcha($gRecaptchaResponse): bool
  {
    $captchaPrivateKey = $_ENV['GOOGLE_RECAPTCHA_SECRET'];
    if (!$captchaPrivateKey) {
      throw new CustomUserMessageAuthenticationException('reCaptcha isn\t configured. Please check it with your admin.');
    }

    $remoteIp = NetworkHelper::getIP();
    $recaptcha = new ReCaptcha($captchaPrivateKey);
    $resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);

    if (!$resp->isSuccess()) {
      if ($_ENV['APP_ENV'] == 'dev') {
        return true;
      }

      $errors = $resp->getErrorCodes();
      throw new CustomUserMessageAuthenticationException('Are you human? If yes, please contact our admin. Error: ' . implode('- ', $errors));
    }

    return true;
  }
}
