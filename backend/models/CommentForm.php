<?php

namespace backend\models;

use yii\base\Model;

/**
 * This is the model class for table "comments".
 *
 * @property int $id
 * @property string|null $content
 * @property int $creator_id
 * @property int $parent_id
 * @property string $created_at
 * @property string|null $updated_at
 * @property int $status
 */
class CommentForm extends Model
{
    public $content;
    public $creator_id;
    public $parent_id;
    public $status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['creator_id', 'content'], 'required'],
            [['creator_id', 'status', 'parent_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'Content',
            'creator_id' => 'Creator ID',
            'parent_id' => 'Parent ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
