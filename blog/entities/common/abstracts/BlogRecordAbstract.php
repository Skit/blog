<?php declare(strict_types=1);

namespace blog\entities\common\abstracts;


use blog\entities\common\exceptions\BlogRecordsException;
use blog\entities\common\interfaces\BlogRecordsInterface;
use blog\entities\user\User;

/**
 * Class BlogRecordAbstract
 * @package blog\entities\common\abstracts
 */
abstract class BlogRecordAbstract extends ContentObjectAbstract implements BlogRecordsInterface
{
    /* @var $user User */
    protected $user;
    /* @var $creator_id int */
    protected $creator_id;
    /* @var $content string */
    protected $content;

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return User|null
     */
    public function getCreator(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @throws BlogRecordsException
     */
    public function checkUserToActive(User $user): void
    {
        if (!$user->isActive()) {
            throw new BlogRecordsException('User must be active for this operation');
        }
    }
}