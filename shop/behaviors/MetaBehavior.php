<?php
namespace shop\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use shop\entities\Meta;

class MetaBehavior extends Behavior
{
    public $attribute = 'meta';
    public $attributeJson = 'meta_json';

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'onAfterFind',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'onBeforeSave',
            ActiveRecord::EVENT_BEFORE_INSERT => 'onBeforeSave',
        ];
    }

//    public function attach($owner)
//    {
//        $brand = $owner;
//        $brand->on(ActiveRecord::EVENT_AFTER_FIND, [$this, 'onAfterFind']);
//        parent::attach($owner);
//    }

    public function onAfterFind(Event $event)
    {
        $brand = $event->sender;
        $meta = Json::decode($brand->getAttribute($this->attributeJson));
        $brand->{$this->attribute} = new Meta(
            ArrayHelper::getValue($meta, 'title'),
            ArrayHelper::getValue($meta, 'description'),
            ArrayHelper::getValue($meta, 'keywords'));
    }

    public function onBeforeSave(Event $event)
    {
        $brand = $event->sender;
        $brand->setAttribute($this->attributeJson, Json::encode([
            'title' => $brand->{$this->attribute}->title, // $brand->meta->title
            'description' => $brand->{$this->attribute}->description,
            'keywords' => $brand->{$this->attribute}->keywords
        ]));
    }
}