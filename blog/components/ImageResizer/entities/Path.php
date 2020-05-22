<?php


namespace blog\components\ImageResizer\entities;


use blog\components\ImageResizer\exceptions\ImageResizerException;
use Stringable;

/**
 * Class Path
 * @package blog\components\ImageResizer\entities
 */
class Path implements Stringable
{
    private $path;

    /**
     * Path constructor.
     * @param string $path
     * @param bool $throw
     * @throws ImageResizerException
     */
    public function __construct(string $path, bool $throw = false)
    {
        if ($throw && !file_exists($path)) {
            throw new ImageResizerException("Doesnt exist path: {$path}");
        }

        $this->path = $path;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->path;
    }

    /**
     * @param bool $make
     * @param int $mode
     * @param bool $recursive
     * @return $this
     * @throws ImageResizerException
     */
    public function create(bool $make = true, int $mode = 0755, bool $recursive = true): self
    {
        if ($make && !is_dir($this->getDirname())) {
            if (!mkdir($this->getDirname(), $mode, $recursive)) {
                throw new ImageResizerException("Failed to create directory \"{$this}\": ");
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDirname(): string
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->path;
    }
}