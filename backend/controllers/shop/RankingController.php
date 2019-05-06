<?php
namespace backend\controllers\shop;

use yii\web\Controller;

ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);

class RankingController extends Controller
{
    /**
     * action for a most popular rating's
     * @return string
     */
    public function actionRating()
    {
        return $this->render('ratings');
    }

    /**
     * action for sub ratings( > 50)
     */
    public function actionSubRating()
    {
        return $this->render('subRatings');
    }

    /**
     * get info for one item
     * @return string
     */
    public function actionData()
    {
        return $this->render('data');
    }
}