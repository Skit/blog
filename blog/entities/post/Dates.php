<?php


namespace blog\entities\post;

use blog\entities\common\Date;
use Exception;

/**
 * Class Dates
 * @package blog\entities\post
 */
class Dates
{
    private $date;
    private $createdAt;
    private $updatedAt;

    /**
     * Dates constructor.
     * @param string|null $createdAt
     * @param string|null $updatedAt
     * @throws Exception
     */
    public function __construct(?string $createdAt = null, ?string $updatedAt = null)
    {
        $this->date = new Date();
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function asNew()
    {
        $this->createdAt = $this->date->getFormatted();

        return $this;
    }

    public function asEdit()
    {
        $this->updatedAt = $this->date->getFormatted();

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}