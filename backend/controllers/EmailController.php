<?php
namespace backend\controllers;

use yii\filters\{AccessControl, VerbFilter};
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use backend\models\Email;
use yii\base\Module;
use common\services\EmailService;
use Yii;

class EmailController extends Controller
{
    private $_emailService;

    public function __construct($id, Module $module, EmailService $emailService, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_emailService = $emailService;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['send', 'grab'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    /**
     * send the email
     *
     * @return mixed
     * @throws \Exception
     */
    public function actionSend()
    {
        $model = new Email(['scenario' => 'send_email']);
        $model->load($params = Yii::$app->request->queryParams);

        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                $emailMessage = $this->_emailService->send($model); // sending email
                $this->_emailService->appendImapEmail($emailMessage); // append email
                Yii::$app->session->setFlash('success', 'send successfully.');
            } catch(\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'There is some error: ' . $e->getMessage() . ' ... try again.');
            }
            return $this->refresh();
        }
        return $this->render('send', ['model' => $model]);
    }

    /**
     * grab emails from manager's and save it to db
     */
    public function actionGrab()
    {
        $interval = $interval ?? Yii::$app->params['imap.sinc.interval'];
        // prepare data
        foreach (array_values(Yii::$app->params['accounts']) as $user) {
            // get list of folders
            if(!$inbox = @imap_open($user['folder']['inbox'], $user['email'], $user['password'])) throw new \RuntimeException('Cant connect to mail boxes: ' . imap_last_error());
            $mailboxes = imap_list($inbox, $user['host'], '*');
            imap_close($inbox);

            // set time to search...
            $time = time() - $interval; // last ten minutes
            $dateMin = date('j F Y', $time);

            // all folders
            foreach ($mailboxes as $key => $item) {
                if(!$inbox = @imap_open($item,  $user['email'], $user['password']))  throw new \RuntimeException('Cant connect to mail boxes: ' . imap_last_error());
                $emails = imap_search($inbox, 'SINCE "' . $dateMin . '"');
                $folder = $this->_emailService->getFolder($key);
                $this->_emailService->save($emails, $inbox, $folder);
                imap_close($inbox);
            }
        }
        exit('done.');
    }

}