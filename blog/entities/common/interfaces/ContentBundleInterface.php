<?php

namespace blog\entities\common\interfaces;

/**
 * Interface ContentBundleInterface
 * @package blog\entities\common\interfaces
 */
interface ContentBundleInterface
{
    public function append($item): void;

    public function getCount(): int;

    public function getBundle(): array;

    public function getFieldsString(string $field, string $quote = '"', string $delimiter = ','): string;
}