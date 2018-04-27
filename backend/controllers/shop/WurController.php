<?php
namespace backend\controllers\shop;

use yii\web\Controller;

ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);

class WurController extends Controller
{
    /**
     * @return string
     */
    public function actionRating()
    {
        return $this->render('ratings');
    }

    public function actionStart()
    {
        return $this->render('start', compact('result'));
    }
}