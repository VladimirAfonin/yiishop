<?php
namespace frontend\controllers\auth;


use shop\services\auth\PasswordResetService;
use yii\web\Controller;
use Yii;
use yii\base\Module;
use shop\forms\auth\PasswordResetRequestForm;
use yii\web\BadRequestHttpException;
use shop\forms\auth\ResetPasswordForm;

class ResetController extends Controller
{
    private $passwordResetService;

    public function __construct($id, Module $module, PasswordResetService $passwordResetService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->passwordResetService = $passwordResetService;
    }


    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $form = new PasswordResetRequestForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try{
                $this->passwordResetService->request($form);
                Yii::$app->session->setFlash('success', 'check your email  for further instructions');
                return $this->goHome();
            } catch(\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $form,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try{
            $this->passwordResetService->validateToken($token);
        } catch(\RuntimeException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $form = new ResetPasswordForm($token);

        if($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->passwordResetService->reset($token, $form);
                Yii::$app->session->setFlash('success', 'new password saved.');
            } catch (\RuntimeException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $form,
        ]);
    }


}