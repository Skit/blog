<?php

namespace blog\entities\tests\unit\comment;

use blog\entities\post\Post;
use blog\entities\user\User;
use Codeception\Specify;
use Codeception\Stub;
use Codeception\Test\Unit;

/**
 * Class Base
 *
 * @property User $activeUser
 * @property User $inactiveUser
 * @property Post $activePost
 * @property Post $inactivePost
 *
 * @package blog\entities\tests\unit\comment
 */
class Base extends Unit
{
    use Specify;

    protected $activeUser;
    protected $inactiveUser;
    protected $activePost;
    protected $inactivePost;

    public function setUp(): void
    {
        $this->activeUser = Stub::make(User::class, ['status' => User::STATUS_ACTIVE]);
        $this->inactiveUser = Stub::make(User::class, ['status' => User::STATUS_INACTIVE]);

        $this->activePost = Stub::make(Post::class, ['status' => Post::STATUS_ACTIVE]);
        $this->inactivePost = Stub::make(Post::class, ['status' => Post::STATUS_INACTIVE]);

        parent::setUp();
    }
}