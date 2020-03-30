<?php

namespace blog\entities\tests\unit\comment;

use blog\entities\user\User;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * Class Base
 * @package blog\entities\tests\unit\comment
 */
class Base extends Unit
{
    protected $activeUser;

    public function setUp(): void
    {
        $this->activeUser = Stub::make(User::class, ['isActive' => true]);
        parent::setUp();
    }
}