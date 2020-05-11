<?php

namespace common\models\active;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "posts".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $preview
 * @property string|null $content
 * @property string|null $meta_data
 * @property string|null $post_banners
 * @property int $category_id
 * @property int $creator_id
 * @property string $created_at
 * @property string|null $published_at
 * @property string|null $updated_at
 * @property int $is_highlight
 * @property int $status
 * @property int|null $count_view
 */
class Post extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'posts';
    }
}
