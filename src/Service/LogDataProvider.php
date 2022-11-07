<?php

namespace App\Service;

use App\Repository\RedirectsRepository;
use App\Repository\UserRepository;
use Svc\LogBundle\DataProvider\GeneralDataProvider;

class LogDataProvider extends GeneralDataProvider
{
  public function __construct(private readonly UserRepository $userRep)
  {
  }

  /**
   * init the sourceType array.
   */
  protected function initSourceTypes(): bool
  {
    if ($this->isSourceTypesInitialized) {
      return true;
    }
    /*
    $this->sourceTypes[AppConstants::LOG_TYPE_REDIRECT] = 'redirect';
    $this->sourceTypes[AppConstants::LOG_TYPE_GENERAL] = 'general';
    $this->sourceTypes[AppConstants::LOG_TYPE_REDIRECT_ERROR] = 'redirect error';
    */
    $this->sourceTypes[AppConstants::LOG_TYPE_LOGIN] = 'login successful';
    $this->sourceTypes[AppConstants::LOG_TYPE_LOGIN_FAILED] = 'login failed';
    $this->sourceTypes[AppConstants::LOG_TYPE_REGISTER] = 'user registration';
    $this->sourceTypes[AppConstants::LOG_TYPE_ANONHOME] = 'anonymous on homepage';
    $this->sourceTypes[AppConstants::LOG_TYPE_IMPERSONATION_START] = 'impersonation starts';
    $this->sourceTypes[AppConstants::LOG_TYPE_IMPERSONATION_END] = 'impersonation ends';
    $this->sourceTypes[AppConstants::LOG_TYPE_IMPERSONATION_ERROR] = 'impersonation failed';

    $this->sourceTypes[AppConstants::LOG_TOTP_SHOW_QR] = 'Show 2FA QR code';
    $this->sourceTypes[AppConstants::LOG_TOTP_ENABLE] = '2FA enabled';
    $this->sourceTypes[AppConstants::LOG_TOTP_DISABLE] = '2FA disabled';
    $this->sourceTypes[AppConstants::LOG_TOTP_RESET] = '2FA reset';
    $this->sourceTypes[AppConstants::LOG_TOTP_CLEAR_TD] = '2FA trusted devices cleared';
    $this->sourceTypes[AppConstants::LOG_TOTP_DISABLE_BY_ADMIN] = '2FA disabled by admin';
    $this->sourceTypes[AppConstants::LOG_TOTP_RESET_BY_ADMIN] = '2FA reset by admin';
    $this->sourceTypes[AppConstants::LOG_TOTP_CLEAR_TD_BY_ADMIN] = '2FA trusted devices cleared by admin';

    $this->isSourceTypesInitialized = true;

    return true;
  }

  // private bool $isRedirectSourceIDsInitialized = false;

  // private array $redirectSourceIDs = [];

  private bool $isUserSourceIDsInitialized = false;

  private array $userSourceIDs = [];

  /**
   * get the text/description for a source ID / sourceType combination.
   */
  public function getSourceIDText(int $sourceID, ?int $sourceType = null): string
  {
    /*
    if (in_array($sourceType, [AppConstants::LOG_TYPE_REDIRECT, AppConstants::LOG_TYPE_REDIRECT_ERROR])) {
      if (!$this->isRedirectSourceIDsInitialized) {
        $this->initRedirectSourceIDs();
      }

      return array_key_exists($sourceID, $this->redirectSourceIDs) ? $this->redirectSourceIDs[$sourceID] : strval($sourceID);
    }
    */
    if (in_array($sourceType, AppConstants::LOG_ACCOUNT_OPERATIONS, true)) {
      if (!$this->isUserSourceIDsInitialized) {
        $this->initUserSourceIDs();
      }

      return array_key_exists($sourceID, $this->userSourceIDs) ? $this->userSourceIDs[$sourceID] : strval($sourceID);
    }

    return strval($sourceID);
  }

  /**
   * read all redirect, store it in an array.
   *
   * @return void
   */
  /*
  private function initRedirectSourceIDs(): void
  {
    if ($this->isRedirectSourceIDsInitialized) {
      return;
    }

    foreach ($this->redirectsRep->findAll() as $redirect) {
      $this->redirectSourceIDs[$redirect->getId()] = $redirect->getShortName();
    }

    $this->isRedirectSourceIDsInitialized = true;
  }
  */

  /**
   * read all redirect, store it in an array.
   *
   * @return void
   */
  private function initUserSourceIDs(): void
  {
    if ($this->isUserSourceIDsInitialized) {
      return;
    }

    foreach ($this->userRep->findAll() as $user) {
      $this->userSourceIDs[$user->getId()] = $user->getUserIdentifier();
    }

    $this->isUserSourceIDsInitialized = true;
  }
}
