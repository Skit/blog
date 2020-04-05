<?php


namespace blog\components\StringTranslator\servers\interfaces;

/**
 * Interface ServerInterface
 * @package blog\components\StringTranslator\drivers\interfaces
 */
interface ServerInterface
{
    public function sendRequest(): void;

    public function getResponse(): string;
}