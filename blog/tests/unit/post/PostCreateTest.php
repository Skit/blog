<?php


namespace blog\tests\unit\post;


use backend\models\PostForm;
use blog\entities\common\Date;
use blog\entities\post\Post;
use blog\managers\PostManager;
use blog\repositories\category\CategoriesRepository;
use blog\repositories\post\PostRepository;
use blog\repositories\users\UsersRepository;
use blog\services\PostService;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\ActiveCommentsFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;
use Faker\Factory;
use Faker\Generator;
use Yii;


/**
 * Class PostCreateTest
 * @package blog\tests\unit\post
 *
 * @property Generator $faker
 * @property PostRepository $repository
 */
class PostCreateTest extends Unit
{
    protected $tester;
    private $repository;
    private $userRepository;
    private $manager;
    private $faker;

    public function _setUp()
    {
        $this->faker = Factory::create();
        $this->repository = new PostRepository(Yii::$app->db);
        $this->userRepository = new UsersRepository(Yii::$app->db);
        $categoryRepository = new CategoriesRepository(Yii::$app->db);
        $this->manager = new PostManager($this->repository,$categoryRepository, new PostService());

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
        ]);
    }

    public function testFullCreate()
    {
        $user = $this->tester->grabFixture('user', 0);
        $category = $this->tester->grabFixture('category', 0);

        $form = new PostForm();
        $form->title = $this->faker->text(255);
        $form->slug = $this->faker->text(50);
        $form->content = $this->faker->text(1500);
        $form->preview = $this->faker->text(255);
        $form->meta_title = $this->faker->text(150);
        $form->meta_keywords = $this->faker->words(25, true);
        $form->meta_description = $this->faker->text(255);
        $form->image_url = $this->faker->imageUrl();
        $form->video_url =  '';
        $form->published_at = Date::getFormatNow();
        $form->status = Post::STATUS_ACTIVE;
        $form->count_view = 0;
        $form->category_id = $category->id;
        $form->creator_id = $user->id;

        $user = $this->userRepository->findOneById($user->id, $user->status);
        $post = $this->manager->create($user, $form);
        $post = $this->repository->findOneById($post->getPrimaryKey(), $form->status);

        expect($post->isHighlight())->false();
        expect($post->getContent())->equals($form->content);
        expect($post->getCategory()->getContent())->notNull();
        expect($post->getCreator()->getProfile())->notNull();
        expect($post->getBanners()->getVideoUrl())->equals('');
        expect($post->getBanners()->getImageUrl())->equals($form->image_url);
        expect($post->getMetaData()->getDescription())->equals($form->meta_description);
    }
}