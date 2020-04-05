<?php


namespace blog\components\StringTranslator\drivers\classes;

use Closure;
use stdClass;

/**
 * Class Response
 * @package blog\components\stringTranslator\classes
 */
class Response
{
    /**
     * @var stdClass $response;
     */
    private $response;

    /**
     * @var Closure $checkErrorFunc;
     */
    private $checkErrorFunc;

    /**
     * @var Closure $getTranslatedFunc;
     */
    private $getTranslatedFunc;

    /**
     * @var Closure $getCode;
     */
    private $getCodeFunc;

    /**
     * @var Closure $getMessageFunc;
     */
    private $getMessageFunc;

    /**
     * Response constructor.
     * @param stdClass $response
     * @param Closure $checkErrorFunc
     * @param Closure $getTranslatedFunc
     * @param Closure $getCodeFunc
     * @param Closure $getMessageFunc
     */
    public function __construct(stdClass $response, Closure $getTranslatedFunc, Closure $checkErrorFunc,
                                Closure $getCodeFunc = null, Closure $getMessageFunc = null)
    {
        $this->response = $response;
        $this->checkErrorFunc = $checkErrorFunc;
        $this->getTranslatedFunc = $getTranslatedFunc;
        $this->getCodeFunc = $getCodeFunc;
        $this->getMessageFunc = $getMessageFunc;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->getCodeFunc->__invoke($this->response);
    }

    /**
     * @return string
     */
    public function getTranslated(): string
    {
        return $this->getTranslatedFunc->__invoke($this->response);
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->getMessageFunc->__invoke($this->response);
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->checkErrorFunc->__invoke($this->response);
    }
}