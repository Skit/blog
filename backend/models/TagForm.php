<?php

namespace backend\models;

use yii\base\Model;

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
class TagForm extends Model
{
    public $title;
    public $slug;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug'], 'required'],
            [['status'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'title' => 'Title',
            'slug' => 'Slug',
            'status' => 'Status',
        ];
    }
}
