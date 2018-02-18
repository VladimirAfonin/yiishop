<?php
namespace backend\controllers\shop;

use backend\forms\shop\BrandSearch;
use shop\forms\manage\Shop\BrandForm;
use shop\services\manage\Shop\BrandManageService;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\base\Module;
use Yii;
use shop\entities\Shop\Brand;
use backend\entities\WebPage;

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
     * get raw html data,
     * scrap data
     *
     * @return string
     */
    public function actionTest()
    {
        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Tohoku University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Rice University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ivan Franko National University of Lviv', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Illinois Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of California, Office of The President', 'gl => US', 'hl' => 'en'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tehran', 'gl => US', 'hl' => 'en'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Gothenburg', 'gl => US', 'hl' => 'en'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Russian Academy of Justice', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tokyo', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Swiss Federal Institute of Technology Zurich', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Canterbury', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ecole Normale Superieure  Paris', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Heidelberg University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidade de Sorocaba', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Al-Farabi University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Seoul National University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Bangkok University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ryerson University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ryerson University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Pepperdine University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Heilongjiang University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ankara University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Andrews University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Technical University Munich', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Porto', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Saurashtra University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Weizmann Institute of Science', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Liaoning Medical University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Indian Institute of Science', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Hanyang University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Osaka University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Ohio State University - Columbus', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Oregon Health and Science University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Kiel', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Medical College of Wisconsin', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Lanzhou University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Kyung Hee University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Technical University of Lisbon', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Calgary', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Copenhagen Business School', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidad de Palermo Argentina', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Escola Bahiana de Medicina e Saúde Publica', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Universidade Estadual do Rio Grande do Sul UERGS', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Okayama University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'King Fahd University of Petroleum & Minerals', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'ESPCI ParisTech', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'China Agricultural University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Arkansas State University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Technion-Israel Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Mordovia State University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Hamburg', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Amity University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Texas at Dallas', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Bauman Moscow State Technical University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tsukuba', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Kyoto University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Emory University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Tehran', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Saint Petersburg State University of Architecture and Civil Engineering', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Carleton University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'United Arab Emirates University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Royal Melbourne Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Hong Kong University of Science and Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Curtin University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'City University of Hong Kong', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Australian National University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'National University of Singapore', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Florida International University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Massachusetts Institute of Technology', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Cambridge', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'ASA College', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Moscow State University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Lingnan University', 'gl => US', 'hl' => 'en'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'University of Sydney'], [], false);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'harvard university'], [], true);
//        $html = WebPage::get('https://www.google.ru/search', ['q' => 'Lomonosov University'], [], true);

        return $this->render('test', compact('html'));
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
     * @param  $id
     * @return Brand
     */
    public function findModel( $id): Brand
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        }
        throw new \RuntimeException('The requested brand does not exist.');

    }
}