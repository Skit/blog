<?php declare(strict_types=1);

// TODO переименовать папку в AR или ActiveRecordModel or something
namespace common\models\active;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $creator_id
 * @property string $created_at
 * @property string $meta_data
 * @property string|null $updated_at
 * @property int $status
 */
class CommentsAR extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comments';
    }
}
