<?
namespace backend\entities;

use DOMDocument;
use DOMXPath;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\db\ActiveRecord;

/**
 * @property string $id
 * @property string $hl #language code: en,ru
 * @property string $node #node name: Country/City/Course/School/Uni
 * @property integer $nid #node id from table
 * @property integer $source #source name: google/wikipedia/geonames
 * @property string $name #html title
 * @property string $path #url without ignore attributes
 * @property string $http_code #curl response code
 * @property string $desc #responce
 * @property string $proxy_list
 */
class WebRankingHelper
{
    /**
     * for QS ranking
     *
     * @param $url
     * @return mixed
     */
    public static function getRequestToUrl($url)
    {
        $userAgent = file('../entities/user_agents_list.txt');
        $userAgentsCount = count($userAgent) - 1;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $code = $userAgent[rand(0, $userAgentsCount)]);
        curl_setopt($ch, CURLOPT_HEADER, 0); // 1
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // added
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // added
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
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

    /**
     * @param $content
     * @return DOMXPath
     */
    public static function dom($content): DOMXPath
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        return new DOMXpath($doc);
    }
}
