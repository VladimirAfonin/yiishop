<?

use app\helpers\Clerk;
use app\helpers\H;
use app\helpers\WebPage;
use app\helpers\Sheet;
use app\helpers\Render;
use yii\helpers\Url;

global $dom;

class Ya
{
	public static $version = 21;
	public static $proxyUrl = 'https://free-proxy-list.net/';
	public static $platforms = [
		//'platforms'                            				   => [ PC               Windows           Smartphone         Android          iOS               PlayStation        Quadcopter
		'для ПК, для смартфонов, для консолей, для квадрокоптеров' => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '+', 'llulwif' => '+', '0v8w2sz' => '+', '3zkrmzx' => '+', '0tizjem' => '+'],
		'для ПК, для консолей, для квадрокоптеров'                 => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '',  'llulwif' => '',  '0v8w2sz' => '',  '3zkrmzx' => '+', '0tizjem' => '+'],
		'для ПК, для смартфонов, для консолей'                     => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '+', 'llulwif' => '+', '0v8w2sz' => '+', '3zkrmzx' => '+', '0tizjem' => '-'],
		'для смартфонов, для квадрокоптеров'                       => ['dkeh1jd' => '',  'a5sj3l2' => '',  'd2ejgh3' => '+', 'llulwif' => '+', '0v8w2sz' => '+', '3zkrmzx' => '',  '0tizjem' => '+'],
		'для ПК, для квадрокоптеров'               				   => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '',  'llulwif' => '',  '0v8w2sz' => '',  '3zkrmzx' => '',  '0tizjem' => '+'],
		'для ПК, для консолей'                     				   => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '',  'llulwif' => '',  '0v8w2sz' => '',  '3zkrmzx' => '+', '0tizjem' => '-'],
		'для ПК'                                                   => ['dkeh1jd' => '+', 'a5sj3l2' => '+', 'd2ejgh3' => '',  'llulwif' => '',  '0v8w2sz' => '',  '3zkrmzx' => '',  '0tizjem' => '-'],
		'для консолей'                                             => ['dkeh1jd' => '',  'a5sj3l2' => '',  'd2ejgh3' => '',  'llulwif' => '',  '0v8w2sz' => '',  '3zkrmzx' => '+', '0tizjem' => '-'],                          //'vxq3g1f' => '',
		'для смартфонов'                                           => ['dkeh1jd' => '',  'a5sj3l2' => '',  'd2ejgh3' => '+', 'llulwif' => '+', '0v8w2sz' => '+', '3zkrmzx' => '',  '0tizjem' => '-'],
		'для квадрокоптеров'                                       => ['dkeh1jd' => '',  'a5sj3l2' => '',  'd2ejgh3' => '',  'llulwif' => '',  '0v8w2sz' => '',  '3zkrmzx' => '',  '0tizjem' => '+'],
	];
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

	/**
	 * @param $pattern
	 * @param int $count
	 * @return null
	 */
	public static function query($pattern, $count = 0)
	{
		global $dom;
		return $dom->query($pattern)->item($count)->nodeValue ?? null;
	}

	/**
	 * get item through encoding
	 * @param $value
	 * @return string
	 */
	public static function getValue($value)
	{
		return iconv("UTF-8", "ISO-8859-1//IGNORE", trim($value));
	}

	/**
	 * @param $str
	 * @return string
	 */
	public static function getAnswer($str)
	{
		$str = self::getValue($str);
		return (mb_stripos(trim($str), 'да') !== false) ? '+' : '-';
	}

	/**
	 * @param $haystack
	 * @param $str
	 * @param bool $needIconV
	 * @return string
	 */
	public static function isValueExists($haystack, $str, $needIconV = true)
	{
		if($needIconV) {
			$haystack = self::getValue($haystack);
		}
		return (mb_stripos($haystack, $str) !== false) ? '+' : '-';
	}

	public static function getDataFromApi($endpoint, $params = [])
	{
		$url = WebPage::makeUrl($endpoint, $params);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0); // 1
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public static function getProxyList($proxyUrl)
	{
		$row = [];
		$html = self::getDataFromApi($proxyUrl);
		$dom = Ya::dom($html);

		for($i = 1; $i <= 300; $i++) {
			$elite_proxy = $dom->query('//table[@id="proxylisttable"]/tbody/tr[' . $i . ']/td')->item(4)->nodeValue ?? null;
			if($elite_proxy == 'elite proxy') {
				$row[$i]['address'] = $dom->query('//table[@id="proxylisttable"]/tbody/tr[' . $i . ']/td')->item(0)->nodeValue ?? null;
				$row[$i]['ip'] = $dom->query('//table[@id="proxylisttable"]/tbody/tr[' . $i . ']/td')->item(1)->nodeValue ?? null;
			}
		}
		$row = array_values($row);
		$file = Url::to('@-parsers/market/proxy.txt');

		if(isset($row) && !empty($row)) {
			$fp = fopen($file, 'w');
			foreach(array_values($row) as $index => $item) {
				fwrite($fp, trim($item['address']) . ':' . trim($item['ip']) . (($index == count($row) - 1) ? '' : PHP_EOL));
			}
			fclose($fp);
		}
	}
}

