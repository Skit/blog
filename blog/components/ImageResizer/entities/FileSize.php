<?php


namespace blog\components\ImageResizer\entities;

/**
 * Class FileSize
 * @package blog\components\ImageResizer\entities
 */
class FileSize
{
    public $bytes = 0;
    public $kilobytes = 0;
    public $megaBytes = 0;

    /**
     * FileSize constructor.
     * @param int $bytes
     * @param int $precision
     */
    public function __construct(int $bytes, int $precision = 2)
    {
        $this->bytes = $bytes;
        $this->kilobytes = round($bytes / 1024, $precision);
        $this->megaBytes = round($this->kilobytes / 1024, $precision);
    }

    /**
     * @return string
     */
    public function getActual(): string
    {
        if ($this->kilobytes > 0) {
            return "{$this->kilobytes} KB";
        } elseif ($this->megaBytes > 0) {
            return "{$this->megaBytes} MB";
        }

        return "{$this->bytes} B";
    }
}