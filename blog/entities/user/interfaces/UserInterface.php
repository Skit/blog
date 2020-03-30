<?php


namespace blog\entities\user\interfaces;

use blog\entities\common\interfaces\ContentObjectInterface;

interface UserInterface extends ContentObjectInterface
{
    public function passwordHashEquals(string $hash): bool;

    public function setPasswordHash(string $hash): void;

    public function setPasswordResetToken(string $token): void;

    public function setVerificationToken(string $token): void;

    public function setAuthKey(string $key): void;

    public function isNotVerify(): bool;

    public function isDeleted(): bool;

    public function isBanned(): bool;

    public function delete(): void;
}