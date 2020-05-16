<?php

namespace common\models\active;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "post_tag".
 *
 * @property int $tag_id
 * @property int $post_id
 */
class PostTag extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post_tag';
    }
}
