<?php declare(strict_types=1);

namespace blog\entities\user;


use blog\entities\relation\interfaces\AsRelation;
use blog\entities\relation\traits\AsRelationTrait;

/**
 * Class Profile
 * @package blog\entities\user
 */
class Profile implements AsRelation
{
    use AsRelationTrait;

    private $id;
    private $user_id;
    private $bio;
    private $avatar_url;
    private $created_at;
    private $updated_at;

    /**
     * @return string|null
     */
    public function getBio(): ?string
    {
        return $this->bio;
    }

    /**
     * @return string|null
     */
    public function getAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }
}
