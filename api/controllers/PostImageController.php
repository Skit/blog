<?php


namespace api\controllers;

use api\managers\PostImageManager;
use api\models\PostImageForm;
use blog\components\ImageResizer\exceptions\ImageResizerException;
use blog\components\PathReplacer\PathReplacerExceptions;
use blog\repositories\post\PostRepository;
use yii\base\InvalidConfigException;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\Request;

/**
 * TODO вернуть сответствующие коды ответа
 * Class ApiController
 * @package backend\controllers
 */
class PostImageController extends Controller
{
    private $postRepository;
    private $imageManager;
    private $request;

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = ['class' => HttpBearerAuth::class];

        return $behaviors;
    }

    /**
     * PostImageController constructor.
     * @param $id
     * @param $module
     * @param PostRepository $postRepository
     * @param PostImageManager $imageManager
     * @param Request $request
     * @param array $config
     */
    public function __construct($id, $module, PostRepository $postRepository, PostImageManager $imageManager, Request $request, $config = [])
    {
        $this->request = $request;
        $this->imageManager = $imageManager;
        $this->postRepository = $postRepository;
        parent::__construct($id, $module, $config);
    }

    /**
     * @return PostImageForm|array
     * @throws PathReplacerExceptions
     */
    public function actionIndex()
    {
        $model = new PostImageForm();
        $model->loadParams($this->request->getQueryParams());

        if ($model->validate('postId')) {
            $typeResized = PostImageForm::TYPE_RESIZED;
            $typeOriginal = PostImageForm::TYPE_ORIGINAL;

            return [
                $typeResized => $this->imageManager->filesList($model, $typeResized),
                $typeOriginal => $this->imageManager->filesList($model, $typeOriginal),
            ];
        }

        return $model;
    }

    /**
     * @return PostImageForm|array
     * @throws PathReplacerExceptions
     * @throws ImageResizerException
     * @throws InvalidConfigException
     */
    public function actionResize()
    {
        $model = new PostImageForm();
        $model->loadParams($this->request->getBodyParams());

        if ($model->validate()) {
            $model->fileName = $model->fileName ?? uniqid();
            $original = $this->imageManager->original($model);
            $resized = $this->imageManager->marginalResize($model);

            return [
                'image' => [
                    'resized' => [
                        'url' => $resized->getUrl(),
                        'relative' => $resized->getRelative(),
                    ],
                    'original' => [
                        'url' => $original->getUrl(),
                        'relative' => $original->getRelative(),
                    ],
                ],
                '_links' => [
                    'delete' => "http://backend.blog.loc/api/post-images?{$model->postId}&path={$resized->getRelative()}"
                ]
            ];
        }

        return $model;
    }

    /**
     * @return PostImageForm|array
     * @throws PathReplacerExceptions
     */
    public function actionDelete()
    {
        $model = new PostImageForm();
        $model->loadParams($this->request->getQueryParams());

        if ($model->validate(['postId', 'path'])) {
            if ($this->imageManager->delete($model)) {
                $typeResized = PostImageForm::TYPE_RESIZED;

                return [
                    $typeResized => $this->imageManager->filesList($model, $typeResized),
                ];
            }

            $model->addError('path', 'Unable to delete');
        }

        return $model;
    }
}