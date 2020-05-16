<?php

namespace common\models\active;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "tags".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property int $frequency
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $status
 */
class Tags extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tags';
    }
}
