<?php
namespace common\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    // TODO перенести в папку с тестами ака test/activeRecords/User
    public $modelClass = 'common\models\User';
}