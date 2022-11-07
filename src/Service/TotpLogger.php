<?php

namespace App\Service;

use Svc\LogBundle\Service\EventLog;
use Svc\TotpBundle\Service\TotpLoggerInterface;

class TotpLogger implements TotpLoggerInterface
{
  public function __construct(private readonly EventLog $eventLog)
  {
  }

  public function log(string $text, int $logType, int $userId): bool
  {
    $intLogTyp = match ($logType) {
      self::LOG_TOTP_SHOW_QR => AppConstants::LOG_TOTP_SHOW_QR,
      self::LOG_TOTP_ENABLE => AppConstants::LOG_TOTP_ENABLE,
      self::LOG_TOTP_DISABLE => AppConstants::LOG_TOTP_DISABLE,
      self::LOG_TOTP_RESET => AppConstants::LOG_TOTP_RESET,
      self::LOG_TOTP_CLEAR_TD => AppConstants::LOG_TOTP_CLEAR_TD,
      self::LOG_TOTP_DISABLE_BY_ADMIN => AppConstants::LOG_TOTP_DISABLE_BY_ADMIN,
      self::LOG_TOTP_RESET_BY_ADMIN => AppConstants::LOG_TOTP_RESET_BY_ADMIN,
      self::LOG_TOTP_CLEAR_TD_BY_ADMIN => AppConstants::LOG_TOTP_CLEAR_TD_BY_ADMIN,
      default => $logType
    };

    $logLevel = EventLog::LEVEL_DATA;
    if (in_array($intLogTyp, [AppConstants::LOG_TOTP_DISABLE, AppConstants::LOG_TOTP_RESET, AppConstants::LOG_TOTP_DISABLE_BY_ADMIN, AppConstants::LOG_TOTP_CLEAR_TD_BY_ADMIN])) {
      $logLevel = EventLog::LEVEL_WARN;
    } elseif ($intLogTyp == AppConstants::LOG_TOTP_SHOW_QR) {
      $logLevel = EventLog::LEVEL_INFO;
    }

    $this->eventLog->log($userId, $intLogTyp, ['message' => $text, 'level' => $logLevel]);

    return true;
  }
}
