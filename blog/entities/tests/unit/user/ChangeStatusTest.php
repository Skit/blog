<?php

use blog\entities\user\User;
use Codeception\Stub;
use Codeception\Test\Unit;

class ChangeStatusTest extends Unit
{
    private $faker;

    public function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        parent::setUp();
    }

    public function testActivateWithoutPasswordHash()
    {
        $user = User::create('Test', 'u@u.u', 0);

        $this->expectExceptionMessage('Password hash is required');
        $user->activate();
    }

    public function testActivateWithoutAuthKey()
    {
        $user = User::create('Test', 'u@u.u', 0);
        $user->setPasswordHash($this->faker->sha1);

        $this->expectExceptionMessage('Auth key is required');
        $user->activate();
    }

    public function testActivateWithoutVerificationToken()
    {
        $user = User::create('Test', 'u@u.u', 0);
        $user->setPasswordHash($this->faker->sha1);
        $user->setAuthKey($this->faker->uuid);

        $this->expectExceptionMessage('Verification token is required');
        $user->activate();
    }

    public function testActivateAlreadyActive()
    {
        $user = User::create('Test', 'u@u.u', 0);
        $user->setPasswordHash($this->faker->sha1);
        $user->setAuthKey($this->faker->uuid);
        $user->setVerificationToken($this->faker->uuid);
        $user->activate();

        $this->expectExceptionMessage('User is already active');
        $user->activate();
    }

    public function testActivateSuccess()
    {
        $user = User::create('Test', 'u@u.u', 0);
        $user->setPasswordHash($this->faker->sha1);
        $user->setAuthKey($this->faker->uuid);
        $user->setVerificationToken($this->faker->uuid);
        $user->activate();

        expect($user->isActive())->true();
    }

    public function testSuccessDeactivate()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_ACTIVE]);
        $user->deactivate();

        expect($user->isActive())->false();
    }

    public function testDeactivateDeleted()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_DELETED]);

        $this->expectExceptionMessage('User is deleted');
        $user->deactivate();
    }

    public function testDeactivateDeactivated()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_INACTIVE]);

        $this->expectExceptionMessage('User is already inactive');
        $user->deactivate();
    }

    public function testSuccessBan()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_ACTIVE]);
        $user->ban();

        expect($user->isActive())->false();
    }

    public function testBanBanned()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_BANNED]);

        $this->expectExceptionMessage('User is already banned');
        $user->ban();
    }

    public function testSuccessDelete()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_ACTIVE]);
        $user->delete();

        expect($user->isActive())->false();
        expect($user->isDeleted())->true();
    }

    public function testDeleteDeleted()
    {
        /* @var $user User */
        $user = Stub::make(User::class, ['status' => User::STATUS_DELETED]);

        $this->expectExceptionMessage('User is already deleted');
        $user->delete();
    }
}