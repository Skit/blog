<?php


namespace blog\components\StringTranslator;


use blog\components\StringTranslator\drivers\interfaces\StringTranslateDriverInterface;
use blog\components\StringTranslator\exceptions\StringTranslatorException;

/**
 * Class StringTranslator
 * @package blog\components\stringTranslator
 */
class StringTranslator
{
    /**
     * @param StringTranslateDriverInterface $driver
     * @return string
     * @throws StringTranslatorException
     */
    public static function translate(StringTranslateDriverInterface $driver): string
    {
        if (!$response = $driver->getResponse()) {
            return '';
        }

        if ($response->hasError()) {
            throw new StringTranslatorException("Unable to translate: {$response->getMessage()}", $response->getCode());
        }

        return $response->getTranslated();
    }
}


