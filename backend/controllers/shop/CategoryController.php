<?php
namespace backend\controllers\shop;

use backend\forms\shop\CategorySearch;
use shop\entities\Shop\Category;
use shop\forms\manage\Shop\CategoryForm;
use shop\services\manage\Shop\CategoryManageService;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\base\Module;
use Yii;

class CategoryController extends Controller
{
    private $_service;

    public function __construct($id, Module $module, CategoryManageService $service, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    /*
                    'move-up' => ['POST'],
                    'move-down' => ['POST'],
                    */
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', ['category' => $this->findModel($id)]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionCreate()
    {
        $form = new CategoryForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $category = $this->_service->create($form);
                return $this->redirect(['view', 'id' => $category->id]);
            } catch(\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('create', ['model' => $form]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUpdate($id)
    {
        $category = $this->findModel($id);
        $form = new CategoryForm($category);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->edit($category->id, $form);
                Yii::$app->session->setFlash('success', 'category successfully edited');
                return $this->redirect(['view', 'id' => $category->id]);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,
            'category' => $category
        ]);
    }

    /**
     * @param $id
     */
    public function actionDelete($id)
    {
        try {
            $this->_service->remove($id);
            Yii::$app->session->setFlash('success', 'category successfully deleted.');
            return $this->redirect(['index']);
        } catch(\RuntimeException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return Category
     */
    public function findModel($id): Category
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        }
        throw new \RuntimeException('The requested category does not exist.');

    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionMoveUp($id)
    {
        $this->_service->moveUp($id);
        return $this->redirect(['index']);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionMoveDown($id)
    {
        $this->_service->moveDown($id);
        return $this->redirect(['index']);
    }
}