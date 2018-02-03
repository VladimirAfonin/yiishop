<?php
namespace backend\entities;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "proxy_list".
 *
 * @property integer $id
 * @property string $address
 * @property integer $created_at
 */
class ProxyList extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proxy_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'created_at'], 'required'],
            [['address'], 'string'],
            [['created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Address',
            'created_at' => 'Created At',
        ];
    }
}