<?php


namespace blog\components\StringTranslator\drivers\abstracts;

use blog\components\StringTranslator\drivers\classes\Response;
use blog\components\StringTranslator\drivers\interfaces\StringTranslateDriverInterface;
use blog\components\StringTranslator\servers\interfaces\ServerInterface;
use Closure;
use stdClass;

/**
 * Class DriverAbstract
 * @package blog\components\StringTranslator\drivers\abstracts
 */
abstract class DriverAbstract implements StringTranslateDriverInterface
{
    protected $text;
    protected $connect;

    /**
     * @param ServerInterface $server
     * @param Closure $closure
     * @return Response|null
     */
    protected function execute(ServerInterface $server, Closure $closure): ?Response
    {
        if ($this->isValidText()) {
            $server->sendRequest();

            if ($serverResponse = $this->getServerResponse($server)) {
                $result = $closure->__invoke($serverResponse);
            }
        }

        return $result ?? null;
    }

    /**
     * @param ServerInterface $server
     * @return stdClass|null
     */
    protected function getServerResponse(ServerInterface $server): ?stdClass
    {
        return json_decode($server->getResponse());
    }

    /**
     * @return string
     */
    protected function getPreparedText(): string
    {
        return trim($this->text);
    }

    /**
     * @return bool
     */
    protected function isValidText(): bool
    {
        return !empty($this->getPreparedText());
    }
}