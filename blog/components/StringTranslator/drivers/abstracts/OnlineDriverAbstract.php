<?php


namespace blog\components\StringTranslator\drivers\abstracts;


/**
 * Class OnlineDriverAbstract
 * @package blog\components\StringTranslator\drivers\abstracts
 */
abstract class OnlineDriverAbstract extends DriverAbstract
{
    protected $to;
    protected $from;
    protected $text;

    /**
     * @return string
     */
    abstract protected function getDriverUrl(): string;

    /**
     * @return string
     */
    protected function getPreparedText(): string
    {
        return preg_replace('~[^\w ]~u', '', parent::getPreparedText());

    }
}