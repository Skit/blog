<?php


namespace blog\components\StringTranslator\servers;

use blog\components\StringTranslator\servers\abstracts\ServerAbstract;

/**
 * Class OfflineServer
 * @package blog\components\StringTranslator\servers
 */
class OfflineServer extends ServerAbstract
{
    /**
     * @var string $term
     */
    private $term;

    /**
     * OfflineServer constructor.
     * @param string $term
     */
    public function __construct(string $term)
    {
       $this->term = $term;
    }

    /**
     * @return void
     */
    public function sendRequest(): void
    {
        $this->response = json_encode([
            'text' =>  strtr($this->term, [
                'а' => 'a', 'б' => 'b', 'в' => 'v',
                'г' => 'g', 'д' => 'd', 'е' => 'e',
                'ё' => 'e', 'ж' => 'j', 'з' => 'z',
                'и' => 'i', 'й' => 'y', 'к' => 'k',
                'л' => 'l', 'м' => 'm', 'н' => 'n',
                'о' => 'o', 'п' => 'p', 'р' => 'r',
                'с' => 's', 'т' => 't', 'у' => 'u',
                'ф' => 'f', 'х' => 'h', 'ц' => 'c',
                'ш' => 'sh', 'щ' => 'sh', 'ь' => '',
                'ы' => 'i', 'ъ' => '', 'э' => 'e',
                'ю' => 'yu', 'я' => 'ya',
                'А' => 'A', 'Б' => 'B', 'В' => 'V',
                'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
                'Ё' => 'E', 'Ж' => 'J', 'З' => 'Z',
                'И' => 'I', 'Й' => 'Y', 'К' => 'K',
                'Л' => 'L', 'М' => 'M', 'Н' => 'N',
                'О' => 'O', 'П' => 'P', 'Р' => 'R',
                'С' => 'S', 'Т' => 'T', 'У' => 'U',
                'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
                'Ш' => 'SH', 'Щ' => 'SH', 'Ь' => '',
                'Ы' => 'I', 'Ъ' => '', 'Э' => 'E',
                'Ю' => 'YU', 'Я' => 'YA',
            ])
        ]);
    }
}