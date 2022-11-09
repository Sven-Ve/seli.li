<?php

namespace App\Service;

/**
 * constants for the hole application.
 */
class AppConstants
{
  final public const LOG_TYPE_LINK_CALLED = 1;
  final public const LOG_TYPE_LINK_CREATED = 2;
  final public const LOG_TYPE_LINK_CHANGED = 3;
  final public const LOG_TYPE_LINK_DELETED = 4;
  final public const LOG_TYPE_ANONHOME = 9; // anonymer user on homepage

  // user, logon, registration
  final public const LOG_TYPE_LOGIN = 10;
  final public const LOG_TYPE_LOGIN_FAILED = 11;
  final public const LOG_TYPE_REGISTER = 12;

  final public const LOG_TYPE_IMPERSONATION_START = 40;
  final public const LOG_TYPE_IMPERSONATION_END = 41;
  final public const LOG_TYPE_IMPERSONATION_ERROR = 42;

  // for SvcTotpBundle - 2FA
  final public const LOG_TOTP_SHOW_QR = 81;
  final public const LOG_TOTP_ENABLE = 82;
  final public const LOG_TOTP_DISABLE = 83;
  final public const LOG_TOTP_RESET = 84;
  final public const LOG_TOTP_CLEAR_TD = 85;
  final public const LOG_TOTP_DISABLE_BY_ADMIN = 86;
  final public const LOG_TOTP_RESET_BY_ADMIN = 87;
  final public const LOG_TOTP_CLEAR_TD_BY_ADMIN = 88;

  final public const LOG_ACCOUNT_OPERATIONS = [
    self::LOG_TYPE_LOGIN,
    self::LOG_TYPE_LOGIN_FAILED,
    self::LOG_TYPE_REGISTER,
    self::LOG_TYPE_IMPERSONATION_START,
    self::LOG_TYPE_IMPERSONATION_END,
    self::LOG_TYPE_IMPERSONATION_ERROR,
    self::LOG_TOTP_SHOW_QR,
    self::LOG_TOTP_ENABLE,
    self::LOG_TOTP_DISABLE,
    self::LOG_TOTP_RESET,
    self::LOG_TOTP_CLEAR_TD,
    self::LOG_TOTP_DISABLE_BY_ADMIN,
    self::LOG_TOTP_RESET_BY_ADMIN,
    self::LOG_TOTP_CLEAR_TD_BY_ADMIN,
  ];

  // Parameter
  final public const PARAM_REGISTER_ENABLED = 'registrationEnabled';
}
