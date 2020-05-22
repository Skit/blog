<?php


namespace blog\components\ImageResizer\settings;

/**
 * Class Sharp
 * @package blog\components\ImageResizer\settings
 */
class Sharp
{
     private $radius;
     private $sigma;
     private $amount;
     private $threshold;

    /**
     * Sharp constructor.
     * @param $radius
     * @param $sigma
     * @param $amount
     * @param $threshold
     */
    public function __construct(float $radius, float $sigma, float $amount, float $threshold)
    {
        $this->radius = $radius;
        $this->sigma = $sigma;
        $this->amount = $amount;
        $this->threshold = $threshold;
    }

    /**
     * @return float
     */
    public function getRadius(): float
    {
        return $this->radius;
    }

    /**
     * @return float
     */
    public function getSigma(): float
    {
        return $this->sigma;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getThreshold(): float
    {
        return $this->threshold;
    }
}