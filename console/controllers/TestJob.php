<?php
namespace console\controllers;

use GuzzleHttp\Event\EventInterface;
use yii\base\BaseObject;
use yii\queue\JobInterface;
use yii\queue\Queue;

class TestJob extends BaseObject implements JobInterface
{
    public $name;

    /**
     * @param Queue $queue which pushed and is handling the job
     * @return void|mixed result of the job execution
     */
    public function execute($queue)
    {
        file_put_contents(__DIR__ . '/1.txt', $this->name);
    }
}