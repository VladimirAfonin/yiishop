<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "main_rankings".
 *
 * @property integer $id
 * @property integer $nid
 * @property string $name
 * @property integer $rank_display
 * @property string $world_ranking
 * @property string $mba_ranking
 * @property string $employability_ranking
 * @property string $business_ratings
 * @property string $business_finance_ratings
 * @property string $business_managment_ratings
 * @property string $year
 */
class MainRankings extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'main_rankings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nid', 'rank_display'], 'integer'],
            [['name', 'world_ranking', 'mba_ranking', 'employability_ranking', 'business_ratings', 'business_finance_ratings', 'business_managment_ratings', 'year'], 'string'],
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
            'world_ranking' => 'World Ranking',
            'mba_ranking' => 'Mba Ranking',
            'employability_ranking' => 'Employability Ranking',
            'business_ratings' => 'Business Ratings',
            'business_finance_ratings' => 'Business Finance Ratings',
            'business_managment_ratings' => 'Business Managment Ratings',
            'year' => 'Year',
        ];
    }
}
