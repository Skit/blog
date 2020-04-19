<?php declare(strict_types=1);

namespace common\components;


use blog\entities\relation\RelationSql;
use yii\db\Connection;

/**
 * Class MConnection
 * @package common\components
 */
class MConnection extends Connection
{
    /**
     * @param RelationSql $relationSql
     * @param array $params
     * @return \yii\db\Command
     */
    public function createCommandWithRelation(RelationSql $relationSql, array $params = [])
    {
        return parent::createCommand($relationSql->getSql(), $params);
    }
}