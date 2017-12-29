<?php
namespace shop\entities;


use lhs\Yii2SaveRelationsBehavior\SaveRelationsBehavior;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use shop\entities\InstantiateTrait;
use yii\db\ActiveQuery;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
//    use InstantiateTrait;

    const STATUS_WAIT = 0;
    const STATUS_ACTIVE = 10;


    /*public function __construct(string $username = '', string $email = '', string $password = '')
    {
        $this->username = $username;
        $this->email = $email;
        $this->setPassword($password);
        $this->created_at = time();
        $this->status = self::STATUS_ACTIVE;
        $this->generateAuthKey();
        parent::__construct();
    }*/

    /**
     * create new user
     *
     * @param string $username
     * @param string $email
     * @param string $password
     * @return User
     */
    public static function create(string $username = '', string $email = '', string $password = ''): self
    {
        // self - не учитывает наследование, static - учитываем наследование
        $user = new static;
        $user->username = $username;
        $user->email = $email;
        $user->setPassword($password);
        $user->created_at = time();
        $user->status = self::STATUS_WAIT;
        $user->generateConfirmEmailToken();
        $user->generateAuthKey();
        return $user;
    }

    /**
     * @return ActiveQuery
     */
    public function getNetworks(): ActiveQuery
    {
        return $this->hasMany(Network::className(), ['user_id' => 'id']);
    }

    /**
     * sign up with networks
     *
     * @param $network
     * @param $identity
     * @return User
     */
    public static function signupByNetwork($network, $identity): self
    {
        $user = new User();
        $user->created_at = time();
        $user->status = self::STATUS_ACTIVE;
        $user->generateAuthKey();
        $user->networks = [ Network::create($network, $identity) ];
        return $user;
    }

    public function attachNetwork($network, $identity): void
    {
        // got related current network's
        $networks = $this->networks;
        foreach($networks as $network) {
            if($network->isFor($network, $identity)) {
                throw new \RuntimeException('network is already attached.');
            }
        }
        // add another one row with some socialnet
        $networks[] =  Network::create($network, $identity);
        $this->networks = $network;
    }

    /**
     * confirm sign up into email
     */
    public function confirmSignup()
    {
        // if user doesnt wait - if he is already active.
        if(!$this->isWait()) { throw new \RuntimeException('user is already active.'); }

        $this->status = self::STATUS_ACTIVE;
        $this->removeEmailConfirmToken();
    }

    /**
     * is user waiting
     * @return bool|bool
     */
    public function isWait(): bool
    {
        return $this->status === self::STATUS_WAIT;
    }

    /**
     *  generate new password reset token
     */
    private function generateConfirmEmailToken()
    {
        $this->email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     *  remove email confirm token
     */
    private function removeEmailConfirmToken()
    {
        $this->email_confirm_token = null;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            [
                'class' => SaveRelationsBehavior::className(),
                'relations' => ['networks'] // -> getNetworks()
            ]
        ];
    }

    /**
     * if we have 'SaveRelationsBehavior',
     * we must use 'transactions'
     *
     * @return array
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_WAIT]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     *
     */
    public function requestPasswordReset(): void
    {
        if(!empty($this->password_reset_token) && self::isPasswordResetTokenValid($this->password_reset_token)) {
            throw new \RuntimeException('password resetting is already requested.');
        }
        $this->password_reset_token = Yii::$app->security->generateRandomString(). '_' . time();
    }

    /**
     * @param $password
     */
    public function resetPassword($password): void
    {
        if(empty($this->password_reset_token)) { throw new \RuntimeException('password resetting is not requested.'); }
        $this->setPassword($password);
        $this->password_reset_token = null;
    }
}
