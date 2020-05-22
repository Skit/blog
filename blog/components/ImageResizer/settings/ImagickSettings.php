<?php


namespace blog\components\ImageResizer\settings;

use blog\components\ImageResizer\interfaces\FormatInterface;
use blog\components\ImageResizer\interfaces\ImageResizerSettingsInterface;

/**
 * Class ImagickSettings
 * @package blog\components\ImageResizer\settings
 */
class ImagickSettings implements ImageResizerSettingsInterface
{
    private $format;
    private $resize;
    private $sharp;
    private $modulate;

    /**
     * ImagickSettings constructor.
     * @param FormatInterface $format
     * @param Resize $resize
     * @param Sharp $sharp
     * @param Modulate $modulate
     */
    public function __construct(FormatInterface $format, Resize $resize, Sharp $sharp, Modulate $modulate)
    {
        $this->sharp = $sharp;
        $this->format = $format;
        $this->resize = $resize;
        $this->modulate = $modulate;
    }

    /**
     * @return Sharp
     */
    public function getSharp(): Sharp
    {
        return $this->sharp;
    }

    /**
     * @return FormatInterface
     */
    public function getFormat(): FormatInterface
    {
        return $this->format;
    }

    /**
     * @return Resize
     */
    public function getResize(): Resize
    {
        return $this->resize;
    }

    /**
     * @return Modulate
     */
    public function getModulate(): Modulate
    {
        return $this->modulate;
    }
}