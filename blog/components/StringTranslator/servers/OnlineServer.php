<?php


namespace blog\components\StringTranslator\servers;

use blog\components\StringTranslator\servers\abstracts\ServerAbstract;

/**
 * Class OnlineServer
 * @package blog\components\StringTranslator\servers
 */
class OnlineServer extends ServerAbstract
{
    /**
     * @var string $url
     */
    private $url;

    /**
     * OnlineServer constructor.
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return void
     */
    public function sendRequest(): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $this->response = curl_exec($ch);
        curl_close($ch);
    }
}