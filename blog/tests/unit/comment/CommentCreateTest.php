<?php


namespace blog\tests\unit\comment;


use backend\models\CommentForm;
use blog\entities\post\Comment;
use blog\entities\user\User;
use blog\managers\CommentManager;
use blog\repositories\comment\CommentRepository;
use blog\repositories\users\UsersRepository;
use blog\services\CommentService;
use Codeception\Test\Unit;
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
        $this->manager = new CommentManager($this->repository, new CommentService());

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
            ]
        ]);
    }

    public function testFullCreate()
    {
        // TODO все тесты репов переделать на фикстуры. не нужно тут созавать объекты. Подобные тесты сделать на сущностях
        $form = new CommentForm();
        $form->content = $this->faker->text(255);
        $form->parent_id = 0;
        $form->status = Comment::STATUS_ACTIVE;

        $user = $this->tester->grabFixture('user', 0);
        $user = $this->userRepository->findOneById($user->id, $user->status);

        $comment = $this->manager->create($user, $form);
        $comment = $this->repository->findOneById($comment->getPrimaryKey(), $form->status);

        expect($comment->getContent())->equals($form->content);
        expect($comment->getParent()->getPrimaryKey())->equals(0);
        expect($comment->getCreator()->getUsername())->equals($user->getUsername());
    }
}