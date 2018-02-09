<?php

namespace backend\entities;

use yii\base\WidgetEvent;

class WebPageHelper
{
    /**
     * add detail info to summary data[]
     *
     * @param array $data
     * @param string|string $key
     * @param string|string $method_name
     * @param bool|false $params
     * @return null
     */
    public static function addDetailInfo(array $data, string $key, string $method_name, $params = false)
    {
        if(method_exists(new self, $method_name)) {
            return (isset($data[$key])) ? WebPageHelper::$method_name($data[$key], $params) : null;
        }
        return null;
    }

    /**
     * compare 'website' info:
     * db against parsing
     *
     * @param $websiteGoogle
     * @param $websiteFromDB
     * @param $websiteWiki
     * @return bool
     */
    public static function isWebsiteInfoEqual($websiteGoogle, $websiteFromDB, $websiteWiki)
    {
        $websiteGoogle = self::getBaseUrl($websiteGoogle);
        $websiteFromDB = self::getBaseUrl($websiteFromDB);
        $websiteWiki = self::getBaseUrl($websiteWiki);

        similar_text($websiteGoogle, $websiteFromDB, $percent1);
        similar_text($websiteGoogle, $websiteWiki, $percent2);
        similar_text($websiteFromDB, $websiteWiki, $percent3);
        $summaryResult = round(($percent1 + $percent2 + $percent3) / 3, 3);
        return $summaryResult;
    }

    /**
     * cut prefix of url
     *
     * @param string $link
     * @return string
     */
    public static function getBaseUrl(string $link): string
    {
        $prefixes = self::prefixesArray();
        $url =  preg_replace("/((http(s)?\:\/\/)((www\.)?(.+?)))\/(.+?)?$/", '$6', $link);
        $url =  preg_replace("/((http(s)?\:\/\/)((www\.)?(.+)))$/", '$6', $url);
        $parts = explode('.', $url);
        if (count($parts) > 2) {
            foreach ($prefixes as $prefix) {
                if (substr($url, 0, strlen($prefix)) == $prefix) {
                    $newUrl = substr($url, strlen($prefix));
                }
            }
        }
        return $newUrl ?? $url;
    }

    /**
     * @return array
     */
    public static function prefixesArray()
    {
        return [
            'www-en.',
            'welcome.',
            'www-eng.',
            'ewww.',
            'wwww.',
            'www.en.',
            'w.',
            'wp.',
            'ww.',
            'wwwp.',
            'www.',
            'www1.',
            'ww1.',
            'www2.',
            'ww2.',
            'www3.',
            'www4.',
            'www5.',
            'www6.',
            'www7.',
            'www8.',
            'www9.',
            'www10.',
            'www11.',
            'www12.',
            'www13.',
            'www14.',
            'www15.',
            'www16.',
            'www17.',
            'www18.',
            'www19.',
            'www20.',
            'w3.',
            'ww3.',
            'www4.',
            'ww4.',
            'www5.',
            'www6.',
            'www10',
            'wwwen.',
            'webs.',
            'web.',
            'int.',
            'internacional.',
            'international.',
            'inter.',
            'university.',
            'intraweb.',
            'staryweb.',
            'about.',
            'global.',
            'portal.',
            'portale.',
            'portail.',
            'portale.',
            'portal2.',
            'portal3.',
            'portal4.',
            '2014.',
            'v.',
            'v1.',
            'v2.',
            'v3.',
            'v4.',
            'public.',
            'cms1.',
            'cms2.',
            'cms3.',
            'cms4.',
            'cms5.',
            'old.',
            'new.',
            'site.',
            'website.',
            'english.',
            'englishweb.',
            'home.',
            'sites.',
            'site.',
            'cms.',
            'wiz.',
            'homepage.',
            'eweb.',
            'eng.',
            'en.',
            'in.',
            'dl.',
            'a.',
            'ou.',
            'e.',
        ];
    }

    /**
     * get final 'website' info
     *
     * @param $matches
     * @return string
     */
    public static function getFinalWebsiteInfo($matches)
    {
        return (isset($matches[0])) ? substr($matches[0], 0, -3) : 'not_found';
    }

    /**
     * detail info for budget
     *
     * @param $arr
     * @param $params
     * @return array
     */
    public static function detailBudget($arr, $params)
    {
        $arr = array_diff($arr, ['', ' ']);
        $str = implode(' ', $arr);
        preg_match('#\(?[0-9]{4}\)?#', $str, $matches);
        if(count($arr) == 2 && (empty($matches))) {
            $params = ['value'];
            $res = array_combine($params, [$str]);
        } else {
            $res = array_combine($params, array_values($arr));
        }

        $res['year'] = (isset($res['year'])) ? str_replace(['(', ')', ','], '', $res['year'] ) : null;
        return $res;
    }

    /**
     * prepare ACT score data[]
     *
     * @param array $arr
     * @return array
     */
    public static function detailItemScoreACT(array $arr)
    {
        $arr = array_slice($arr, 1);
        $keys = ['value', 'year'];
        $res = array_combine($keys, array_values($arr));
        $res['year'] = str_replace(['(', ')'], '', $res['year'] );
        $res['value'] = str_replace('-', ',', $res['value']);
        return $res;
    }

    /**
     * get detail
     *
     * @param array $arr
     * @param array $params
     * @return array
     */
    public static function detailResultItemInfo(array $arr, array $params = [])
    {
        $arr = array_diff($arr,['', ' ']);
        $res = array_combine($params, array_values($arr));
        $res['year'] = (isset($res['year'])) ? str_replace(['(', ')', ','], '', $res['year'] ) : null;
        return $res;
    }

    /**
     * get detail for more than one tuition and fees
     *
     * @param $arr
     * @return array
     */
    public static function detailTuitionFees($arr)
    {
        $summaryArr = [];
        $params = ['value', 'currency', 'year'];
        foreach ($arr as $k => $item) {
            $res = array_combine($params, array_values($item));
            $res['year'] = str_replace(['(', ')', ','], '', $res['year'] );
            $summaryArr[$k] = $res;
        }
        return $summaryArr;
    }

    /**
     * get detail for one tuition and fees
     *
     * @param $arr
     * @param $params
     * @return array
     */
    public static function detailOneTuitionFees($arr, $params)
    {
        if (count($arr) == 1) {
            $res = [];
            foreach ($arr as $k => $item) {
                $res[] = $item;
            }
            $res = array_combine($params, array_values($res[0]));
        } else {
            $arr = array_diff($arr, ['']);
            $res = array_combine($params, array_values($arr));
        }
        $res['year'] = (isset($res['year'])) ? str_replace(['(', ')', ','], '', $res['year']) : null;

        return $res;
    }

    /**
     * helper function for testing
     *
     * @param $str
     */
    public static function prettyPrint($str)
    {
        echo '<pre>';
        print_r($str);
        echo '</pre>';
    }

    /**
     * prepare 'detail item score' data[]
     * for search item
     *
     * @param array $arr
     * @return array
     */
    public static function detailItemScoreSAT(array $arr)
    {
        foreach ($arr as $k => $item) {
            $arr[$k][$item[1]] = [
                'value' => str_replace('-', ',',  $item[2]),
                'year' => str_replace(['(', ')'], '', $item[3])
            ];
        }

        // get dimension of array '-1'
        $resultArr = [];
        foreach ($arr as $subArr) {
            $resultArr = array_merge($resultArr, $subArr);
        }

        // get result array
        foreach ($resultArr as $k => $item) {
            if (!is_string($k)) {
                unset($resultArr[$k]);
            }
        }
        return $resultArr;
    }

}