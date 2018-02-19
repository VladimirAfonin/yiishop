<?php
namespace backend\entities;

use Yii;
use app\models\MainRankings;
use app\models\UniversitySubjectRankings;

class RankingHelper
{
    /**
     * get main rating
     * @param array $arrRankings
     */
    public static function runGetMainRatings(array $arrRankings = [])
    {
        foreach ($arrRankings as $k => $value) {
            $univRank = self::createDataFile("$k"."_r", self::getItemUrl("$k")['r'], self::getItemUrl("$k")['base']);
            $univIndicators = self::createDataFile("$k"."_i", self::getItemUrl($k)['i']);
            self::saveToDb($univRank, $value);
            sleep(1);
        }
    }

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
     * save info to db
     *
     * @param $data
     * @param string $rankingName
     */
    public static function saveToDb($data, $rankingName)
    {
        $year = $data['year'];
        $data = $data['data'];
        foreach ($data as $item) {
            $ranking = MainRankings::findOne(['nid' => $item['nid']]);
            if ( ! $ranking) {
                $m = new MainRankings();
                $m->nid = $item['nid'];
                $m->name = $item['title'];
                $m->$rankingName = $item['rank_display'];
                $m->year = $year;
                $m->save(false);
            } else {
                $ranking->$rankingName = $item['rank_display'];
                $ranking->save(false);
            }
        }
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

    /**
     * @param $data
     * @param $field
     */
    public static function updateToDbWithSubject($data, $field)
    {
        $year = $data['year'];
        $finalData = $data['data'];
        foreach ($finalData as $item) {
            $obj = UniversitySubjectRankings::find()->where(['core_id' => $item['core_id']])->one();
            if($obj) {
                $obj->$field = $item['rank_display'];
                $obj->save(false);
            } else {
                $obj = self::addItemsToDb($item, $year);
                $obj->$field = $item['rank_display'];
                $obj->save(false);
            }
        }
    }

    /**
     * @param $data
     * @param $year
     * @return \UniversitySubjectRankings
     */
    public static function addItemsToDb($data, $year)
    {
        $m = new UniversitySubjectRankings();
        $m->nid = $data['nid'];
        $m->name = $data['title'];
        $m->country = $data['country'];
        $m->core_id = $data['core_id'];
        $m->year = $year;
        $m->save(false);
        return $m;
    }

    /**
     * generate summary array with data for
     * one university
     *
     * @param $html
     * @param $url
     * @return array
     */
    public static function generateResultData($html, $url)
    {
        $htmlDom = WebPage::dom($html);

        $resultArr = [];

        // get url
        $resultArr['site_url'] = $url;

        // get criteria #1
        $criteria_1 = $htmlDom->query('//div[@class="criteria"]/text()')->item(0)->nodeValue ?? null;
        if (isset($criteria_1) && (!is_null($criteria_1))) {
            $criteria_1 = strtolower(str_replace(' ', '_', $criteria_1));
            $value_1 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(0)->nodeValue;
            $resultArr[$criteria_1] = $value_1;
        }

        // get criteria #2
        $criteria_2 = $htmlDom->query('//div[@class="criteria"]/text()')->item(1)->nodeValue ?? null;
        if (isset($criteria_2) && (!is_null($criteria_2))) {
            $criteria_2 = strtolower(str_replace(' ', '_', $criteria_2));
            $value_2 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(1)->nodeValue ?? null;
            $resultArr[$criteria_2] = $value_2;
        }

        // get criteria #3
        $criteria_3 = $htmlDom->query('//div[@class="criteria"]/text()')->item(2)->nodeValue ?? null;
        if(isset($criteria_3) && (!is_null($criteria_3))) {
            $criteria_3 = strtolower(str_replace(' ', '_', $criteria_3));
            $value_3 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(2)->nodeValue ?? null;
            $resultArr[$criteria_3] = $value_3;
        }

        // get criteria #4
        $criteria_4 = $htmlDom->query('//div[@class="criteria"]/text()')->item(3)->nodeValue ?? null;
        if(isset($criteria_4) && (!is_null($criteria_4))) {
            $criteria_4 = strtolower(str_replace(' ', '_', $criteria_4));
            $value_4 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(3)->nodeValue ?? null;
            $resultArr[$criteria_4] = $value_4;
        }

        // get criteria #5
        $criteria_5 = $htmlDom->query('//div[@class="criteria"]/text()')->item(4)->nodeValue ?? null;
        if(isset($criteria_5) && (!is_null($criteria_5))) {
            $criteria_5 = strtolower(str_replace(' ', '_', $criteria_5));
            $value_5 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(4)->nodeValue ?? null;
            $resultArr[$criteria_5] = $value_5;
        }

        // get criteria #6
        $criteria_6 = $htmlDom->query('//div[@class="criteria"]/text()')->item(5)->nodeValue ?? null;
        if(isset($criteria_6) && (!is_null($criteria_6))) {
            $criteria_6 = strtolower(str_replace(' ', '_', $criteria_6));
            $value_6 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(5)->nodeValue ?? null;
            $resultArr[$criteria_6] = $value_6;
        }

        // get criteria #7
        $criteria_7 = $htmlDom->query('//div[@class="criteria"]/text()')->item(6)->nodeValue ?? null;
        if (isset($criteria_7) && (!is_null($criteria_7))) {
            $criteria_7 = strtolower(str_replace(' ', '_', $criteria_7));
            $value_7 = $htmlDom->query('//div[@class="bcl"]/div[@class="text"]/text()')->item(6)->nodeValue ?? null;
            $resultArr[$criteria_7] = $value_7;
        }

        // get number of academic faculty staff
        $resultArr['academic_faculty_staff_total'] = $htmlDom->query('//div[@class="total faculty"]/div[@class="text"]/div[@class="number"]/text()')->item(0)->nodeValue ?? null;
        $resultArr['academic_faculty_staff_inter'] = $htmlDom->query('//div[@class="inter faculty"]/div[@class="text"]/div[@class="number"]/text()')->item(0)->nodeValue ?? null;
        // get number of students
        $resultArr['students_info']['domestic']['number_students'] = $htmlDom->query('//div[@class="total student"]/div[@class="barw"]/div[@class="barbg"]/div[@class="barp progress-bar-info"]/div[@class="number"]/text()')->item(0)->nodeValue ?? null;
        $resultArr['students_info']['domestic']['number_students_postgraduate'] = $htmlDom->query('//div[@class="total student"]/div[@class="cirlce clearfix"]/div[@class="stat"]/div[@class="post"]/span[@class="perc"]/text()')->item(0)->nodeValue ?? null;
        $resultArr['students_info']['domestic']['number_students_undergraduate'] = $number_students_undergraduate = $htmlDom->query('//div[@class="total student"]/div[@class="cirlce clearfix"]/div[@class="stat"]/div[@class="grad"]/span[@class="perc"]/text()')->item(0)->nodeValue ?? null;
        // get number of international students
        $resultArr['students_info']['international']['number_students'] = $htmlDom->query('//div[@class="total inter"]/div[@class="barw"]/div[@class="barbg"]/div[@class="barp progress-bar-info"]/div[@class="number"]/text()')->item(0)->nodeValue ?? null;
        $resultArr['students_info']['international']['number_students_postgraduate'] = $htmlDom->query('//div[@class="total inter"]/div[@class="cirlce clearfix"]/div[@class="stat"]/div[@class="post"]/span[@class="perc"]/text()')->item(0)->nodeValue ?? null;
        $resultArr['students_info']['international']['number_students_undergraduate'] = $number_students_undergraduate = $htmlDom->query('//div[@class="total inter"]/div[@class="cirlce clearfix"]/div[@class="stat"]/div[@class="grad"]/span[@class="perc"]/text()')->item(0)->nodeValue ?? null;

        return $resultArr;
    }
}