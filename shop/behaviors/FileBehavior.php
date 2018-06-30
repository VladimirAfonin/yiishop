<?php
namespace shop\behaviors;

use yii\base\Behavior;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;

class FileBehavior extends Behavior
{
    public $attribute;
    public $path;

    public function getFileUrl()
    {
        return \Yii::getAlias('@web/'.$this->path).'/'.$this->owner->{$this->attribute};
    }

    public function getFilePath()
    {
        return $this->getFileDir().'/'.$this->owner->{$this->attribute};
    }

    public function getFileDir()
    {
        return \Yii::getAlias('@webroot/'.$this->path);
    }
}

// dynamic attach behavior
// 1-st
/*
$model = Post::findOne($id);
$model->attachBehavior('fileBehavior', [
    'class' => 'shop\behavior\FileBehavior',
    'attribute' => 'image',
    'path' => 'upload/post'
]);
// 2-nd
$behavior = new shop\behavior\FileBehavior();
$behavior->fromAttribute = 'text';
$behavior->toAttribute = 'text_html';
// 3-rd
$model->attachBehavior('markDownBehavior', $behavior);
$model->detachBehavior('markDownBehavior');
*/

// somewhere in Post model...
/*public function behaviors()
{
    return [
        [
            'class' => AttributeBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at','updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
            'value' => function($event){ return time(); }
        ],
        [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
        ]
    ];
}*/