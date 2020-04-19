<?php declare(strict_types=1);

namespace blog\entities\user;

use blog\entities\common\abstracts\BlogRecordAbstract;
use blog\entities\common\abstracts\ContentObjectAbstract;
use blog\entities\common\Date;
use blog\entities\relation\interfaces\AsRelation;
use blog\entities\relation\interfaces\HasRelation;
use blog\entities\relation\traits\AsRelationTrait;
use blog\entities\relation\traits\HasRelationTrait;
use blog\entities\user\exceptions\UserException;
use blog\entities\user\interfaces\UserInterface;

/**
 * Class User
 * @package blog\entities\user
 */
class User extends ContentObjectAbstract implements UserInterface, AsRelation, HasRelation
{
    use AsRelationTrait, HasRelationTrait;

    public const STATUS_NOT_VERIFY = 20;
    public const STATUS_BANNED = 30;
    public const STATUS_DELETED = 40;

    private $username;
    private $email;
    private $role;

    private $profile;

    private $auth_key;
    private $password_hash;
    private $password_reset_token;
    private $verification_token;

    /**
     * @param string $username
     * @param string $email
     * @param string $role
     * @return User
     */
    public static function create(string $username, string $email, string $role): User
    {
        $user = new User;
        $user->username = $username;
        $user->email = $email;
        $user->role = $role;
        $user->status = static::STATUS_INACTIVE;
        $user->created_at = Date::getFormatNow();

        return $user;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $role
     */
    public function edit(string $username, string $email, string $role): void
    {
        $this->username = $username;
        $this->email = $email;
        $this->role = $role;
        $this->updated_at = Date::getFormatNow();
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function passwordHashEquals(string $hash): bool
    {
        return $this->password_hash === $hash;
    }

    /**
     * @throws UserException
     */
    public function activate(): void
    {
        if ($this->status === static::STATUS_ACTIVE) {
            throw new UserException('User is already active');
        }

        if ($this->password_hash === null) {
            throw new UserException('Password hash is required');
        }

        if ($this->auth_key === null) {
            throw new UserException('Auth key is required');
        }

        if ($this->verification_token === null) {
            throw new UserException('Verification token is required');
        }

        $this->status = static::STATUS_ACTIVE;
    }

    /**
     * @throws UserException
     */
    public function deactivate(): void
    {
        if ($this->status === static::STATUS_DELETED) {
            throw new UserException('User is deleted');
        }

        if ($this->status === static::STATUS_INACTIVE) {
            throw new UserException('User is already inactive');
        }

        $this->status = static::STATUS_INACTIVE;
    }

    /**
     * @throws UserException
     */
    public function delete(): void
    {
        if ($this->status === static::STATUS_DELETED) {
            throw new UserException('User is already deleted');
        }

        $this->status = static::STATUS_DELETED;
    }

    /**
     * @throws UserException
     */
    public function ban(): void
    {
        if ($this->status === static::STATUS_BANNED) {
            throw new UserException('User is already banned');
        }

        if ($this->status === static::STATUS_DELETED) {
            throw new UserException('User is deleted');
        }

        $this->status = static::STATUS_BANNED;
    }

    /**
     * @return bool
     */
    public function isNotVerify(): bool
    {
        return (int) $this->status === static::STATUS_NOT_VERIFY;
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return (int) $this->status === static::STATUS_BANNED;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return (int) $this->status === static::STATUS_DELETED;
    }

    /**
     * @param string $hash
     * @throws UserException
     */
    public function setPasswordHash(string $hash): void
    {
        if (strlen($hash) < 32) {
            throw new UserException('Set short password hash');
        }

        $this->password_hash = $hash;
    }

    /**
     * @param string $token
     */
    public function setPasswordResetToken(string $token): void
    {
        $this->password_reset_token = $token;
    }

    /**
     * @param string $key
     * @throws UserException
     */
    public function setAuthKey(string $key): void
    {
        if (strlen($key) < 32) {
            throw new UserException('Set wrong auth key');
        }

        $this->auth_key = $key;
    }

    /**
     * @param string $token
     * @throws UserException
     */
    public function setVerificationToken(string $token): void
    {
        if (strlen($token) < 32) {
            throw new UserException('Set short verification token');
        }

        $this->verification_token = $token;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return Profile|null
     */
    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->setRelationObject($name, $value);
    }
}