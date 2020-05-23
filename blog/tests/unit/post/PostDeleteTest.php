<?php


namespace blog\tests\unit\post;


use blog\managers\PostManager;
use blog\repositories\post\PostRepository;
use Codeception\Specify;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\PostFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;
use Yii;

/**
 * Class PostDelete
 * @property PostManager $manager
 * @property PostRepository $repo
 *
 *
 * @package blog\tests\unit\post
 */
class PostDeleteTest extends Unit
{
    use Specify;

    protected $tester;
    private $repo;
    private $manager;

    protected function _before()
    {
        $this->repo = Yii::$container->get(PostRepository::class);
        $this->manager = Yii::$container->get(PostManager::class);

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
        $this->specify('Set status deleted', function () {
            $post = $this->tester->grabFixture('post', 0);
            $post = $this->repo->findAnyById($post->id);
            $this->manager->statusDelete($post);

            expect($post)->notNull();
            expect($post->isDelete())->true();
            expect($this->repo->findAnyById($post->getPrimaryKey()))->notNull();
        });

        $this->specify('Delete from database', function () {
            $post = $this->tester->grabFixture('post', 0);
            $post = $this->repo->findAnyById($post->id);
            $this->manager->delete($post);

            expect($post->isDelete())->true();
            expect($this->repo->findAnyById($post->getPrimaryKey()))->null();
        });

    }
}