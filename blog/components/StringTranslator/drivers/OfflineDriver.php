<?php


namespace blog\components\StringTranslator\drivers;


use blog\components\StringTranslator\drivers\classes\Response;
use blog\components\StringTranslator\drivers\abstracts\DriverAbstract;
use blog\components\StringTranslator\servers\OfflineServer;

/**
 * Class Offline
 * @package blog\components\onlineTranslator\drivers
 */
class OfflineDriver extends DriverAbstract
{
    /**
     * Offline constructor.
     * @param string $sourceText
     */
    public function __construct(string $sourceText)
    {
        $this->text = $sourceText;
    }

    /**
     * @return null|Response
     */
    public function getResponse(): ?Response
    {
        return $this->execute(new OfflineServer($this->getPreparedText()),
            function ($serverResponse) {
                return new Response(
                    $serverResponse,
                    function ($response) {
                        return $response->text;
                    },
                    function () {
                        return false;
                    }
                );
            }
        );
    }
}