<?php
namespace shop\events;

use yii\base\Event;

class LoaderEvent extends Event
{
    public $errorCode;
}