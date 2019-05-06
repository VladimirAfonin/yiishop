<?php

namespace frontend\entities;

use Yii;

/**
 * This is the model class for table "test_user".
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $desc
 */
class TestUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'test_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['desc'], 'string'],
            [['username', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'desc' => 'Desc',
        ];
    }
}
