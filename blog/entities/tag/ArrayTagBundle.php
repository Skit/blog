<?php


namespace blog\entities\tag;


use blog\components\StringTranslator\drivers\OfflineDriver;
use blog\components\StringTranslator\StringTranslator;
use blog\entities\common\abstracts\bundles\ArrayBundle;
use blog\entities\tag\exceptions\TagException;
use Exception;

/**
 * Class TagArrayBundle
 * @package blog\entities\tag
 */
class ArrayTagBundle extends ArrayBundle
{
    /**
     * TagArrayBundle constructor.
     * @param string $tags
     * @param int $status
     * @param string $delimiter
     * @throws TagException
     */
    public function __construct(string $tags, int $status, string $delimiter = ',')
    {
        try {
            $tags = explode($delimiter, $tags);
            $this->createBundle($tags, function ($tag) use ($status) {
                return [
                    'title' => $title = trim($tag),
                    'slug' => StringTranslator::translate(new OfflineDriver($title)),
                    'status' => $status
                ];
            });
        } catch (Exception $e) {
            throw new TagException("Fail to create tag bundle: {$e->getMessage()}", 0, $e);
        }
    }
}