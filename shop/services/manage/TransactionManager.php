<?php
namespace shop\services\manage;

use shop\dispatchers\DeferredEventDispatcher;

class TransactionManager
{
    private $dispatcher;

    public function __construct(DeferredEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function wrap(callable $function): void
    {
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $this->dispatcher->defer();
            $function();
            $tr->commit();
            $this->dispatcher->release();
        } catch(\Exception $e) {
            $tr->rollback();
            $this->dispatcher->clean();
            throw $e;
        }
    }
}