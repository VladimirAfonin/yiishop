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
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function isWebsiteInfoEqual($haystack, $needle)
    {
        return (strpos($haystack, $needle) === false ? 0 : 1);
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