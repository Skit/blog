<?php

namespace common\models\active;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_profiles".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $bio
 * @property string|null $avatar_url
 * @property string $created_at
 * @property string|null $updated_at
 */
class UserProfiles extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profiles';
    }
}
