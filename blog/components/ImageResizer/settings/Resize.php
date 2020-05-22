<?php


namespace blog\components\ImageResizer\settings;

/**
 * Class Resize
 * @package blog\components\ImageResizer\settings
 */
class Resize
{
    private $blur;
    private $bestfit;
    private $filter;

    /**
     * Resize constructor.
     * @param $blur
     * @param $bestfit
     * @param $filter
     */
    public function __construct(float $blur, bool $bestfit, int $filter)
    {
        $this->blur = $blur;
        $this->bestfit = $bestfit;
        $this->filter = $filter;
    }

    /**
     * @return mixed
     */
    public function getBlur()
    {
        return $this->blur;
    }

    /**
     * @return mixed
     */
    public function getBestfit()
    {
        return $this->bestfit;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }
}