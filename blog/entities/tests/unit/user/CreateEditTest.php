<?php

use blog\entities\user\User;
use Codeception\Test\Unit;

/**
 * Class CategoryCreateTest
 */
class CreateEditTest extends Unit
{
    private $faker;

    public function setUp(): void
    {
        $this->faker = Faker\Factory::create();
        parent::setUp();
    }

    public function testFullCreateWithActivate()
    {
        $user = User::create('Test', 'u@u.u', 0);
        $user->setPasswordHash($this->faker->sha1);
        $user->setAuthKey($this->faker->uuid);
        $user->setVerificationToken($this->faker->uuid);

        expect($user->isActive())->false();
        expect($user->getEmail())->equals('u@u.u');
        expect($user->getUsername())->equals('Test');
    }

    public function testEdit()
    {
        $user = User::create('Test', 'u@u.u', 0);
        $user->edit('Bob', 'bob@u.u', 1);

        expect($user->getEmail())->equals('bob@u.u');
        expect($user->getUsername())->equals('Bob');
    }
}