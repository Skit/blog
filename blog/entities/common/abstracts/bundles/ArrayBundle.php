<?php


namespace blog\entities\common\abstracts\bundles;


/**
 * Class ArrayBundle
 * @package blog\entities\common\abstracts\bundles
 */
class ArrayBundle extends ContentBundleAbstract
{
    /**
     * @param string $field
     * @param string $quote
     * @param string $delimiter
     * @return string
     */
    public function getFieldsString(string $field, string $quote = '', string $delimiter = ','): string
    {
        $result = array_column($this->getBundle(), $field);

        if ($quote) {
            $result = array_map(function ($item) use ($quote) {
                return "{$quote}{$item}{$quote}";
            }, $result);
        }

        return implode($delimiter, $result);
    }
}