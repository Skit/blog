<?php declare(strict_types=1);


namespace blog\services;


use blog\entities\common\MetaData;
use yii\base\Model;

class ContentService
{
    /**
     * @param Model $model
     * @return MetaData
     */
    public function setMetaData(Model $model): MetaData
    {
        return new MetaData($model->meta_title, $model->meta_description, $model->meta_keywords);
    }
}