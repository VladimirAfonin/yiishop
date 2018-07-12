<?php

namespace backend\controllers;

use shop\entities\Attribute;
use shop\entities\AttributeValue;
use Yii;
use shop\entities\Product;
use backend\controllers\search\ProductSearch;
use yii\base\Model;
use yii\caching\TagDependency;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
           /* 'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ]
                ],
            ],*/
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

   /* public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }*/

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // cache particular ...
            TagDependency::invalidate(Yii::$app->cache, ['product', 'categories']); // метки для кэша

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        /** @var AttributeValue[] $existAttributes */
        $existAttributes = $model->getAttributeValue()->with('productAttribute')->indexBy('attribute_id')->all(); // 'id' of attributesValues in product
        $allAttributes = Attribute::find()->indexBy('id')->all(); // 'id' of all attributes

        foreach (array_diff_key($allAttributes, $existAttributes) as $attribute) {
            $existAttributes[] = new AttributeValue(['attribute_id' => $attribute->id]); // now we have all attributes empty or not empty
        }
        foreach ($existAttributes as $attributeValue) {
            $attributeValue->setScenario(AttributeValue::SCENARIO_TABULAR);
        }
        $post = Yii::$app->request->post();
        if ($model->load($post) && $model->save() && Model::loadMultiple($existAttributes, $post)) {
            $transaction = Product::getDb()->beginTransaction();
            try {
                $model->save(false);
                $this->saveAttributeValues($existAttributes, $model);
                $transaction->commit();
            } catch(\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            foreach ($existAttributes as $existAttribute) {
                $existAttribute->product_id = $model->id;
                if ($existAttribute->validate()) {
                    /** @var AttributeValue $existAttribute */
                    if (!empty($existAttribute->value)) {
                        $existAttribute->save();
                    } else {
                        $existAttribute->delete();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'values' => $existAttributes
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
