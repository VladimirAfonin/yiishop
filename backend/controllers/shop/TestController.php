<?php
namespace backend\controllers\shop;

use backend\controllers\shop\test\Gsm;
use backend\entities\WebPageHelper;
use Mailgun\Mailgun;
use shop\forms\auth\EmailForm;
use yii\filters\VerbFilter;
use yii\web\Controller;
use Yii;
use backend\entities\WebPage;
use app\models\ConflictsList;
use backend\models\Email;
use backend\models\UserAccount;
use yii\base\Module;
use common\services\EmailService;
use yii\helpers\Html;

ini_set('max_execution_time', 70);
ini_set('memory_limit', '256M');
class TestController extends Controller
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
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST']
                ]
            ]
        ];
    }

    /**
     * get table with conflicts
     * scrap data
     * @return string
     */
    public function actionTest()
    {
        $googleApiKey = 'AIzaSyDdQ2h8pTul-FVW89x4vMN6mL7xn-N7Ms4';
        // get names of items from csv file
        $path = dirname(__FILE__);
        $fh = fopen($path . '/../../entities/universities.csv', 'r');
        $namesOfUniversities = [];
        while ($row = fgetcsv($fh, 0, ',', 'r')) {
            $namesOfUniversities[] = $row[1];
        }

        $namesOfUniversities = array_slice($namesOfUniversities, 1);
        $countOfItems = count($namesOfUniversities);
        $try = true;
        $steps = 500;
        $step  = 0;
        $conflictsCount = 0;
        while($try) {
            $wikiId = 'Q13371'; // harvard for example,  must be from db
            $linkToWiki = 'http://en.wikipedia.org/wiki/Harvard_University'; // // harvard for example,  must be from db
            $propertyWiki = 'P856'; // for website

            $websiteWiki = WebPage::getDataFromApi('https://www.wikidata.org/w/api.php', ['action' => 'wbgetclaims', 'entity' => $wikiId, 'property' => $propertyWiki, 'format' => 'json']);
            $websiteFromGoogleApi = WebPage::getDataFromApi('https://kgsearch.googleapis.com/v1/entities:search', ['query' => $namesOfUniversities[$step], 'key' => $googleApiKey, 'types' => 'CollegeOrUniversity', 'limit' => 1]);

            $arrFromGoogleApi = json_decode($websiteFromGoogleApi);
            $websiteFromGoogleApi = $arrFromGoogleApi->itemListElement[0]->result->url ?? ''; // google
            $arrFromWiki = json_decode($websiteWiki);
            $websiteWiki = $arrFromWiki->claims->$propertyWiki[0]->mainsnak->datavalue->value ?? ''; // wiki
            $websiteFromDB = 'http://www.harvard.edu'; // for harvard // db

            // check for conflicts
            $dataResult = WebPageHelper::isWebsiteInfoEqual($websiteFromGoogleApi, $websiteFromDB, $websiteWiki);
            if($dataResult < 98) {
                $m = new ConflictsList();
                $m->name = $namesOfUniversities[$step];
                $m->link_wiki = $linkToWiki;
                $m->wiki_website = $websiteWiki;
                $m->google_website = $websiteFromGoogleApi;
                $m->db_website = $websiteFromDB;
                $m->save(false);


                $conflictsCount++;
            }
            $step++;
            // get page from cache or new request
            WebPage::get('https://www.google.ru/search', ['q' => $namesOfUniversities[$step], 'gl => US', 'hl' => 'en'], [], false);
            $try = ($conflictsCount < $steps);
        }

        return $this->render('conflicts', [
            'models' => ConflictsList::find()->all(),
           ]);
    }

    /**
     * detail view for one item
     *
     * @return string
     */
    public function actionDetailView($needParser = 0, $id = false)
    {
        $googleApiKey = 'AIzaSyDdQ2h8pTul-FVW89x4vMN6mL7xn-N7Ms4';
        // get names of items from csv file
        $path = dirname(__FILE__);
        $fh = fopen($path . '/../../entities/universities.csv', 'r');
        $namesOfUniversities = [];
        while ($row = fgetcsv($fh, 0, ',', 'r')) {
            $namesOfUniversities[] = $row[1];
        }
        $id = ($id) ?? 1;
        $nameUni = $namesOfUniversities[$id]; // for harvard e.g.

        $wikiId = 'Q13371'; // harvard for example,  must be from db
        $linkToWiki = 'http://en.wikipedia.org/wiki/Harvard_University'; // // harvard for example,  must be from db
        $propertyWiki = 'P856';

        $websiteWiki = WebPage::getDataFromApi('https://www.wikidata.org/w/api.php', ['action' => 'wbgetclaims', 'entity' => $wikiId, 'property' => $propertyWiki, 'format' => 'json']);
        $websiteFromGoogleApi = WebPage::getDataFromApi('https://kgsearch.googleapis.com/v1/entities:search', ['query' => $nameUni, 'key' => $googleApiKey, 'types' => 'CollegeOrUniversity', 'limit' => 1]);

        $arrFromGoogleApi = json_decode($websiteFromGoogleApi);
        $websiteFromGoogleApi = $arrFromGoogleApi->itemListElement[0]->result->url ?? '';
        $arrFromWiki = json_decode($websiteWiki);
        $websiteWiki = $arrFromWiki->claims->$propertyWiki[0]->mainsnak->datavalue->value ?? '';
         $websiteFromDB = 'http://www.harvard.edu'; // for harvard

        if($needParser == 1) {
            $html = WebPage::get('https://www.google.ru/search', ['q' => $nameUni, 'gl => US', 'hl' => 'en'], [], false);
        }


        // old version
        return $this->render('test', compact(
            'html',
            'websiteFromDB',
            'websiteWiki',
            'nameUni',
            'linkToWiki',
            'websiteFromGoogleApi'));
    }


    public function actionComment()
    {
//        return $this->render('comment');
		for($i=0;$i<= 10000;$i++) {
			$t = $i++;
		}
		return $t;
    }

    /**
     * send the email
     *
     * @param $to_email
     * @return mixed
     * @throws \Exception
     */
    public function actionSend($to_email)
    {
        $to_client = Html::encode($to_email);
        $userName = Yii::$app->user->identity->email;  // get current auth user
        $password = $this->checkManagerPassword($userName); // get pass from 'config' for yandex email account
        if(!$password) throw new \Exception("для пользователя c email: {$userName}, не заданы config записи для подключения к почте.");

        $form = new EmailForm();
        if($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_emailService->send($form); // sending email
                sleep(3); // get pause - waiting to delivery
                $this->moveImapEmail($userName, $password); // move email from 'inbox' to 'sent'
                Yii::$app->session->setFlash('success', 'send successfully.');
            } catch(\Exception $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', 'there is some error... try again.');
            }
            return $this->refresh();
        }
        return $this->render('send_email', ['model' => $form, 'to_client' => $to_client]);
    }

    /**
     * grab emails from manager and save it to db
     */
    public function actionGrabEmails()
    {
        // prepare data
        $imapPath = Yii::$app->params['managers_imap_path'];
        $host = Yii::$app->params['managers_imap_host'];

        foreach (Yii::$app->params['managers_email'] as $userName => $password) {
            $inbox = imap_open($imapPath, $userName, $password) or die("Can't connect to mail boxes: " . imap_last_error());
            $mailboxes = imap_list($inbox, $host, '*');
            $inbox_sent = imap_open($mailboxes[0], $userName, $password) or die("Can't connect to mail boxes: " . imap_last_error());

            // set time to search...
            $lastTenMinutes = time() - 600; // last ten minutes
            $dateMin = date('j F Y', $lastTenMinutes);

            // search email's in 'inbox' & 'sent'
            $emails_inbox = imap_search($inbox, 'SINCE "' . $dateMin . '"');
            $emails_sent = imap_search($inbox_sent, 'SINCE "' . $dateMin . '"');

            $this->saveEmails($emails_inbox, $inbox); // save emails from 'inbox'
            $this->saveEmails($emails_sent, $inbox_sent); // save emails from 'sent' folder

            imap_close($inbox);
            imap_close($inbox_sent);
        }
        exit('done.');
    }

    /**
     * save emails to db
     *
     * @param $emails
     * @param $resource
     */
    public function saveEmails($emails, $resource)
    {
        if($emails) {
            foreach ($emails as $mail) {
                $headerInfo = imap_headerinfo($resource, $mail); // get email header
                $code = $headerInfo->message_id;
                $m = Email::findOne(['code' => $code]);
                if(!$m) {
                    // find the recipient & account
                    $user_email = $headerInfo->from[0]->mailbox . '@' . $headerInfo->from[0]->host;
                    $recipient_email = $headerInfo->to[0]->mailbox . '@' . $headerInfo->to[0]->host;
                    $account = UserAccount::findOne(['email' => $user_email]);
                    $recipient = UserAccount::findOne(['email' => $recipient_email]);

                    $obj = new Email();
                    $obj->to = $recipient_email;
                    $obj->from = $user_email;
                    $obj->reply_to = $headerInfo->reply_to[0]->mailbox . '@' .  $headerInfo->reply_to[0]->host;
                    $obj->cc = (isset($headerInfo->cc[0])) ? $headerInfo->cc[0]->mailbox . '@' . $headerInfo->cc[0]->host : '';
                    $obj->bcc = (isset($headerInfo->bcc[0])) ? $headerInfo->bcc[0]->mailbox . '@' . $headerInfo->bcc[0]->host : '';
                    $obj->subject = (isset($headerInfo->subject)) ? $this->getCleanStr($headerInfo->subject) : '';
                    $obj->code = $code;
                    $obj->html = $this->getHtmlBody($resource , $mail);
                    $obj->desc = $this->getCleanBody(imap_fetchbody($resource, $mail, 1));
                    $obj->account_id = $account->id ?? null;
                    $obj->recipient_id = $recipient->id ?? null;
                    $obj->save();
                }
            }
        }
    }

    /**
     * send email's through mailgun api
     *
     * @param $key
     * @param $url
     * @param bool|false $payloadData
     * @return string
     */
    public function sendEmailWithApi($key, $url, $payloadData = false)
    {
        if($payloadData) {
            $payload = http_build_query(['to' => [$payloadData]]);
            $method = 'POST';
        }
        $opts = [
            'http' => [
                'method' => $method ?? 'GET',
                'content' => (isset($payload) && $payload != false) ? $payload : false,
                'header' => implode("\r\n", [
                    'Authorization: Basic ' . base64_encode("api:$key"),
                    'Content-Type: application/x-www-form-urlencoded',
                ])
            ]
        ];
        $context = stream_context_create($opts);
        return file_get_contents($url, false, $context);
    }

    /**
     * get data from api mailgun response
     *
     * @param $api_key
     * @param $domain
     * @param $time
     * @return \stdClass
     */
    public function getApiResponse($api_key, $domain, $time)
    {
        $queryStr = [
            'begin' => "$time",
            'ascending' => 'yes', // get last record
            'limit' => 1,
            'pretty' => 'yes',
            'event' => 'delivered',
        ];
        $mailgun = new Mailgun($api_key);
        return $mailgun->get($domain . "/events", $queryStr);
    }

    /**
     * decode 'imap' response subject
     *
     * @param $str
     * @return string
     */
    public function getCleanStr($str)
    {
        if(stripos($str, '?UTF-8?B')) {
            $result = ( base64_decode( str_ireplace(['=?=', '=?UTF-8?B?'], '', $str) ) );
        } elseif(stripos($str, '?UTF-8?Q')) {
            $str = quoted_printable_decode( str_ireplace(['=?UTF-8?Q?','?= ', '?='], '', ($str)));
            $str = str_replace('_', ' ', $str);
        }
        return $result ?? $str;
    }

    /**
     * decode 'imap' response body
     *
     * @param $str
     * @return string
     */
    public function getCleanBody($str)
    {
        $str = trim(str_replace(['_'], '', strip_tags($str)));
        if(stripos($str, '=') != false) {
            $result = base64_decode($str);
        }
        if(preg_match("/=[A-Z][0-9|A-Z]=/mu", $str)) {
            $str = quoted_printable_decode($str);
        }
        return $result ?? $str;
    }


    /**
     * get html body of email
     *
     * @param $inbox
     * @param $email_number
     * @return mixed|string
     */
    public function getHtmlBody($inbox, $email_number)
    {
        $message = imap_fetchbody($inbox,$email_number,'2');
        $html_body = quoted_printable_decode($message);
        $html_body = mb_convert_encoding($html_body, 'utf-8', mb_detect_encoding($html_body));
        $html_body = mb_convert_encoding($html_body, 'html-entities', 'utf-8');

        if(!$html_body){
            $message = imap_fetchbody($inbox,$email_number,2);
            $html_body = quoted_printable_decode($message);
            $html_body = mb_convert_encoding($html_body, 'utf-8', mb_detect_encoding($html_body));
            $html_body = mb_convert_encoding($html_body, 'html-entities', 'utf-8');
        }
        return $html_body;
    }

    /**
     * check if we have in config manager data for email sending
     *
     * @param $managerEmail
     * @return bool
     */
    public function checkManagerPassword($managerEmail)
    {
        if(array_search($managerEmail, array_keys(Yii::$app->params['managers_email'])) !== false) {
            return Yii::$app->params['managers_email'][$managerEmail];
        }
        return false;
    }

    /**
     * move email through imap to 'sent' folder
     *
     * @param $userName
     * @param $password
     */
    public function moveImapEmail($userName, $password)
    {
        // get value of 'sent' box
        $inbox = imap_open(Yii::$app->params['managers_imap_path'], $userName, $password) or die("Can't connect to mail: " . imap_last_error());
        $mailboxes = imap_list($inbox, Yii::$app->params['managers_imap_host'], '*');
        imap_close($inbox);

        // move mail from inbox to sent
        $inboxIncome = imap_open(Yii::$app->params['managers_imap_path'], $userName, $password) or die("Can't connect to mail: " . imap_last_error());
        preg_match('#.+ssl}(.+)?#ui', ($mailboxes[0]), $matches);
        @imap_mail_move($inboxIncome, '*', $matches[1]);
        imap_close($inboxIncome, CL_EXPUNGE);
    }


    public function actionTech()
    {
        $this->layout = 'main-login';
//        $gsm = new Gsm();
//        $data = $gsm->search('iphone x'); // Keyword
//        print_r($data);
//        exit('exit');

        $url = 'https://www.gsmarena.com/apple_iphone_x-8858.php';
        $url2 = 'https://www.gsmarena.com/lg_v30s_thinq-9090.php';
        $url_nokia = 'https://www.gsmarena.com/nokia_3210-6.php';
        $url_xiaomi = 'https://www.gsmarena.com/xiaomi_mi_mix_2s-9067.php';
        $url_redmi_note = 'https://www.gsmarena.com/xiaomi_redmi_note_5_pro-8893.php';
        $url_huawei = 'https://www.gsmarena.com/huawei_honor_9_lite-8962.php';

        $url_turkish = 'https://www.epey.com/akilli-telefonlar/apple-iphone-x.html';
        $url_turkish_xiaomi = 'https://www.epey.com/akilli-telefonlar/xiaomi-mi-mix-18k.html';

//        $response = WebPage::getDataFromApi($url_turkish_xiaomi);

//       return $this->render('tech');
//       return $this->render('tech_run');
//       return $this->render('import_product');
//		exit('test');
//       return $this->render('non_english');
//		 return $this->render('epey_draft');
//		 return $this->render('cnet_draft');
//		 return $this->render('yandex_draft');
//		 return $this->render('amazon_draft');
		 return $this->render('pleer_draft');
//         return $this->render('epey');

    }
}