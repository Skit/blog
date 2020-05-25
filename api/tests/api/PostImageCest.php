<?php


namespace api\tests\api;

use api\tests\ApiTester;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\PostFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;

/**
 * Class PostImageTest
 * @package api\tests\api
 */
class PostImageCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir('user.php')
            ],
            'profile' => [
                'class' => UserProfilesFixture::class,
                'dataFile' => codecept_data_dir('profile.php')
            ],
            'category' => [
                'class' => ActiveCategoriesFixture::class,
                'dataFile' => codecept_data_dir('activeCategories.php')
            ],
            'posts' => [
                'class' => PostFixture::class,
                'dataFile' => codecept_data_dir('posts.php')
            ],
        ]);
    }

    public function index(ApiTester $I)
    {
        $I->amBearerAuthenticated('HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR');
        $I->sendGET('api/post-images?postId=f0d5b176-a8e3-40be-a78d-fc6bee14f2d6');

        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('testim');
        $I->seeResponseContainsJson([
            'resized' => [],
            'original' => [],
        ]);
    }

    public function incorrectToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('some-fail-token');
        $I->sendGET('api/post-images?postId=f0d5b176');

        $I->seeResponseCodeIs(401);
        $I->seeResponseContains('Unauthorized');
    }
}