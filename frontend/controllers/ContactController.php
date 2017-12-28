<?php
namespace frontend\controllers;

use yii\web\Controller;
use yii\base\Module;
use shop\services\contact\ContactService;
use Yii;
use shop\forms\auth\ContactForm;

class ContactController extends Controller
{
    private $contactService;

    public function __construct(
        $id,
        Module $module,
        ContactService $contactService,
        $config = [] )
    {
        parent::__construct($id, $module, $config);
        $this->contactService = $contactService;
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $form = new ContactForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->contactService->send($form);
                Yii::$app->session->setFlash('success', 'thank you for contacting us. We will respond to you as soon as possible.');
                return $this->goHome();
            } catch (\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }
            return $this->refresh();
        }
        return $this->render('index', [
            'model' => $form,
        ]);

    }
}