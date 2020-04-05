<?php


namespace blog\components\StringTranslator\drivers\interfaces;


use blog\components\StringTranslator\drivers\classes\Response;

/**
 * Interface StringTranslateDriverInterface
 * @package blog\components\StringTranslator\drivers\interfaces
 */
interface StringTranslateDriverInterface
{
    public function getResponse(): ?Response;
}