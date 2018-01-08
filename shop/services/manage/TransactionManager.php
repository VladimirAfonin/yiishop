<?php
namespace shop\services\manage;

class TransactionManager
{
    public function wrap(callable $function): void
    {
        $tr = \Yii::$app->db->beginTransaction();
        try {
            $function();
            $tr->commit();
        } catch(\Exception $e) {
            $tr->rollBack();
            throw $e;
        }
    }
}