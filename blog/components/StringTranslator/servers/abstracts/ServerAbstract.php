<?php


namespace blog\components\StringTranslator\servers\abstracts;


use blog\components\StringTranslator\servers\interfaces\ServerInterface;
use stdClass;

/**
 * Class ServerAbstract
 * @package blog\components\StringTranslator\servers\abstracts
 */
abstract class ServerAbstract implements ServerInterface
{
    /**
     * @var stdClass $response
     */
    protected $response;

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    abstract public function sendRequest(): void;
}