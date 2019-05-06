<?php
namespace frontend\controllers;

use backend\forms\UserSearch;
use frontend\entities\TestUser;
use frontend\search\TestUserSearch;
use Yii;
use yii\web\NotFoundHttpException;

class TestUserController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $searchModel = new TestUserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param $id
     * @return bool
     * @throws NotFoundHttpException
     */
    private function findModel($id)
    {
        if ($model = TestUser::findOne($id) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException;
        }
    }

}