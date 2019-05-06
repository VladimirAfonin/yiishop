<?php
namespace shop\events;
use yii\base\Component;
use yii\base\Event;

class Loader extends Component
{
    const EVENT_SUCCESS = 'success';
    const EVENT_ERROR = 'error';

    public $response;
    public $errorMsg;

    /**
     * @param $url
     */
    public function load($url)
    {
        if (is_string($url)) {
            $this->response = 'its ok';
            $event = new LoaderEvent();
            $this->trigger(self::EVENT_SUCCESS, $event);
        } else {
            $event = new LoaderEvent();
            $this->errorMsg = 'there is some error';
            $this->trigger(self::EVENT_ERROR, $event);
        }
    }
}

// next possible logic:
$loader = new Loader();
// 1-st
$loader->on(Loader::EVENT_SUCCESS, function (LoaderEvent $event) {
    echo $event->sender->reponse;
});
// 2-nd
$loader->on(Loader::EVENT_ERROR, function (LoaderEvent $event) {
    echo $event->errorCode;
});
// 3-rd if static eventHandler
$loader->on('eventName', ['\components\myHandler','myHandlerEvent']);
// 4-th if non-static eventHandler
$myHandlerObj = new Handler();
$loader->on('eventName', [$myHandlerObj,'myHandlerEvent']);
$loader->on('eventName', 'handler');
$loader->load('//ya.ru');