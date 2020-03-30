<?php


namespace blog\entities\common\interfaces;

/**
 * Interface ContentObjectInterface
 * @package blog\entities\common\interfaces
 */
interface ContentObjectInterface
{
    public function getStatus(): int;

    public function getCreatedAt(): string;

    public function getUpdatedAt(): ?string;

    public function getPrimaryKey(): int;

    public function isNew(): bool;

    public function isActive(): bool;

    public function activate(): void;

    public function deactivate(): void;

    public function delete(): void;

    public function setPrimaryKey(int $pk): void;
}