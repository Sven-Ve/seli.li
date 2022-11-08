<?php

namespace App\Exception;

class SuperAdminDeleteForbiddenException extends \Exception
{
  protected $message = 'Deleting SuperAdmins is not allowed';
}
