<?php
namespace shop\forms\auth;

use yii\base\Model;

class EmailForm extends Model
{
    public $name;
    public $text_body;
    public $html_body;
    public $reply_to;
    public $set_bcc;
    public $set_cc;
    public $subject;
    public $client_email;

    public function rules()
    {
        return [
            [['subject', 'client_email', 'html_body', 'text_body', 'reply_to'], 'required'],
            [['client_email', 'reply_to', 'set_cc', 'set_bcc'], 'email'],
            [['subject'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'subject' => 'Тема письма',
            'client_email' => 'Кому',
            'set_bcc' => 'Скрытая копия',
            'set_cc' => 'Копия'
        ];
    }
}
