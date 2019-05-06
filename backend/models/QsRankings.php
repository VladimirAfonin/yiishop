<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "qs_rankings".
 *
 * @property integer $id
 * @property string $name
 * @property integer $nid
 * @property string $data
 */
class QsRankings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qs_rankings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'data'], 'string'],
            [['nid'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'nid' => 'Nid',
            'data' => 'Data',
        ];
    }
}
