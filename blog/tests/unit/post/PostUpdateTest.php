<?php


namespace blog\tests\unit\post;

use blog\entities\post\Post;
use blog\managers\PostManager;
use blog\repositories\post\PostRepository;
use Codeception\Specify;
use Codeception\Test\Unit;
use common\fixtures\ActiveCategoriesFixture;
use common\fixtures\PostFixture;
use common\fixtures\PostTagFixture;
use common\fixtures\TagFixture;
use common\fixtures\UserFixture;
use common\fixtures\UserProfilesFixture;
use Faker\Factory;
use Faker\Generator;
use Yii;

/**
 * Class PostUpdateTest
 *
 * @property Generator $faker
 * @property PostManager $manager
 * @property PostRepository $repo
 *
 * @package blog\tests\unit\post
 */
class PostUpdateTest extends Unit
{
    use Specify;

    protected $tester;
    private $manager;
    private $repo;
    private $faker;

    protected function _before()
    {
        $this->faker = Factory::create();
        $this->manager = Yii::$container->get(PostManager::class);
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
            'tag' => [
                'class' => TagFixture::class,
                'dataFile' => codecept_data_dir('tags.php')
            ],
            'postTag' => [
                'class' => PostTagFixture::class,
                'dataFile' => codecept_data_dir('post_tag.php')
            ],
        ]);
    }

    public function testUpdate()
    {
        $user = $this->tester->grabFixture('user', 0);
        $category = $this->tester->grabFixture('category', 1);
        $postOld = $this->tester->grabFixture('post', 0);

        $post = $this->repo->findAnyById($postOld->id);
        $form = $this->manager->getForm($post);

        $form->title = $this->faker->text(255);
        $form->tags = 'post, new, some2';
        $form->content = $this->faker->text(1500);
        $form->category_id = $category->id;
        $form->meta_title = '';
        $form->meta_keywords = '';
        $form->meta_description = $this->faker->text(255);
        $form->image_url = null;
        $form->video_url =  $this->faker->imageUrl();
        $form->status = Post::STATUS_INACTIVE;

        $this->manager->update($form, $post);
        $post = $this->manager->find($form);
        $this->manager->delete($post);

        $this->assertNotEquals($postOld->category_id, $category->id);

        expect($post->getTags()->getCount())->equals(3);
        expect($post->getTags())->equals($form->tags);
        expect($post->getCategory()->getPrimaryKey())->equals($category->id);
        expect($post->getCreator()->getUsername())->equals($user->username);
        expect($post->getTitle())->equals($form->title);
        expect($post->getBanners()->getImageUrl())->equals('');
        expect($post->getBanners()->getVideoUrl())->equals($form->video_url);
        expect($post->getMetaData()->getTitle())->equals('');
        expect($post->getMetaData()->getKeywords())->equals('');
        expect($post->getMetaData()->getDescription())->equals($form->meta_description);
        expect($post->isHighlight())->false();
        expect($post->getStatus())->equals($form->status);

        expect($post->isActive())->false();
    }

    public function testHighlightUpdate()
    {
        // TODO важен порядок. Проблемы с удалением созданных записей
        $this->specify('Unset highlight', function() {
            $postOld = $this->tester->grabFixture('post', 1);
            $post = $this->repo->findAnyById($postOld->id);
            $form = $this->manager->getForm($post);

            $form->title = 'Unset highlight';
            $form->content = $this->faker->text(255);
            $form->video_url =  $this->faker->imageUrl();

            $this->manager->update($form, $post);
            $post = $this->manager->find($form);
            $this->manager->delete($post);

            expect($post->isHighlight())->false();
            expect($post->getContent())->equals($form->content);
            expect($post->getZipContent())->null();
            expect($post->getHighlightContent())->null();
        });

        $this->specify('Set highlight', function()  {
            $postOld = $this->tester->grabFixture('post', 0);
            $post = $this->repo->findAnyById($postOld->id);
            $form = $this->manager->getForm($post);

            $form->content .= '<pre class="_lang__php"><?="Hello World";?></pre>';
            $form->video_url =  $this->faker->imageUrl();

            $this->manager->update($form, $post);
            $post = $this->manager->find($form);
            $this->manager->delete($post);

            expect($post->isHighlight())->true();
            expect($post->getContent())->equals($form->content);
            expect($post->getZipContent())->notNull();
            expect($post->getHighlightContent())->stringContainsString('hljs php');
            expect($post->getHighlightContent())->stringNotContainsString('_lang__php');
        });
    }
}