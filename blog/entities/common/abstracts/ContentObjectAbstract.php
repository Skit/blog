<?php declare(strict_types=1);

namespace blog\entities\common\abstracts;


use blog\entities\common\exceptions\BlogRecordsException;
use blog\entities\common\interfaces\ContentObjectInterface;

/**
 * Class ContentObjectAbstract
 * @package blog\entities\common\abstracts
 */
abstract class ContentObjectAbstract implements ContentObjectInterface
{
    public const STATUS_DELETED = 0;
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;

    /* @var $id integer */
    protected $id;
    /* @var $status integer */
    protected $status;
    /* @var $created_at string */
    protected $created_at;
    /* @var $updated_at string */
    protected $updated_at;

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @return null|string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getPrimaryKey() === null;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getStatus() === static::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getStatus() === static::STATUS_DELETED;
    }

    /**
     * @throws BlogRecordsException
     */
    public function activate(): void
    {
        if ($this->getStatus() === static::STATUS_ACTIVE) {
            throw new BlogRecordsException('Record is already active');
        }

        $this->status = static::STATUS_ACTIVE;
    }

    /**
     * @throws BlogRecordsException
     */
    public function deactivate(): void
    {
        if ($this->getStatus() === static::STATUS_INACTIVE) {
            throw new BlogRecordsException('Record is already inactive');
        }

        $this->status = static::STATUS_INACTIVE;
    }

    /**
     * @throws BlogRecordsException
     */
    public function delete(): void
    {
        if ($this->status === static::STATUS_DELETED) {
            throw new BlogRecordsException('Record is already deleted');
        }

        $this->status = static::STATUS_DELETED;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return (int) $this->status;
    }

    /**
     * TODO переделать. 0 это есть значение, null его нет
     * @return int
     */
    public function getPrimaryKey(): int
    {
        return (int) $this->id;
    }

    /**
     * @param int $pk
     */
    public function setPrimaryKey(int $pk): void
    {
        $this->id = $pk;
    }
}