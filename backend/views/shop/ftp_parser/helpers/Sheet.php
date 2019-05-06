<? namespace app\helpers;

use yii\base\Exception;
use yii\helpers\Url;

class Sheet
{
	public static function sf($text, $glue = "\t", $file = "@-generate/grid/results.txt")
	{
		if (is_array($text)) $text = implode($glue, $text);
		file_put_contents($file, $text . "\n", FILE_APPEND);
	}

	public static function rf($path, $params = [])
	{
		global $trimmer;
		extract($params);
		/*@var $path string */
		/*@var $indexFrom string */
		/*@var $asArray string */
		/*@var $separator string */
		/*@var $trimmer string */
		/*@var $prefix string */

		if (strpos($path, '@') === false) {
			$path = '@-generate/' . $path;
		};

		if (!isset($separator)) $separator = "\t";
		if (!isset($prefix)) $prefix = "";
		if (!isset($asArray)) $asArray = false;
		if (!isset($indexFrom)) $indexFrom = false;

		if (!is_array($params)) {
			$params = [$params, ' '];
		}
//		list($separator, $trimmer) = $params;

		if (isset($params['empty'])) {
			if (!file_exists(Url::to($path))) {
				return $params['empty'];
			}
		}
		$file = file(Url::to($path), FILE_IGNORE_NEW_LINES);
		$header = explode($separator, $file[0]);

		array_walk($header, function (&$value, &$key) {
			global $trimmer;
			$value = trim($value, $trimmer);
		});
		if (count($header) !== count(array_flip($header))) {
			throw new Exception('Duplicates headers names');
		}
		//unset($header[0]);
		unset($file[0]);
		$rows = [];
		$n = 0;

		foreach ($file as $s => $string) {
			$row = explode($separator, $string);
			$id = $n++;
			if ($indexFrom) {
				$index = array_search($indexFrom, $header);
				$id = $row[$index]; //$row[$header[0]];
			}

			if (empty($string)) continue;
//			unset($row[0]);
			foreach ($header as $i => $k) {
				if (!isset($row[$i])) {
					$row[$i] = null;
					//throw new Exception('Can not find header: '.$i.' ('.$k.")\n String:".$s."\n Headers:".count($header)."\n Row values:".count($row));
				}
				if ($row[$i] == '""') $row[$i] = '';
				if ($asArray) {
					$rows[$id][$prefix][$s][$k] = trim($row[$i], $trimmer);
				} elseif (!isset($rows[$id][$prefix . $k])) {
					$rows[$id][$prefix . $k] = trim($row[$i], $trimmer);
				}
			}
		}
		return $rows;
	}

	public static function br_ar($array = [])
	{
		foreach ($array as $i => $v) {
			if (!is_numeric($i)) {
				$i = '"' . $i . '"';
			}
			if (is_array($v)) {
				if (isset($v[1])) $comment = '    //' . $v[1];
				$v = $v[0];
			} else {
				$comment = '';
			}
			if (!is_numeric($v)) {
				$v = '"' . $v . '"';
			}
			H::br([$i . "=>" . $v . "," . $comment ?? null]);
		}
	}

	public static function show(array $rows, $class = 'table table-bordered')
	{
		if (empty($rows)) return '';
		$text = '';
		$ths = '';
		foreach (array_keys(reset($rows)) as $title) {
			$div = H::div($title, ['class' => 'ellipsis']);
			$ths .= H::tag('th', $div, ['title' => $title]);
		}
		$text .= H::tag('tr', $ths);
		foreach ($rows as $row) {
			$tds = '';
			foreach ($row as $attribute) {
				$tds .= H::td($attribute);
			}
			$text .= H::tag('tr', $tds);
		}
		return H::tag('table', $text, ['class' => $class]);
	}

	public static function gradient($min, $max, $val)
	{
		if ($val > $max) $val = $max;
		if ($val < $min) $val = $min;
		$colors = ['FEC2C2', 'FBC6BF', 'F9CABD', 'F7CEBB', 'F5D2B8', 'F3D7B6', 'F1DBB4', 'EFE0B2', 'EDE4AF', 'EBE9AD', 'E5E9AB', 'DCE7A9', 'D3E5A6', 'CAE3A4', 'C2E1A2', 'B9DFA0', 'B0DD9E', 'A7DB9C', '9ED99A', '98D79A'];
		$colors = array_reverse($colors);
		if (($max - $min) === 0) {
			$max = 1;
			$min = 0;
		}

		$rate = ($max - $val) / ($max - $min);
		$set = round($rate * (count($colors) - 1));
		return $colors[$set];
	}

	public static function showColors(array $data, $class = 'table table-bordered table-gradient')
	{
		if (empty($data)) return '';

		$med = [];
		foreach ($data as $id => $row) {
			foreach ($row as $k => $v) {
				if (!is_numeric($v)) continue;
				if (!isset($med[$k])) {
					$med[$k]['max'] = $v;
					$med[$k]['min'] = $v;
				} else {
					if ($med[$k]['max'] < $v) $med[$k]['max'] = $v;
					if ($med[$k]['min'] > $v) $med[$k]['min'] = $v;
				}
			}
		}

		$text = '';
		$ths = '';
		foreach (array_keys(reset($data)) as $title) {
			$div = H::div($title, ['class' => 'ellipsis']);
			$ths .= H::tag('th', $div, ['title' => $title]);
		}

		$text .= H::tag('tr', $ths);
		foreach ($data as $row) {
			$tds = '';
			foreach ($row as $k => $v) {
				$options = [];
				if (is_numeric($v)) {
					$color = self::gradient($med[$k]['min'], $med[$k]['max'], $v);
					$options = ['class' => 'td-gradient', 'style' => 'color:#000;background:#' . $color];
				}

				$tds .= H::td($v, $options);
			}
			$text .= H::tag('tr', $tds);
		}
		return H::tag('table', $text, ['class' => $class]);
	}

	public static function TableHtmlToWiki($text)
	{
		$text = str_replace('<table>', '', $text);
		$text = str_replace('</table>', '', $text);
		$text = str_replace('<th>', '', $text);
		$text = str_replace('<tr>', '', $text);
		$text = str_replace('<td>', '', $text);
		$text = str_replace("	", '', $text);
		$text = str_replace("\r\n", '', $text);
		$text = str_replace('</tr>', "\n", $text);
		$text = str_replace('</th>', "\t", $text);
		$text = str_replace('</td>', "\t", $text);
		$text = str_replace("\t\t", "\t", $text);
		$text = str_replace("\t\n", "\n", $text);
		$text = str_replace("\n\n", "\n", $text);
		return "\n{{table\n" . $text . "}}\n";
	}
}