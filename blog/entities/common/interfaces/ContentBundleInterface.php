<?php

namespace blog\entities\common\interfaces;

/**
 * Interface ContentBundleInterface
 * @package blog\entities\common\interfaces
 */
interface ContentBundleInterface
{
    public function getCount(): int;

    public function getBundle(): array;

    public function findByPrimaryKey(int $pk): ?ContentObjectInterface;

    public function removeByPrimaryKey(int $pk): ?bool;
}