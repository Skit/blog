<?php declare(strict_types=1);

namespace backend\models;

use yii\base\Model;

/**
 * This is the model class for table "categories".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $content
 * @property string $creator_id
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $status
 * TODO переименовать в единственное число
 */
class CategoriesForm extends Model
{
    public $title;
    public $slug;
    public $content;
    public $meta_title;
    public $meta_keywords;
    public $meta_description;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug'], 'required'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['content', 'meta_title', 'meta_description', 'meta_keywords'], 'string'],
            [['status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'content' => 'content',
            'meta_data' => 'Meta Data',
            'creator_id' => 'Creator ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
