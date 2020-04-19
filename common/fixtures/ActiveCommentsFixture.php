<?php

namespace common\fixtures;

use common\models\active\CommentsAR;
use yii\test\ActiveFixture;

/**
 * Class CategoriesFixture
 * @package common\fixtures
 */
class ActiveCommentsFixture extends ActiveFixture
{
    public $modelClass = CommentsAR::class;
}