<?php

namespace common\fixtures;

use common\models\active\CategoriesActive;
use yii\test\ActiveFixture;

/**
 * Class CategoriesFixture
 * @package common\fixtures
 */
class ActiveCategoriesFixture extends ActiveFixture
{
    public $modelClass = CategoriesActive::class;
}