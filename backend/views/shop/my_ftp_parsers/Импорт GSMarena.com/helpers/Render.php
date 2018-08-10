<?php
namespace app\helpers;

use yii\helpers\Html;
use yii\helpers\Url; 

class Render
{
    /**
     * render data in table
     *
     * @param $result_summary_info
     * @param $code
     * @param array $specsHeadTable
     * @return string
     */
    public static function render($result_summary_info, $code, $specsHeadTable = ['title_ru','code','units_en'])
    {
        $code = self::filterCode($result_summary_info, $code);

        $text = '';
        foreach($specsHeadTable as $val) {
            $ths = '';
            $ths .= Html::tag('th','url'); 
            foreach ($code as $k => $item) {
                $div = Html::tag('div', $item[$val]);
                $ths .= Html::tag('th', $div);
            }
            $text .= Html::tag('tr', $ths);
        }

        foreach ($result_summary_info as $k => $item) {
            $tds = '';
             if(isset($item['url'])) { $tds .= Html::tag('td',$item['url']); } 
            foreach ($code as $key => $value) {
                $tds .= Html::tag('td', 
               (isset($item[$key]) && ($key == 'related' || $key == 'family'))
                        ? (self::getLinksToRelated($item[$key]))
                        : ((isset($item[$key])) ? ((is_array($item[$key])) ? self::multi_implode(',', $item[$key]) : $item[$key]) : ''),
                ['style' => 'overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']);
            }
            $text .= Html::tag('tr', $tds);
        }
        $text = Html::tag('table', $text, ['class' => 'table table-responsive table-bordered ellipsis']);

        return Html::tag('div', $text, ['style' => 'width:auto; overflow-x:scroll;']);
    }
    
    public static function multi_implode($glue, $arr) 
    {
        $_arr = [];
        foreach($arr as $item) {
            $_arr[] = is_array($item) ? self::multi_implode($glue, $item) : $item;
        }
        return implode($glue, $_arr);
    }
    
    public static function getLinksToRelated($arr) 
    {
        $res_arr = [];
        foreach($arr as $k => $item) {
            $str = Html::a($k, Url::to($item, true));
            $res_arr[] = $str;
        }
        $test = implode(', ', $res_arr);
        return $test;
    }

    /**
     * @param $result_summary_info
     * @param $code
     * @return mixed
     */
    public static function filterCode($result_summary_info, $code)
    {
        foreach($code as $k => $item) {
            $exist = false;
            foreach($result_summary_info as $url => $rows) {
                if(isset($rows[$k])) {
                    $exist = true;
                }
            }
            if(!$exist) { unset ($code[$k]); }
        }
        return $code;
    }

    public static function pretty_print($str)
    {
        echo '<pre>';
        print_r($str);
        echo '</pre>';
    }

}