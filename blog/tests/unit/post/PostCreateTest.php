<?php


namespace blog\tests\unit\post;


use backend\models\PostForm;
use blog\entities\common\Date;
use blog\entities\post\Post;
use blog\managers\PostManager;
use Codeception\Specify;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
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
 * @property PostManager $manager
 */
class PostCreateTest extends Unit
{
    use Specify;

    protected $tester;
    private $manager;
    private $faker;

    public function _setUp()
    {
        $this->faker = Factory::create();
        $this->manager = Yii::$container->get(PostManager::class);

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
        $form->preview = $this->faker->text(255);
        $form->content = $this->faker->text(1500);
        $form->meta_title = $this->faker->text(150);
        $form->meta_keywords = $this->faker->words(25, true);
        $form->meta_description = $this->faker->text(255);
        $form->image_url = $this->faker->imageUrl();
        $form->video_url =  $this->faker->imageUrl();
        $form->published_at = Date::getFormatNow();
        $form->status = Post::STATUS_ACTIVE;
        $form->tags = 'first, second, third';
        $form->count_view = 0;
        $form->category_id = $category->id;
        $form->creator_id = $user->id;

        // TODO важен порядок. Пост не удаляется между тестами.
        $this->specify('Full without highlight', function() use ($form) {
            $post = $this->manager->create($form);
            $form->id = $post->getPrimaryKey();
            $post = $this->manager->find($form);
            $this->manager->delete($post);

            expect($post->isHighlight())->false();
            expect($post->getContent())->equals($form->content);
            expect($post->getZipContent())->null();
            expect($post->getHighlightContent())->null();
        });

        $this->specify('Full with highlight', function() use ($form) {
            $form->content .= '<pre class="_lang__php"><?="Hello World";?></pre>';

            $post = $this->manager->create($form);
            $form->id = $post->getPrimaryKey();
            $post = $this->manager->find($form);
            $this->manager->delete($post);

            expect($post->isHighlight())->true();
            expect($post->getContent())->equals($form->content);
            expect($post->getZipContent())->notNull();
            expect($post->getHighlightContent())->stringContainsString('hljs php');
            expect($post->getHighlightContent())->stringNotContainsString('_lang__php');

            expect($post->getTags()->getCount())->equals(3);
            expect($post->getTags()->getFieldsString('title'))->notNull();
            expect($post->getCategory()->getContent())->notNull();
            expect($post->getCreator()->getProfile())->notNull();

            expect($post->getBanners()->getVideoUrl())->equals($form->video_url);
            expect($post->getBanners()->getImageUrl())->equals($form->image_url);
            expect($post->getBanners()->getMainUrl())->equals($form->video_url);

            expect($post->getMetaData()->getTitle())->equals($form->meta_title);
            expect($post->getMetaData()->getKeywords())->equals($form->meta_keywords);
            expect($post->getMetaData()->getDescription())->equals($form->meta_description);
        });
    }
}