<?
//namespace app\helpers;
namespace backend\views\shop\ftp_parser\helpers;

use yii\helpers\Url;
use backend\views\shop\test\H;

class Render
{
	/**
	 * render data in table
	 *
	 * @param $items
	 * @param $code
	 * @param [] $headers
	 * @return string
	 */
	public static function render($items, $code, $headers = ['title_ru', 'code', 'units_en'])
	{
		$code = self::filterCode($items, $code);

		$text = '';
		foreach ($headers as $header) {
			$ths = '';
			$ths .= H::tag('th', 'url');
			foreach ($code as $k => $item) {
				$div = H::tag('div', $item[$header]);
				$ths .= H::tag('th', $div);
			}
			$text .= H::tag('tr', $ths);
		}

		foreach ($items as $options) {
			$tds = '';
			if (isset($options['url'])) {
				$tds .= H::tag('td', $options['url']);
			}
			foreach ($code as $key => $value) {
				$tds .= H::tag('td',
					(isset($options[$key]) && ($key == 'related' || $key == 'family'))
						? (self::getLinksToRelated($options[$key]))
                        : (self::ifWeHaveMultiArr($options, $key)),
					['style' => 'overflow: hidden;text-overflow: ellipsis;white-space: nowrap;']);
			}
			$text .= H::tag('tr', $tds);
		}
		$text = H::tag('table', $text, ['class' => 'table table-responsive table-bordered table-micro ellipsis']);

		return H::tag('div', $text, ['style' => 'width:auto; overflow-x:scroll;']);
	}

    public static function ifWeHaveMultiArr($item, $key)
    {
        return ((isset($item[$key]))
            ? ((is_array($item[$key])) ? self::multi_implode(',', $item[$key]) : $item[$key])
            : '');
    }

	public static function multi_implode($glue, $arr)
	{
		$_arr = [];
		foreach ($arr as $item) {
			$_arr[] = is_array($item) ? self::multi_implode($glue, $item) : $item;
		}
		return implode($glue, $_arr);
	}

	public static function getLinksToRelated($items)
	{
		$res_arr = [];
		foreach ($items as $url => $item) {
			$str = H::a($url, Url::to($item, true));
			$res_arr[] = $str;
		}
		$string = implode(', ', $res_arr);
		return $string;
	}

	/**
	 * @param $items
	 * @param $code
	 * @return mixed
	 */
	public static function filterCode($items, $codes)
	{
		foreach ($codes as $code => $value) {
			$exist = false;
			foreach ($items as $url => $options) {
				if (isset($options[$code])) {
					$exist = true;
				}
			}
			if (!$exist) {
				unset ($codes[$code]);
			}
		}
		return $codes;
	}
}