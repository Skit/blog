<?php


namespace api\models;


use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class PostImageForm
 *
 * @property string $type
 * @property string $path
 * @property string $fileName
 * @property string $postUuid
 * @property integer $with
 * @property integer $height
 * @property bool $compress
 * @property UploadedFile $image
 *
 * @package api\models
 */
class PostImageForm extends Model
{
    public const TYPE_RESIZED = 'resized';
    public const TYPE_ORIGINAL = 'original';

    public $postUuid;
    public $path;
    public $fileName;
    public $image;
    public $with;
    public $height;
    public $compress = false;

    public $type;

    public function rules()
    {
        return [
            [['postUuid', 'image', 'with', 'height'], 'required'],
            [['path'], 'string', 'max' => 255],
            [['postUuid', 'fileName'], 'string', 'max' => 36],
            [['with', 'height'], 'integer'],
            [['compress'], 'boolean', 'trueValue' => true, 'falseValue' => false],
            // TODO использовать проверку из компонента, которая достоверно проверяет формат, а не расширение
            ['image', 'file', 'extensions' => ['png', 'jpg'], 'maxSize' => 1024*1024*3],
        ];
    }

    public function beforeValidate()
    {
        $this->image = UploadedFile::getInstanceByName('image');
        return parent::beforeValidate();
    }

    public function loadParams(array $params)
    {
        return parent::load($params, '');
    }
}