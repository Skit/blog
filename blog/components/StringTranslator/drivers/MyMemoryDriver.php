<?php


namespace blog\components\StringTranslator\drivers;


use blog\components\StringTranslator\drivers\abstracts\OnlineDriverAbstract;
use blog\components\StringTranslator\drivers\classes\Response;
use blog\components\StringTranslator\servers\OnlineServer;

/**
 * Class MyMemoryDriver
 * @package blog\components\StringTranslator\drivers
 */
class MyMemoryDriver extends OnlineDriverAbstract
{
    /**
     * MyMemory constructor.
     * @param string $sourceText
     * @param string $from
     * @param string $to
     */
    public function __construct(string $sourceText, string $from = 'ru', string $to = 'en')
    {
        $this->to = $to;
        $this->from = $from;
        $this->text = $sourceText;
    }

    /**
     * @return null|Response
     */
    public function getResponse(): ?Response
    {
        return $this->execute(new OnlineServer($this->getDriverUrl()),
            function ($serverResponse) {
                return new Response(
                    $serverResponse,
                    function ($response) {
                        return $response->responseData->translatedText;
                    },
                    function ($response) {
                        return $response->responseStatus !== 200;
                    },
                    function ($response) {
                        return $response->responseStatus;
                    },
                    function ($response) {
                        return $response->responseDetails;
                    }
                );
            }
        );
    }

    /**
     * @return string
     */
    protected function getDriverUrl(): string
    {
        return 'http://mymemory.translated.net/api/get?' .
            http_build_query([
                'q' => $this->getPreparedText(),
                'langpair' => "{$this->from}|{$this->to}"
            ]);
    }
}