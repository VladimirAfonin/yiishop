<?php namespace common\services;

use yii\mail\MailerInterface;
use backend\models\Email;
use yii\helpers\ArrayHelper;
use backend\models\User;
use Mailgun\Mailgun;
use Yii;

class EmailService
{
    private $mailer;
    private $supportEmail;
    private $_auth = false;

    const DEFAULT_ACCOUNT = 1;

    public function __construct($supportEmail, MailerInterface $mailer)
    {
        $this->supportEmail = $supportEmail;
        $this->mailer = $mailer;
    }

    public function send(Email $form)
    {
        $cc = (!empty($form->cc)) ? $form->cc : null;
        $bcc = (!empty($form->bcc)) ? $form->bcc : null;

        $sent = $this->mailer
            ->compose()
            ->setFrom($this->supportEmail)
            ->setTo([$form->to])
            ->setSubject($form->subject)
            ->setTextBody($form->desc)
            ->setHtmlBody($form->html)
            ->setReplyTo($form->reply_to)
            ->setCc($cc)
            ->setBcc($bcc);

        $emailMessage = $sent->toString();
        $sent->send();

        if(!$sent) { throw new \RuntimeException('sending email error.'); }
        return $emailMessage;
    }

    /**
     * save emails to db
     *
     * @param $emails
     * @param $folder
     * @param $resource
     */
    public function save($emails, $resource, $folder)
    {
        if($emails) {
            foreach ($emails as $mail) {
                $headerInfo = imap_headerinfo($resource, $mail); // get email's headers

                $code = $headerInfo->message_id;
                $m = Email::findOne(['code' => $code]);
                if(!$m) {
                    // find the recipient & account
                    $user_email = $this->itemFromHeader($headerInfo, 'from');
                    $recipient_email = $this->itemFromHeader($headerInfo, 'to');
                    $account = User::findOne(['email' => $user_email]);
                    $recipient = User::findOne(['email' => $recipient_email]);

                    $obj = new Email();
                    $obj->to = $recipient_email;
                    $obj->from = $user_email;
                    $obj->reply_to = $this->itemFromHeader($headerInfo, 'reply_to');
                    $obj->cc =  $this->itemFromHeader($headerInfo, 'cc');
                    $obj->bcc = $this->itemFromHeader($headerInfo, 'bcc');
                    $obj->subject = (isset($headerInfo->subject)) ? $this->getCleanStr($headerInfo->subject) : '';
                    $obj->code = $code;
                    $obj->html = $this->getHtmlBody($resource , $mail);
                    $obj->desc = $this->getCleanBody(imap_fetchbody($resource, $mail, 1));
                    $obj->account_id = $account->id ?? null;
                    $obj->recipient_id = $recipient->id ?? null;
//                    $obj->mail_date = gmdate("Y-m-d h:i:s", $headerInfo->udate); // date from email header in UTC
                    $obj->created_at = gmdate("Y-m-d h:i:s", $headerInfo->udate); // date from email header in UTC
                    $obj->discovery = $this->getDiscovery($folder);
                    $obj->folder = $folder;
                    $obj->save(/*false*/);
                }
            }
        }
    }

    /**
     * get discovery field value
     *
     * @param $flag
     * @return int
     */
    public function getDiscovery($flag)
    {
        switch($flag) {
            case 'deleted':
                return 1;
            default:
                return 5;
        }
    }

    /**
     * send email's with mailgun api
     *
     * @param $key
     * @param $url
     * @param bool|false $payloadData
     * @return string
     */
    public function sendWithApi($key, $url, $payloadData = false)
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
     * check if user has email account
     *
     * @return mixed
     */
    public function auth()
    {
        $user_id = Yii::$app->user->identity->getId();
        if ( ! isset(Yii::$app->params['accounts'][$user_id])) {
            return Yii::$app->params['accounts'][self::DEFAULT_ACCOUNT];
        }
        return Yii::$app->params['accounts'][$user_id];
    }

    /**
     * append email with imap to 'sent' folder
     *
     * @param $emailMessage
     */
    public function appendImapEmail($emailMessage)
    {
        $this->getAuth(); // get account data

        // get value of 'sent' box
        $inbox = imap_open($this->_auth['folder']['inbox'], $this->_auth['email'], $this->_auth['password']) or die("Can't connect to mail: " . imap_last_error());
        $mailboxes = imap_list($inbox, $this->_auth['host'], '*');
        imap_close($inbox);

        // append mail to sent
        $inboxIncome = imap_open($this->_auth['folder']['inbox'], $this->_auth['email'], $this->_auth['password']) or die("Can't connect to mail: " . imap_last_error());
        @imap_append($inboxIncome, $mailboxes[0], $emailMessage);
        imap_close($inboxIncome, CL_EXPUNGE);
    }

    /**
     * @return bool|mixed
     */
    public function getAuth()
    {
        if ( ! $this->_auth) {
            $this->_auth = $this->auth();
        }
        return $this->_auth;
    }

    /**
     * get name of folder
     *
     * @param $index
     * @return null|string
     */
    public function getFolder($index)
    {
        $folders = Yii::$app->params['email_folders']['yandex'];
        return ArrayHelper::getValue($folders, $index);
    }

    /**
     * get detail info about item from headers
     *
     * @param $headerInfo
     * @param  $item
     * @return string
     */
    public function itemFromHeader($headerInfo, string $item)
    {
        return (isset($headerInfo->$item[0])) ? $headerInfo->$item[0]->mailbox . '@' . $headerInfo->$item[0]->host : '';
    }
}