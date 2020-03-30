<?php declare(strict_types=1);

namespace blog\entities\common;

use blog\entities\common\exceptions\MetaDataExceptions;
use Codeception\Util\HttpCode;
use Exception;
use JsonSerializable;

/**
 * TODO написать тесты
 *
 * Class MetaData
 * @package blog\entities\metaData
 */
class MetaData implements JsonSerializable
{
    private $title;
    private $description;
    private $keywords;

    /**
     * MetaData constructor.
     * @param string|null $title
     * @param string|null $description
     * @param string|null $keywords
     */
    public function __construct(?string $title = null, ?string $description = null, ?string $keywords = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
    }

    /**
     * @param string|null $json
     * @return $this
     * @throws MetaDataExceptions
     */
    public static function fillByJson(?string $json): self
    {
        if (!empty($json = json_decode($json, true))) {
            try {
                return new self($json['title'], $json['description'], $json['keywords']);
            } catch (Exception $e) {
                throw new MetaDataExceptions('Fail to set data from json', HttpCode::INTERNAL_SERVER_ERROR, $e);
            }
        }

        return new self();
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getKeywords(): ?string
    {
        return $this->keywords;
    }


    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'keywords' => $this->getKeywords()
        ];
    }
}