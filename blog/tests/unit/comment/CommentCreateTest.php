<?php


namespace blog\tests\unit\comment;


use backend\models\CommentForm;
use blog\entities\post\Comment;
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
use Faker\Factory;
use Faker\Generator;
use Yii;

/**
 * Class CommentCreateTest
 *
 * @property Generator $faker
 * @property CommentRepository $repository
 * @property UsersRepository $userRepository
 * @property CommentManager $manager
 *
 * @package blog\tests\unit\comment
 */
class CommentCreateTest extends Unit
{
    protected $tester;
    private $repository;
    private $userRepository;
    private $manager;
    private $faker;

    public function _setUp()
    {
        $this->faker = Factory::create();
        $this->repository = new CommentRepository(Yii::$app->db);
        $this->userRepository = new UsersRepository(Yii::$app->db);
        $this->manager = new CommentManager($this->repository, $this->userRepository, new PostRepository(Yii::$app->db), new CommentService());

        return parent::_setUp();
    }

    protected function _before()
    {
        $this->tester->haveFixtures([
            'user' => [
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

    public function testFullCreate()
    {
        $user = $this->tester->grabFixture('user', 0);
        $post = $this->tester->grabFixture('post', 0);

        // TODO все тесты репов переделать на фикстуры. не нужно тут созавать объекты. Подобные тесты сделать на сущностях
        $form = new CommentForm();
        $form->content = $this->faker->text(255);
        $form->post_id = $post->id;
        $form->parent_id = 0;
        $form->creator_id = $user->id;
        $form->status = Comment::STATUS_ACTIVE;

        $comment = $this->manager->create($form);
        $comment = $this->repository->findOneById($comment->getPrimaryKey(), $form->status);

        expect($comment->getContent())->equals($form->content);
        expect($comment->getParent()->getPrimaryKey())->equals(0);
        expect($comment->getCreator()->getUsername())->equals($user->username);
    }

    public function testReply()
    {
        $user = $this->tester->grabFixture('user', 1);
        $comment = $this->tester->grabFixture('comments', 1);

        $replyForm = new CommentForm();
        $replyForm->parent_id = $comment->id;
        $replyForm->post_id = $comment->post_id;
        $replyForm->creator_id = $user->id;
        $replyForm->content = 'reply text';
        $replyForm->status = Comment::STATUS_ACTIVE;

        $reply = $this->manager->create($replyForm);

        expect($reply->getContent())->equals($replyForm->content);
        expect($reply->getParent()->getContent())->equals($comment->content);
        expect($reply->getCreator()->getUsername())->equals($user->username);
    }
}