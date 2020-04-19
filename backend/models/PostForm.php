<?php

namespace backend\models;

use yii\base\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $preview
 * @property string|null $content
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $image_url
 * @property string|null $video_url
 * @property int $category_id
 * @property int $creator_id
 * @property string $created_at
 * @property string|null $published_at
 * @property string|null $updated_at
 * @property int $is_highlight
 * @property int $status
 * @property int|null $count_view
 */
class PostForm extends Model
{
    public $id;
    public $title;
    public $slug;
    public $preview;
    public $content;
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    public $image_url;
    public $video_url;
    public $category_id;
    public $creator_id;
    public $created_at;
    public $published_at;
    public $updated_at;
    public $is_highlight;
    public $status;
    public $count_view;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug', 'category_id'], 'required'],
            [['content'], 'string'],
            [['meta_data', 'post_banners', 'published_at'], 'safe'],
            [['category_id', 'is_highlight', 'status'], 'integer'],
            [['title', 'slug'], 'string', 'max' => 255],
            [['preview'], 'string', 'max' => 500],
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
            'preview' => 'Preview',
            'content' => 'Content',
            'meta_data' => 'Meta Data',
            'post_banners' => 'Post Banners',
            'category_id' => 'Category ID',
            'creator_id' => 'Creator ID',
            'created_at' => 'Created At',
            'published_at' => 'Published At',
            'updated_at' => 'Updated At',
            'is_highlight' => 'Is Highlight',
            'status' => 'Status',
            'count_view' => 'Count View',
        ];
    }
}
