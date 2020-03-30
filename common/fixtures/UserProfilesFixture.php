<?php

namespace common\fixtures;


use common\models\active\UserProfiles;
use yii\test\ActiveFixture;

/**
 * Class ProfileFixture
 * @package common\fixtures
 */
class UserProfilesFixture extends ActiveFixture
{
    // TODO перенести в папку с тестами ака test/activeRecords/User
    public $modelClass = UserProfiles::class;
}
