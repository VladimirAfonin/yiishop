<?php
/**
 * Created by PhpStorm.
 * User: Maniac
 * Date: 24.06.2018
 * Time: 17:54
 */

namespace console\models;
use yii\db\ActiveRecord;

class User extends ActiveRecord
{
    public static function tableName()
    {
        return 'test_user';
    }

    public function rules()
    {
        return [
            [['username','email'],'required'],
            [['username','email'],'unique'],
            [['username','email'],'string','max' => 255],
            ['username','match','pattern' => '#^[0-9a-z_-]+$#i'],
            [['email'],'email'],
        ];
    }
}