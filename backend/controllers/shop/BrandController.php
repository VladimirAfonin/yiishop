<?php
namespace backend\controllers\shop;

use backend\forms\shop\BrandSearch;
use shop\forms\manage\Shop\BrandForm;
use shop\services\manage\Shop\BrandManageService;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\base\Module;
use Yii;
use shop\entities\Shop\Brand;
use backend\entities\WebPage;
use backend\entities\{ProxyRequest, ProxyList};


class BrandController extends Controller
{
    private $_service;

    public function __construct($id, Module $module, BrandManageService $service, array $config = [])
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
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', compact('searchModel', 'dataProvider'));
    }

    public function actionTest()
    {
        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidad de Montevideo'], [], true);
//        $htmlDom = WebPage::urlDom($html); //->query('//*[@id="rhs_block"]');
        return $this->render('test', compact('html'  /*, 'htmlDom', 'proxy'*/ ));
    }

    /**
     * @param $id
     * @return string
     */
    public function actionView($id)
    {
        return $this->render('view', ['brand' => $this->findModel($id)]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionCreate()
    {
        $form = new BrandForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $brand = $this->_service->create($form);
                return $this->redirect(['view', 'id' => $brand->id]);
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
        $brand = $this->findModel($id);
        $form = new BrandForm($brand);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_service->edit($id, $form);
                Yii::$app->session->setFlash('success', 'brand successfully edit');
                return $this->redirect(['view', 'id' => $brand->id]);
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'model' => $form,
            'brand' => $brand
        ]);
    }

    /**
     * @param $id
     */
    public function actionDelete($id)
    {
        try {
            $this->_service->remove($id);
            return $this->redirect(['index']);
        } catch(\RuntimeException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param $id
     * @return Brand
     */
    public function findModel($id): Brand
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        }
        throw new \RuntimeException('The requested brand does not exist.');

    }
}