// list with items
$ids = require_once(Url::to('@-parsers/market/targets.php'));

// get proxy and save to file
Ya::getProxyList(Ya::$proxyUrl);

// result summary
$items = [];
$clerk = new Clerk('@-generate/clerks/market-run.txt', ['total' =>count($ids)]);
foreach($ids as $id) {
	$url = "https://market.yandex.ru/product/$id/spec";
	$attempts = 1;
	$clerk->tick(['url'=>$url,'attempts'=>'attempts:1']);
	$data = ['url'=>$url];

	WebPage::get($url,[],[],$page);
	while($page->redirect_count != 0) {
		$attempts++;
		$clerk->update(['attempts'=>"attempts:$attempts"]);
		$page->delete();
		WebPage::get($url,[],[],$page);
	};
	$dom = WebPage::dom($page->desc);

	// get model
	$model_name = Ya::getValue($dom->query('//div[@class="n-product-title"]/div[@class="n-title__text"]/h1[@class="title title_size_22"]')->item(0)->nodeValue ?? null);
	if(preg_match("/^Очки виртуальной реальности (.+?)$/",$model_name, $match)){
		$product_type = 12; //Product type is VR Glasses
		$model_name = $match[1];
	}
	$full_name = explode(' ', $model_name);
	$data['w81a9u0'] = ucfirst($full_name[0]);

	$name = '';
	$b = 1;
	while(isset($full_name[$b])) {
		$name .= $full_name[$b] . ' ';
		$b++;
	}
	$data['33fksng'] = preg_replace('/-\s+\d+\s+GB/mui', "", $name);

	$i = 0;
	while($elem = $dom->query('//dt[@class="n-product-spec__name"]/span[@class="n-product-spec__name-inner"]/text()')->item($i)->nodeValue ?? null) {
		$elem = Ya::getValue($elem);
		switch($elem) {
			case "Самостоятельное устройство":
				// get 'alone'
				$alone = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($alone) $data['ywkphr2'] = Ya::getAnswer($alone);
				break;
			case "Вывод изображения":
				// get output image
				$display_output = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_output) {
					$str = Ya::getValue($display_output);
					if($str === 'собственный экран'){
						$data['aakph191'] = '+';
					}elseif($str==='экран смартфона'){
						$data['aakph191'] = '-';
						$data['d2ejgh3'] = $data['d2ejgh3']??'+';
						$data['ywkph216'] = $data['ywkph216']??'+';
					}
					//$data['ywkph212'] = str_replace(['экран смартфона', 'собственный экран'], ['smartphone screen','own screen'], $str);
				}
				break;
			case "Устройство подходит":
				$approaches = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($approaches) {
					$platform = Ya::getValue($approaches);
					if(isset(Ya::$platforms[$platform])){
						$data = array_merge(Ya::$platforms[$platform],$data);
					}else{
						var_dump($platform);
					}
//					$data['ywkph12b'] = Ya::getValue($approaches);
				}
				break;
			case 'Объем встроенной памяти':
				// get storage
				$storage = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($storage) $data['c8xo6x6'] = str_replace(' Гб', 'Gb' ,Ya::getValue($storage));
				break;
			case "Частота обновления":
				// display freq
				$display_freq = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_freq) $data['e16jcrzd'] = str_replace('Гц', '', Ya::getValue($display_freq));
				break;
			case "Цвет:":
				// color
				$z = 0;
				while($color = $dom->query("//div[contains(@class, 'n-filter-picker__img radiobox__box')]/@data-name")->item($z)->nodeValue ?? null) {
					$data['color'][] = (Ya::getValue($color));
					$z++;
				}
				$data['color'] = array_unique($data['color']);
				break;
			case "Цвет товара:":
				// color
				$z = 0;
				while($color = $dom->query("//div[contains(@class, 'n-filter-picker__img radiobox__box')]/@data-name")->item($z)->nodeValue ?? null) {
					$data['color'][] = (Ya::getValue($color));
					$z++;
				}
				$data['color'] = array_unique($data['color']);
				break;
			case "Диагональ экрана смартфона":
				$diagonal = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($diagonal) $data['ywkph216'] = str_replace('"','',Ya::getValue($diagonal));
				break;
			case "Разрешение дисплея":
				// resolution
				$display_res = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_res) {
					$display_res = Ya::getValue($display_res);
					if(preg_match('#\s+((\d+)\×(\d+)\s+)#mui', $display_res, $out_resolution)) {
						$data['3vzdcz1'] = $out_resolution[1];
						$data['nggks18'] = $out_resolution[2];
						$data['j2p7bju'] = $out_resolution[3];
					} else {
						$data['3vzdcz1'] = $display_res;
						$res_info = explode('×', $display_res);
						$data['nggks18'] = $res_info[0];
						$data['j2p7bju'] = $res_info[1];
					}
				}
				break;
			case "Разрешение дисплея для каждого глаза":
				// resolution
				$display_res = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_res) {
					$display_res = Ya::getValue($display_res);
					$data['3vzdcz1'] = $display_res;
					$res_info = explode('×', $display_res);
					$width_res = $res_info[0];
					$height_res = $res_info[1];
					$data['nggks18'] = $width_res;
					$data['j2p7bju'] = $height_res;
				}
				break;
			case 'Угол обзора':
				// view angle
				$display_angle = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_angle) $data['a62jcrld'] = str_replace('°', '',Ya::getValue($display_angle));
				break;
			case 'Размеры (ШxВ)':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($size) {
					$params = explode('x', str_ireplace('мм', '', Ya::getValue($size)));
					$data['65ihv16'] = $params[0];
					$data['qorav98'] = $params[1];
				}
				break;
			case 'Ширина устройства':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null; // todo exadd +
				if($size) {
					$data['65ihv16'] = str_ireplace('мм', '', Ya::getValue($size));
				}
				break;
			case 'Размеры (ШxГ)':
				// get size of item
				$size = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null; // todo exadd +
				if($size) {
					$params = explode('x', str_ireplace('мм', '', Ya::getValue($size)));
					$data['65ihv16'] = $params[0];
					$data['vbryix7'] = $params[1];
				}
				break;
			case 'Диаметр линз':
				// view angle
				$diameter = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($diameter) $data['ywkph003'] = str_replace('мм', '', Ya::getValue($diameter));
				break;
			case 'Разъемы':
				// 3.5 mm jack rmjj6m58
				$jack = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($jack) {
					$data['yh7xh3q'] = Ya::isValueExists($jack, '3.5');
					$data['rmjj6m58'] = Ya::isValueExists($jack, 'разъем для подключения зарядного устройства');
				}
				break;
			case 'Датчики':
				// sensors
				$sensor = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($sensor) {
					$data['h1ddzrt'] = Ya::isValueExists($sensor, 'акселерометр');
					$data['ywtcejg'] = Ya::isValueExists($sensor, 'гироскоп');
					$data['h88pkmd1'] = Ya::isValueExists($sensor, 'датчик приближения');
					$data['ywtcej2'] = Ya::isValueExists($sensor, 'магнитометр');
				}
				break;
			case 'Наушники':
				// headphone
				$headphone = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($headphone) {
					$data['2uecljhv'] = Ya::isValueExists($headphone, 'в комплекте');
				}
				break;
			case 'Пульт управления в комплекте':
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetlj1'] = Ya::isValueExists($console, 'есть');
				}
				break;
			case 'Геймпад в комплекте':
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetlj3'] = Ya::isValueExists($console,'есть');
				}
				break;
			case "Сенсорная панель управления":
				// console
				$console = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($console) {
					$data['4yetljh'] = Ya::isValueExists($console, 'есть');
				}
				break;
			case 'Размеры (ШxВxГ)':
				// dimensions
				$dimensions = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($dimensions) {
					$params = explode('x', str_ireplace('мм', '', Ya::getValue($dimensions)));
					$data['65ihv16'] = $params[0];
					$data['qorav98'] = $params[1];
					$data['vbryix7'] = $params[2];
				}
				break;
			case 'Вес':
				// weight
				$weight = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($weight) {
					$data['ywkph16b'] = str_ireplace('г', '', Ya::getValue($weight));
				}
				break;
			case 'Очки дополненной реальности':
				$glass = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($glass) { $data['ywkph215'] = Ya::getAnswer($glass); }
				break;
			case 'Дополнительная информация':
				// add info
				$addit_info = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				$addit_info = Ya::getValue($addit_info);
				if($url == 'https://market.yandex.ru/product/1716269848/spec') {
					$add_info = explode('; ', $addit_info);
				} else {
					$add_info = explode('; ', $addit_info);
					if(count($add_info) < 4) {
						$add_info = explode(', ', $addit_info);
					};
				}
				foreach($add_info as $item) {
					if(strpos($item, 'ОС ') !== false) {
						$data['ui65qcn'] = trim(str_replace(['ОС ', '-'], [''], $item));
					}
					if(strpos($item, 'время отклика') !== false) {
						$data['3jsiejz'] = trim(str_replace(['время отклика', 'миллисекунд'], [''], $item));
					}
					if(strpos($item, 'cкорость смены пикселей:') !== false) {
						$data['3jsiejz'] = trim(str_replace(['cкорость смены пикселей:', 'ms'], [''], $item));
					}
					if(strpos($item, 'графический процессор - ') !== false) {
						$data['4kzmswo'] = trim(str_replace(['графический процессор - ','GPU'], [''], $item));
					}
					if(strpos($item, 'графический процессор ') !== false) {
						$data['4kzmswo'] = trim(str_replace(['графический процессор ', 'до 700МГц (44,8 Гфлоп/с)','-','GPU'], [''], $item));
					}
					if(strpos($item, 'процессор -') !== false && (strpos($item, 'графический') === false)) {
						// get cpu freq
						if(preg_match('/\d+\.\d+ГГц/mu', $item, $cpu_freq)) {
							$data['y5xo6x6'] = str_replace('ГГц', 'Ghz',$cpu_freq[0]);
						}
						if(isset($data['y5xo6x6'][0])) {
							$data['y5xo6x4'] = trim(str_replace(['процессор - ', $cpu_freq[0]], [''], $item));
						} else {
							$data['y5xo6x4'] = trim(str_replace(['процессор - '], [''], $item));
						}
					}
					if(strpos($item, 'Процессор ') !== false && (strpos($item, 'графический') === false)) {
						$data['y5xo6x4'] = trim(str_replace('Процессор ', '', $item));
					}
					if(strpos($item, 'Аккумулятор на') !== false) {
						$data['wbswcml'] = trim(str_replace(['Аккумулятор на ', 'мВч'], [''], $item));
					}
					if(strpos($item, 'оперативная память ') !== false) {
						$data['ej4wq1y'] = trim(str_replace(['оперативная память ','встроенная -'], [''], str_replace([' Гб','Гб','ГБ','DDR3'],['Gb','Gb','Gb',''],$item)));
						if(strpos($item, 'DDR3') !== false) $data['z3xo6x6'] = 'DDR3';
					}
					if(strpos($item, 'оперативной памяти') !== false) {
						$data['ej4wq1y'] = trim(str_replace('оперативной памяти', '', str_replace([' ГБ','ГБ','DDR3'],['Gb','Gb',''],$item)));
					}
					if(strpos($item, 'RAM -') !== false) {
						$data['ej4wq1y'] = trim(str_replace(['RAM -', 'встроенная -'], [''], str_replace([' Гб', 'Гб', 'ГБ', 'DDR3'], ['Gb', 'Gb', 'Gb', ''], $item)));
						if(strpos($item, 'DDR3') !== false) $data['z3xo6x6'] = 'DDR3';
					}
					if(strpos($item, '32-рязрядная Windows') !== false) {
						$data['ui65qcn'] = str_replace('рязрядная','bit', trim($item));
					}
					if(strpos($item, 'WiFi 802') !== false) {
						preg_match('/WiFi(\s+\d+\.\d+\s+[a-z]+\/[a-z]+)/mui', trim($item), $output_arr);
						if(isset($output_arr[1]) && !empty($output_arr[1])) {
							$data['2pinrcv'] = trim($output_arr[1]);
						}
					}
					if(!isset($data['2pinrcv'])) {
						if(strpos($item, 'WiFi') !== false) {
							{ $data['2pinrcv'] = '+'; }
						}
					}
					if(strpos($item, 'MIMO ') !== false) {
						$data['p4zld1l5'] = '+';
					}
					if(strpos($item, 'Dual Band') !== false) {
						$data['p4zld1l2'] = '+';
					}
					if(strpos($item, 'Bluetooth') !== false) {
						preg_match('/Bluetooth\s+(\d+\.\d+)/mui', $item, $out_bluetooth);
						if(isset($out_bluetooth[1]) && !empty($out_bluetooth[1])) {
							$data['p4zld5l'] = trim($out_bluetooth[1]);
						}
					}
					if(strpos($item, 'USB Type-C') !== false) {
						$data['rmjj6m5t'] = '+';
					}
					if(strpos($item, 'USB-C') !== false) { $data['rmjj6m5t'] = '+'; }
					if(strpos($item, 'поддержка карт памяти') !== false) {
						if(strpos($item, 'Micro SD')) {
							$data['yz90cwl'] = 'Micro SD';
						};
						if(strpos($item, 'MicroSD')) {
							$data['yz90cwl'] = 'MicroSD';
						};
						if(strpos($item, 'TF')) {
							$data['yz90cwl'] = 'TF';
						};
						preg_match('/(\d+)\s?(Гб)|(\d+)\s?(Тб)/mui', $item, $out_max_card);
						$out_max_card = array_values(array_diff($out_max_card, ['']));

						if(isset($out_max_card[1]) && !empty($out_max_card[1])) {
							$data['yz90cwq'] = str_replace(['Гб', 'Тб'], ['Gb', 'Tb'], $out_max_card[1] . ($out_max_card[2] ?? ''));
						}
					}
					if(strpos($item, 'разъем для карт памяти') !== false) {
						if(strpos($item, 'Micro-SD')) {
							$data['yz90cwl'] = 'Micro SD';
						};
						preg_match('/(\d+)\s?(Гб)|(\d+)\s?(Тб)/mui', $item, $out_max_card);
						$out_max_card = array_values(array_diff($out_max_card, ['']));

						if(isset($out_max_card[1]) && !empty($out_max_card[1])) {
							$data['yz90cwq'] = str_replace(['Гб', 'Тб'], ['Gb', 'Tb'], $out_max_card[1] . ($out_max_card[2] ?? ''));
						}
					}
					if(strpos($item, 'двойная фронтальная камера') !== false) {
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'Графический комплекс ') !== false) {
						$data['4kzmswo'] = trim(str_replace(['Графический комплекс ','GPU'], [''], $item));
					}
					if(strpos($item, ' видеопамяти') !== false) {
						$video_memory = trim(str_replace(['видеопамяти '], [''], $item));
						preg_match('/\d+\s+МБ/mui', $item, $out_video);
						if(isset($out_video[0]) && !empty($out_video[0])) {
							$data['y1xo6x7'] = str_replace(' МБ', 'Mb', ($out_video[0]));
						}
					}
					if(strpos($item, 'возможность голосового управления') !== false) {
						$data['c534jxf'] = '+';
						$data['yq2jcrll'] = '+';

					}
					if(strpos($item, 'поддержка голосового управления') !== false) {
						$data['c534jxf'] = '+';
						$data['yq2jcrll'] = '+';
					}
					if(strpos($item, '2 фронтальные камеры ') !== false) {
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'двойная фронтальная камера') !== false) {
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'встроенный микрофон') !== false) {
						$data['yq2jcrll'] = '+';
					}
					if(strpos($item, 'интегрированный микрофон') !== false) {
						$data['yq2jcrll'] = '+';
					}
					if(strpos($item, 'встроенные микрофоны и камеры') !== false) {
						$data['yq2jcrll'] = '+';
						$data['gtgn2m6'] = 2;
					}
					if(strpos($item, 'фронтальная камера') !== false) {
						if(!isset($data['gtgn2m6'])) {
							$data['gtgn2m6'] = 1;
						}
					}
					if(strpos($item, 'размер экрана:') !== false) {
						preg_match('/\d+[\.|\,]\d+/mui', $item, $disp_size);
						if(isset($disp_size[0]) && !empty($disp_size[0])) {
							$data['1n820fz'] = $disp_size[0];
						}
					}
					if(strpos($item, 'порты') !== false) {
						$data['p85t8s8a'] = Ya::isValueExists($item, 'HDMI');
						$data['0arcae64'] = Ya::isValueExists($item, 'USB 2.0');
						$data['2q8o92fk'] = Ya::isValueExists($item, 'USB');
					}
					if(strpos($item, 'HDMI 2.0') !== false) {
						$data['cie73ha'] = '+';
					}
					if(strpos($item, 'USB 3.0') !== false) {
						$data['p7s2uenu'] = '+';
					}
					if(strpos($item, 'USB 2.0') !== false) {
						$data['0arcae64'] = '+';
					}
					if(strpos($item, 'фронтальная камера') !== false || strpos($item, 'встроенная стерео-камера') !== false) {
						preg_match('/(\d+)/mui', $item, $out_res);
						if(isset($out_res[1]) && !empty($out_res[1])) {
							$data['06wzu4yz'] = $out_res[1];
						}
					};
					if(strpos($item, 'GPS') !== false) {
						$data['yfvshn2'] = '+';
					}
					if(strpos($item, 'Micro-USB') !== false) {
						$data['p85t8s8z'] = '+';
					}
					if(strpos(strtolower($item), 'micro USB') !== false) {
						$data['p85t8s8z'] = '+';
					}
					if(strpos($item, 'OTG') !== false) {
						$data['9qsw0l7d'] = '+';
					}
					if(strpos($item, 'степень защиты') !== false) {
						if(strpos($item, 'IP54')) {
							$data['cxeplx1'] = '+';
							$data['cxeplx2'] = '+';
						}
					}
					if(strpos($item, 'камера Playstation') !== false) {
						$data['gtgn2m6'] = 1;
					}
					if(strpos($item, 'кабель HDMI') !== false) {
						$data['dkzm1ds'] = '+';
					}
					if(strpos($item, 'стереонаушники') !== false) {
						$data['2uecljhv'] = '+';
					}
					if(strpos($item, 'адаптер переменного тока') !== false) {
						$data['9deiqkz'] = '+';
					}
					if(strpos($item, 'силовой кабель переменного тока') !== false) {
						$data['01dksua'] = '+';
					}
					if(strpos($item, 'HDMI') !== false) {
						$data['p85t8s8a'] = '+';
					}
					if(strpos(strtolower($item), 'microSD') !== false) {
						$data['yz90cwl'] = 'MicroSD';
					}
					if(strpos($item, 'USB-держателя') !== false) {
						$data['4yetlj4'] = '+';
					}
					if(strpos($item, 'ткань для удаления пыли') !== false) {
						$data['4yetlj5'] = '+';
					}
					if(strpos($item, 'очищающая салфетка') !== false) {
						$data['4yetlj5'] = '+';
					}
					if(strpos($item, 'DP 1.2') !== false) {
						$data['rmjj6m56'] = '+';
					}
					if(strpos($item, 'DisplayPort 1.2') !== false) {
						$data['rmjj6m56'] = '+';
					}
					if(strpos($item, 'контроллеры Touch') !== false) {
						$data['4yetlj1'] = '+';
					}
					if(strpos($item, 'чехол') !== false) {
						$data['h2bybiw'] = '+';
					}
					if(strpos($item, 'встроенное окно камеры') !== false) {
						$data['gtgn223'] = '+';
					}
					if(strpos($item, 'SteamVR Tracking') !== false) {
						$data['4yetlj6'] = '+';
					}
					if(strpos($item, 'управление с помощью поворотов головы') !== false) {
						$data['ywkph213'] = '+';
					}
					if(strpos($item, 'дополнительные линзы') !== false) {
						$data['4yetlj8'] = '+';
					}
					if(strpos($item, 'руководство пользователя') !== false) {
						$data['v464xz6'] = '+';
					}
					if(strpos($item, 'складные') !== false) {
						$data['ywkph214'] = '+';
					}
					if(strpos($item, 'складная конструкция') !== false) {
						$data['ywkph214'] = '+';
					}
					if(strpos($item, 'встроенный приемник') !== false) {
						$data['4yetlj9'] = '+';
					}
					if(strpos($item, 'возможно беспроводное подключение') !== false) {
						$data['ppzld1l4'] = '+';
					}
					if(strpos($item, 'AV вход/выход') !== false) {
						$data['ppzld1q4'] = '+';
					}
					if(strpos($item, 'вход AV') !== false) {
						$data['ppzld1q4'] = '+';
					}
					if(strpos($item, 'камера') !== false && !isset($data['06wzu4yz'])) {
						preg_match('/(\d+)Мп/mui', $item, $out_res);
						if(isset($out_res[1]) && !empty($out_res[1])) {
							$data['06wzu4yz'] = $out_res[1];
							if(!isset($data['gtgn2m6'])) $data['gtgn2m6'] = 1;
						}
					}
				}
				break;
			case "Регулировка межзрачкового расстояния":
				$adjastment = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($adjastment) {
					$data['ywkph001'] = Ya::isValueExists($adjastment, 'есть');
				}
				break;
			case "Регулировка фокуса":
				// focus
				$focus = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($focus) {
					$data['ywkph002'] = Ya::isValueExists($focus, 'есть');
				}
				break;
			case "Совместимость с ОС":
				// OS
				$compatible = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($compatible) {
//					$data['ui65qc1'] = Ya::getValue($compatible);

//					if(!isset($data['llulwif'])||empty($data['llulwif']))
					$data['llulwif'] = Ya::isValueExists($compatible, 'Android');
					//if(!isset($data['0v8w2sz'])||empty($data['0v8w2sz']))
					$data['0v8w2sz'] = Ya::isValueExists($compatible, 'iOS');
					if(mb_stripos($compatible,'Android')!==false||mb_stripos($compatible,'iOS')!==false){
						if(!isset($data['d2ejgh3'])||empty($data['d2ejgh3'])) $data['d2ejgh3'] = '+';
						unset($data['ui65qc1']);
						$data['ui65qc1'] = '';
					}
				}
				break;
			case 'Технология экрана':
				// display tech
				$display_tech = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($display_tech) {
					$data['xxyv5nx'] = Ya::getValue($display_tech);
				}
				break;
			case 'Минимальные системные требования':
				$requirements = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($requirements) {
					$data['ui65q11'] = Ya::getValue($requirements);
				}
				break;
			case 'Контроллер движений в комплекте':
				$controller = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($controller) {
					$data['4yetlj2'] = Ya::isValueExists($controller, 'есть');
				}
				break;
			case 'Внешний датчик положения в пространстве':
				$controller = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($controller) {
					$data['ywtcej3'] = Ya::isValueExists($controller, 'есть');
				}
				break;
			case 'Запись видео':
				// recording video
				$video_rec = $dom->query('//dd[@class="n-product-spec__value"]/span[@class="n-product-spec__value-inner"]')->item($i)->nodeValue ?? null;
				if($video_rec) {
					$data['lggn122'] = Ya::isValueExists($video_rec, 'есть');
				}
				break;
			default:
				H::br([H::a($model_name,$url), 'Unknown specification', $elem]);
		}
		$i++;
	}
	$items[$url] = $data;
}

$codes = Sheet::rf('@config/product/specs.csv', ['indexFrom' => 'code']);
//H::print_r($items);
echo(Render::render($items, $codes, ['category', 'group', 'title_ru', 'code', 'units_en']));