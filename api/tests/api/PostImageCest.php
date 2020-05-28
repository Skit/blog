<?php


namespace api\tests\api;

use api\tests\ApiTester;
use blog\components\PathReplacer\PathReplacer;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\PostFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;
use Yii;
use yii\helpers\FileHelper;

/**
 * Class PostImageTest
 * @property PathReplacer $replacer
 * @package api\tests\api
 */
class PostImageCest
{
    private $replacer;

    public function _before(ApiTester $I)
    {
        $this->replacer = Yii::$container->get(PathReplacer::class);

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

    public function _after()
    {
        FileHelper::removeDirectory($this->replacer->replace('front:{uploads}')->getPath());
    }

    public function index(ApiTester $I)
    {
        $I->amBearerAuthenticated('HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR');
        $I->sendGET('api/post-images?postUuid=f0d5b176-a8e3-40be-a78d-fc6bee14f2d6');

        $I->seeResponseCodeIs(200);
        $I->canSeeResponseJsonMatchesJsonPath('$.resized');
        $I->canSeeResponseJsonMatchesJsonPath('$.original');
    }

    public function resize(ApiTester $I)
    {
        $I->amBearerAuthenticated('HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR');
        $I->sendPOST('api/post-images', [
            'postUuid' => 'f0d5b176-a8e3-40be-a78d-fc6bee14f2d6',
            'with' => 200,
            'height' => 300
        ], ['image' => codecept_data_dir('landscape400x225.jpg')]);

        $I->seeResponseCodeIs(200);
        $I->canSeeResponseContains('400x225.jpg');
        $I->canSeeResponseJsonMatchesJsonPath('$.resized');
        $I->canSeeResponseJsonMatchesJsonPath('$.original');
        $I->canSeeResponseJsonMatchesJsonPath('$._links');
    }

    public function delete(ApiTester $I)
    {
        $I->amBearerAuthenticated('HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR');
        $I->sendPOST('api/post-images', [
            'postUuid' => 'f0d5b176-a8e3-40be-a78d-fc6bee14f2d6',
            'with' => 200,
            'height' => 300
        ], ['image' => codecept_data_dir('landscape400x225.jpg')]);

       $resized = json_decode($I->grabResponse())->resized->relative;

        $I->amBearerAuthenticated('HP187Mvq7Mmm3CTU80dLkGmni_FUH_lR');
        $I->sendDELETE("api/post-images?path={$resized}&postUuid=f0d5b176-a8e3-40be-a78d-fc6bee14f2d6");
        $I->canSeeResponseJsonMatchesJsonPath('$.resized');
    }

    public function incorrectToken(ApiTester $I)
    {
        $I->amBearerAuthenticated('some-fail-token');
        $I->sendGET('api/post-images?postUuid=f0d5b176');

        $I->seeResponseCodeIs(401);
        $I->seeResponseContains('Unauthorized');
    }
}