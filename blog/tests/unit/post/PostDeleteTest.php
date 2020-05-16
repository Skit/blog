<?php


namespace blog\tests\unit\post;


use blog\repositories\post\PostRepository;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\PostFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;
use Yii;

/**
 * Class PostDelete
 * @property PostRepository $repo
 *
 * @package blog\tests\unit\post
 */
class PostDeleteTest extends Unit
{
    protected $tester;
    private $repo;

    protected function _before()
    {
        $this->repo = Yii::$container->get(PostRepository::class);

        $this->tester->haveFixtures([
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
            'post' => [
                'class' => PostFixture::class,
                'dataFile' => codecept_data_dir('post.php')
            ],
        ]);
    }

    public function testDelete()
    {
        $post = $this->tester->grabFixture('post', 0);
        $post = $this->repo->findAnyById($post->id);
        $this->repo->deleteById($post->getPrimaryKey());

        expect($post)->notNull();
        expect($this->repo->findAnyById($post->getPrimaryKey()))->null();
    }
}