<?php


namespace blog\repositories\interfaces;


use blog\entities\common\interfaces\ContentObjectInterface;

/**
 * Interface CreateRepository
 * @package blog\repositories\interfaces
 */
interface CRUDRepositoryInterface
{
    public function create(ContentObjectInterface $object): ContentObjectInterface;

    public function update(ContentObjectInterface $object): ContentObjectInterface;

    public function findOneById(int $id, int $status): ?ContentObjectInterface;
}