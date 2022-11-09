<?php

namespace App\Service;

use App\Repository\LinkRepository;
use App\Repository\UserRepository;
use Svc\LogBundle\DataProvider\GeneralDataProvider;

class LogDataProvider extends GeneralDataProvider
{
  public function __construct(private readonly UserRepository $userRep, private LinkRepository $linkRep)
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
    $this->sourceTypes[AppConstants::LOG_TYPE_LINK_CALLED] = 'link called';
    $this->sourceTypes[AppConstants::LOG_TYPE_LINK_CHANGED] = 'link changed';
    $this->sourceTypes[AppConstants::LOG_TYPE_LINK_CREATED] = 'link created';
    $this->sourceTypes[AppConstants::LOG_TYPE_LINK_DELETED] = 'link deleted';

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

  private bool $isLinkSourceIDsInitialized = false;

  private array $linkSourceIDs = [];

  private bool $isUserSourceIDsInitialized = false;

  private array $userSourceIDs = [];

  /**
   * get the text/description for a source ID / sourceType combination.
   */
  public function getSourceIDText(int $sourceID, ?int $sourceType = null): string
  {
    if (in_array($sourceType, [AppConstants::LOG_TYPE_LINK_CREATED, AppConstants::LOG_TYPE_LINK_CALLED, AppConstants::LOG_TYPE_LINK_CHANGED, AppConstants::LOG_TYPE_LINK_DELETED])) {
      if (!$this->isLinkSourceIDsInitialized) {
        $this->initLinkSourceIDs();
      }

      return array_key_exists($sourceID, $this->linkSourceIDs) ? $this->linkSourceIDs[$sourceID] : strval($sourceID);
    }

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
   */
  private function initLinkSourceIDs(): void
  {
    if ($this->isLinkSourceIDsInitialized) {
      return;
    }

    foreach ($this->linkRep->findAll() as $link) {
      $this->linkSourceIDs[$link->getId()] = $link->getName();
    }

    $this->isLinkSourceIDsInitialized = true;
  }

  /**
   * read all redirect, store it in an array.
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
