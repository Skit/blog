<?php


namespace common\fixtures;


use common\models\active\Post;
use yii\test\ActiveFixture;

/**
 * Class PostFixture
 * @package common\fixtures
 */
class PostFixture extends ActiveFixture
{
    public $modelClass = Post::class;
}