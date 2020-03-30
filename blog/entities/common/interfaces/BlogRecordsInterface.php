<?php

namespace blog\entities\common\interfaces;

use blog\entities\user\User;

/**
 * Interface BlogRecordsInterface
 * @package blog\entities\common\interfaces
 */
interface BlogRecordsInterface
{
    public function getContent(): ?string;

    public function getCreator(): User;

    public function checkUserToActive(User $user): void;
}