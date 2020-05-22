<?php


namespace blog\components\ImageResizer\settings;

use blog\components\ImageResizer\interfaces\FormatInterface;

/**
 * Class Jpeg
 * @package blog\components\ImageResizer\settings
 */
class Jpeg implements FormatInterface
{
    private $quality;

    /**
     * Jpeg constructor.
     * @param int $quality
     */
    public function __construct(int $quality)
    {
        $this->quality = $quality;
    }

    /**
     * @return int
     */
    public function getQuality(): int
    {
        return $this->quality;
    }

    public function getExtension(): string
    {
        return 'jpeg';
    }
}