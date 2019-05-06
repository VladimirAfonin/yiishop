<? namespace backend\models;

use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $discovery
 * @property string $code
 * @property string $to
 * @property string $from
 * @property string $reply_to
 * @property string $cc
 * @property string $bcc
 * @property string $language_id
 * @property string $subject
 * @property string $desc
 * @property string $html
 * @property string $source
 * @property integer $account_id
 * @property integer $recipient_id
 * @property integer $author_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $folder
 */
class Email extends ActiveRecord
{
	public static function tableName(){return 'user_email';}

	/**
	 * @return array
     */
	public function scenarios()
	{
		$scenarios = parent::scenarios();
		$scenarios['send_email'] = ['subject', 'to', 'html', 'desc', 'reply_to', 'cc', 'bcc'];
		return $scenarios;
	}

	public function rules()
	{
		return [
			[['desc', 'html', 'subject', 'reply_to', 'to'], 'required'],
            [['discovery', 'account_id', 'recipient_id', 'author_id'], 'integer'],
            [['desc', 'html', 'source','folder'], 'string'],
            [['created_at','updated_at'], 'safe'],
            [['code', 'from', 'subject', 'folder'], 'string', 'max' => 255],
            [['language_id'], 'string', 'max' => 8],
			[['to', 'reply_to','cc', 'bcc' ], 'email'],
        ];
	}

	/* @return User */ public function getAccount()  {return $this->hasOne(User::className(), ['id' => 'account_id']);}
	/* @return User */ public function getAuthor()   {return $this->hasOne(User::className(), ['id' => 'author_id']);}
	/* @return User */ public function getRecipient(){return $this->hasOne(User::className(), ['id' => 'recipient_id']);}

	public function behaviors()
	{
		return [
//			['class' => TimestampBehavior::className(),'value' => new Expression('NOW()'),'updatedAtAttribute'=>false], // todo
			['class' => TimestampBehavior::className(),'value' => new Expression('NOW()'),'createdAtAttribute'=>false],
			['class' => BlameableBehavior::className(),'createdByAttribute' => 'author_id', 'updatedByAttribute' => false]
		];
	}

	public function attributeLabels()
	{
		/*
		return [
				'subject' => 'Тема письма',
				'desc' => 'Текстовая версия',
				'html' => 'Html версия',
				'cc' => 'Копия',
				'bcc' => 'Скрытая копия',
				'to' => 'Кому',
		];
		*/
	}

}
