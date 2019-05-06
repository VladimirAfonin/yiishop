<?php
namespace backend\entities;

use Yii;
use app\models\MainRankings;
use app\models\UniversitySubjectRankings;

class RankingHelper
{
    /**
     * create json file with all data
     * @param $name
     * @param $url
     * @param string $baseUrl
     * @return mixed
     */
    public static function createDataFile($name, $url, $baseUrl = '')
    {
        $year = self::getYear($baseUrl);
        if(!file_exists("rankings/$name.json")) {
            $univIndicators = WebPage::getRequestToUrl($url);
            $fp = fopen("rankings/$name.json", "w");
            fwrite($fp, $univIndicators);
            fclose($fp);
        }
        $univIndicators = json_decode(file_get_contents("rankings/$name.json"), true);
        $univIndicators['year'] = $year;
        return $univIndicators;
    }

    /**
     * get year from url
     * @param $url
     * @return null
     */
    public static function getYear($url)
    {
        preg_match('#[0-9]{4}?#ui', $url, $matches);
        if ( ! empty($matches[0])) {
            $year = $matches[0];
        }
        return $year ?? null;
    }

    /**
     * get url for some rating for json response
     * 'r' - rating response, 'i' - indicator response
     * @param $key
     * @return mixed
     */
    public static function getItemUrl($key = false)
    {
        $urlsOfRanking = Yii::$app->params['urlsOfRanking'];
        if($key != false) {
            return $urlsOfRanking[$key];
        }
        return $urlsOfRanking;
    }

    /**
     * save to result array
     *
     * @param $data
     * @param $rankingName
     * @return array
     */
    public static function saveToArr($data, $rankingName)
    {
        $year = $data['year'];
        $data = $data['data'];

        foreach ($data as $item) {
            $result[] = [
                $rankingName => [
                    $item['nid'] => [
                        $item['title'] => [
                            'rank_display' => $item['rank_display'],
                            'year' => $year
                        ]
                    ]
                ]

            ];
        }
        return $result;
    }

    /**
     * get url for sub rankings
     * @param bool|false $key
     * @return array
     */
    public  static function getItemSubUrl($key = false)
    {
        $arr = Yii::$app->params['urlsOfSubjectRanking'];
        if($key != false) {
            return $arr[$key];
        } else {
            return $arr;
        }
    }

}