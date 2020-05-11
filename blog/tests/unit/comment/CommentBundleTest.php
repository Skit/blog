<?php


namespace blog\tests\unit\comment;

use backend\models\CommentForm;
use blog\entities\post\Comment;
use blog\entities\post\Post;
use blog\entities\user\User;
use blog\managers\CommentManager;
use blog\repositories\comment\CommentRepository;
use blog\repositories\post\PostRepository;
use blog\repositories\users\UsersRepository;
use blog\services\CommentService;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\ActiveCommentsFixture;
use common\fixtures\PostFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;
use UnitTester;
use Yii;

/**
 * Class CommentBundleTest
 *
 * @property CommentRepository $repository
 * @property CommentManager $manager
 * @property UsersRepository $userRepository
 * @property PostRepository $postRepository
 * @property UnitTester $tester
 *
 * @package blog\tests\unit\comment
 */
class CommentBundleTest extends Unit
{
    protected $tester;
    private $manager;
    private $repository;
    private $userRepository;
    private $postRepository;

    public function _setUp()
    {
        // TODO убрать переменные которые не используются в тестах
        $this->repository = new CommentRepository(Yii::$app->db);
        $this->postRepository = new PostRepository(Yii::$app->db);
        $this->userRepository = new UsersRepository(Yii::$app->db);
        $this->manager = new CommentManager($this->repository, $this->userRepository, $this->postRepository, new CommentService());

        return parent::_setUp();
    }

    protected function _before()
    {
        $this->tester->haveFixtures([
            'users' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'profile' => [
                'class' => UserProfilesFixture::class,
                'dataFile' => codecept_data_dir() . 'profile.php'
            ],
            'category' => [
                'class' => ActiveCategoriesFixture::class,
                'dataFile' => codecept_data_dir() . 'activeCategories.php'
            ],
            'post' => [
                'class' => PostFixture::class,
                'dataFile' => codecept_data_dir() . 'post.php'
            ],
            'comments' => [
                'class' => ActiveCommentsFixture::class,
                'dataFile' => codecept_data_dir() . 'activeComments.php'
            ]
        ]);
    }

    public function testFind()
    {
        $post = $this->postRepository->findOneById(1, Post::STATUS_ACTIVE);
        $comments = $this->repository->findAllByPost($post, Comment::STATUS_ACTIVE);

        expect($comments->getCount())->greaterOrEquals(2);
        expect($comments->getBundle()[0]->hasParent())->false();
        expect($comments->getBundle()[1]->hasParent())->true();
        expect($comments->getBundle()[1]->getCreator())->notNull();
        expect($comments->getBundle()[1]->getParent()->hasParent())->false();
        expect($comments->getBundle()[1]->getParent()->isActive())->true();
        expect($comments->getBundle()[1]->getParent()->getContent())->notNull();
        expect($comments->getBundle()[1]->getParent()->getCreator())->notNull();
    }
}