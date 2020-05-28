<?php


namespace api\controllers;

use api\managers\PostImageManager;
use api\models\PostImageForm;
use blog\components\ImageResizer\exceptions\ImageResizerException;
use blog\components\PathReplacer\PathReplacerExceptions;
use blog\entities\post\exceptions\PostBlogException;
use blog\entities\relation\exceptions\RelationException;
use blog\entities\tag\exceptions\TagException;
use blog\managers\PostManager;
use blog\repositories\exceptions\RepositoryException;
use Codeception\Util\HttpCode;
use yii\base\InvalidConfigException;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;
use yii\web\Response;

/**
 * TODO вернуть сответствующие коды ответа
 * Class ApiController
 * @package backend\controllers
 */
class PostImageController extends Controller
{
    private $postManager;
    private $imageManager;
    private $response;
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
     * @param PostManager $postManager
     * @param PostImageManager $imageManager
     * @param Request $request
     * @param Response $response
     * @param array $config
     */
    public function __construct($id, $module, PostManager $postManager, PostImageManager $imageManager, Request $request, Response $response, $config = [])
    {
        $this->response = $response;
        $this->request = $request;
        $this->imageManager = $imageManager;
        $this->postManager = $postManager;
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

        if ($model->validate('postUuid')) {
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
     * @throws ImageResizerException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws PathReplacerExceptions
     * @throws PostBlogException
     * @throws RelationException
     * @throws RepositoryException
     * @throws TagException
     */
    public function actionResize()
    {
        $model = new PostImageForm();
        $model->loadParams($this->request->getBodyParams());

        if ($model->validate()) {
            if ($post = $this->postManager->findOneActiveByUuid($model->postUuid)) {
                $model->fileName = $model->fileName ?? uniqid();
                $original = $this->imageManager->original($model);
                $resized = $this->imageManager->marginalResize($model);

                // TODO бросить исключение
                if ($this->postManager->setImage($post, $resized->getRelative())) {
                    $this->response->setStatusCode(HttpCode::CREATED);
                }

                return [
                    'resized' => [
                        'url' => $resized->getUrl(),
                        'relative' => $resized->getRelative(),
                    ],
                    'original' => [
                        'url' => $original->getUrl(),
                        'relative' => $original->getRelative(),
                    ],
                    '_links' => [
                        'delete' => "http://backend.blog.loc/api/post-images?{$model->postUuid}&path={$resized->getRelative()}"
                    ]
                ];
            } else {
                throw new NotFoundHttpException('Post is not found');
            }
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

        if ($model->validate(['postUuid', 'path'])) {
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