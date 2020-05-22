<?php


namespace blog\components\ImageResizer\settings;

/**
 * Class Modulate
 * @package blog\components\ImageResizer\settings
 */
class Modulate
{
    private $brightness;
    private $saturation;
    private $hue;

    /**
     * Modulate constructor.
     * @param $brightness
     * @param $saturation
     * @param $hue
     */
    public function __construct($brightness, $saturation, $hue)
    {
        $this->brightness = $brightness;
        $this->saturation = $saturation;
        $this->hue = $hue;
    }

    /**
     * @return mixed
     */
    public function getBrightness()
    {
        return $this->brightness;
    }

    /**
     * @return mixed
     */
    public function getSaturation()
    {
        return $this->saturation;
    }

    /**
     * @return mixed
     */
    public function getHue()
    {
        return $this->hue;
    }
}