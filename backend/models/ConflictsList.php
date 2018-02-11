<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "conflicts_list".
 *
 * @property integer $id
 * @property string $name
 * @property string $link_wiki
 * @property string $wiki_website
 * @property string $db_website
 * @property string $google_website
 */
class ConflictsList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'conflicts_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'wiki_website', 'db_website', 'google_website'], 'string'],
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
            'wiki_website' => 'Wiki Website',
            'db_website' => 'Db Website',
            'google_website' => 'Google Website',
        ];
    }
}
