<?php
namespace frontend\controllers;

use yii\web\Controller;
use shop\entities\Shop\Product\Product;

class CatalogController extends Controller
{
    public $layout = 'catalog';

    public function actionIndex()
    {
        // we use as default, but we have ProductQuery with custom 'find' method
//        Product::find()->andWhere(['status' => Product::STATUS_ACTIVE]);

        // our custom ProductQuery!!!
        Product::find()->active('p')/* but doesnot work with: ->alias('p')->joinWith('categoryAssignments c')->andWhere(['category_id' => $id])->all() */->all();

        return $this->render('index');
    }
}