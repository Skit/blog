<?php


namespace blog\components\StringTranslator\drivers;

use blog\components\StringTranslator\drivers\abstracts\OnlineDriverAbstract;
use blog\components\StringTranslator\drivers\classes\Response;
use blog\components\StringTranslator\servers\OnlineServer;

/**
 * Class YandexDriver
 * @package blog\components\StringTranslator\drivers
 */
class YandexDriver extends OnlineDriverAbstract
{
    /**
     * Yandex driver constructor.
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
                        return $response->text[0];
                    },
                    function ($response) {
                        return $response->code !== 200;
                    },
                    function ($response) {
                        return $response->code;
                    },
                    function ($response) {
                        return $response->message;
                    }
                );
            });
    }

    /**
     * @return string
     */
    protected function getDriverUrl(): string
    {
        return 'https://translate.yandex.net/api/v1.5/tr.json/translate?' .
            http_build_query([
                'key' => 'trnsl.1.1.20141127T171151Z.69f1ca25ee747016.63416f07eaa70035f0cc29330a7061a9be78f0a8',
                'text' => $this->getPreparedText(),
                'lang' => "{$this->from}-{$this->to}"
            ]);
    }
}