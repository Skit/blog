<?php


namespace blog\components\ImageResizer\entities;

/**
 * Class Size
 * @package blog\components\ImageResizer\entities
 */
class Size
{
    private $with;
    private $height;
    private $left;
    private $top;

    /**
     * Size constructor.
     * @param $with
     * @param $height
     * @param $left
     * @param $right
     */
    public function __construct(int $with, int $height, int $left = 0, int $right = 0)
    {
        $this->with = $with;
        $this->height = $height;
        $this->left = $left;
        $this->top = $right;
    }

    /**
     * @return int
     */
    public function getWith(): int
    {
        return $this->with;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getLeft(): int
    {
        return $this->left;
    }

    /**
     * @return int
     */
    public function getTop(): int
    {
        return $this->top;
    }
}