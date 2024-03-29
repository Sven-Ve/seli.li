<?php

namespace App\Tests\Service;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MFATest extends TestCase
{
  public function testUserAddBackUpCode(): void
  {
    $user = new User();
    $this->assertTrue($user->addBackUpCode('123456'));
    $this->assertFalse($user->addBackUpCode('123456'));

    $user->clearBackUpCodes();
    $this->assertTrue($user->addBackUpCode('123456'));
  }

  public function testInvalidateBackupCode(): void
  {
    $user = new User();
    $this->assertTrue($user->addBackUpCode('123456'));
    $user->invalidateBackupCode('123456');
    $this->assertTrue($user->addBackUpCode('123456'));
  }

  public function testIsBackupCode(): void
  {
    $user = new User();
    $this->assertFalse($user->isBackupCode('123456'));

    $this->assertTrue($user->addBackUpCode('123456'));
    $this->assertTrue($user->isBackupCode('123456'));
    $this->assertFalse($user->isBackupCode('111111'));
  }

  public function testTrustedToken(): void
  {
    $user = new User();
    $this->assertEquals(0, $user->getTrustedTokenVersion());

    $user->clearTrustedToken();
    $this->assertEquals(1, $user->getTrustedTokenVersion());
  }

  public function testTOTPEnabled(): void
  {
    $user = new User();
    $user->setTotpSecret('abcdefg');
    $this->assertTrue($user->addBackUpCode('123456'));

    $this->assertTrue($user->enableTotpAuthentication());

    $this->assertTrue($user->isTotpSecret());
    $this->assertTrue($user->isTotpAuthenticationEnabled());
    $this->assertFalse($user->isBackupCode('123456'));
  }

  public function testTOTPDisabled(): void
  {
    $user = new User();
    $user->setTotpSecret('abcdefg');
    $this->assertTrue($user->addBackUpCode('123456'));

    $user->disableTotpAuthentication();

    $this->assertFalse($user->isTotpAuthenticationEnabled());
    $this->assertFalse($user->isBackupCode('123456'));
    $this->assertEquals(0, $user->getTrustedTokenVersion());
  }

  public function testTOTPUsername(): void
  {
    $user = new User();
    $user->setEmail('1@1.com');
    $this->assertEquals('1@1.com', $user->getTotpAuthenticationUsername());
  }
}
