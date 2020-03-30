<?php declare(strict_types=1);

namespace common\components;

use yii\db\Command;

class MCommand extends Command
{
    public function fetchObject(string $className)
    {
        return $this->queryInternal('fetchObject', $className);
    }
}