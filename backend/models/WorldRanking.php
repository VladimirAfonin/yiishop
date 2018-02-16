<?php

namespace app\models;

use backend\shop\RankingInterface;
use Yii;

/**
 * This is the model class for table "world_ranking".
 *
 * @property integer $id
 * @property integer $nid
 * @property string $name
 * @property integer $rank_display
 * @property string $country
 * @property integer $stars
 * @property string $region
 * @property double $score
 */
class WorldRanking extends \yii\db\ActiveRecord implements RankingInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'world_ranking';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nid', 'rank_display', 'stars'], 'integer'],
            [['name', 'country', 'region'], 'string'],
            [['score'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nid' => 'Nid',
            'name' => 'Name',
            'rank_display' => 'Rank Display',
            'country' => 'Country',
            'stars' => 'Stars',
            'region' => 'Region',
            'score' => 'Score',
        ];
    }
}
