<?
use app\helpers\Clerk;
use app\helpers\WebPage;
use app\helpers\Sheet;
use app\helpers\Render;
use yii\helpers\Url;
use yii\helpers\Json;
use app\helpers\H;

global $dom;

class GsmArena
{
	public static $version = 5;

	public static function dom($content): DOMXPath
	{
		$head = '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
		$doc = new DOMDocument();
		@$doc->loadHTML($head.$content);
		return new DOMXpath($doc);
	}

	public static function query($pattern, $count = 0)
	{
		global $dom;
		return $dom->query($pattern)->item($count)->nodeValue ?? null;
	}

	public static function getNoiseCrosstalk($string)
	{
		preg_match('/-[\d]+\.[\d]+dB/mu', trim($string), $output_array);
		return $output_array[0] ?? null;
	}

	public static function getPrimaryCamera($string)
	{
		return preg_split('/,\s+check quality/mu', $string)[0];
	}

	public static function getBatteryReplacement($string)
	{
		if(strpos($string, 'n-removable') != false) {
			return '-';
		} else {
			return '+';
		}
	}

	public static function getWaterResistant($string)
	{
		preg_match('/IP\d+/', $string, $output_array);
		if(isset($output_array[0]) && ! empty($output_array[0])) {
			return $output_array[0];
		}
		return null;
	}

	public static function getDustResistant($str)
	{
		preg_match('/IP\d/', $str, $output_array);
		if(isset($output_array[0]) && ! empty($output_array[0])) {
			return $output_array[0] . 'X';
		} else {
			return null;
		}
	}

	public static function isValueExists($item, $name)
	{
		if(strpos($item, $name) !== false) {
			return '+';
		} else {
			return '-';
		}
	}

	public static function getSim($str)
	{
		preg_match('/\s*(.+)/mu', $str, $output_array);
		if( ! empty($output_array[0])) return trim($output_array[0]);
	}

	public static function getWlan($str)
	{
		preg_match('/([0-9]+\.[0-9]+\s+).+/mu', $str, $output_array);
		if( ! empty($output_array[0])) return explode(',', $output_array[0])[0];
	}

	public static function getAnswer($str)
	{
		return (strpos(trim($str), 'es') != false) ? '+' : '-';
	}

	public static function getAnswerNo($str)
	{
		return (strpos(trim($str), 'No') !== false) ? '-' : '+';
	}

	public static function getWatchOs($platform_os)
	{
		$watch_os = ['wearable', 'Android OS compatible', 'Android Wear', 'watchOS', 'Proprietary OS', 'Wearable platform', 'Nucleus OS', 'LG Wearable', 'Tencent OS'];
		foreach($watch_os as $item) {
			if(stripos($platform_os, $item) !== false) {
				return true;
			}
		}
		return false;
	}

	public static function getDataFromApi($endpoint, $params = [])
	{
		$url = self::makeUrl($endpoint, $params);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1); // 1
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	public static function makeUrl($url, $urlParams = [], $ignoreParams = []): string
	{
		foreach($ignoreParams as $key) {
			unset($urlParams[$key]);
		};
		if( ! empty($urlParams)) {
			$url .= "?" . http_build_query($urlParams);
		}
		return $url;
	}

	public static function check($item, $name)
	{
		if(mb_strpos($item, $name) !== false) {
			return '+';
		} else {
			return '';
		}
	}

	public static function getAllPhones($list_all_phones = 'https://www.gsmarena.com/sitemap-phones.xml')
	{
		if( ! $xml = simplexml_load_file($list_all_phones)) throw new RuntimeException('cant load file');
		return $xml;
	}

	public static function links()
	{
		$links = [];
		$path = Url::to("@-parsers/gsmarena/files/map.json");
		$period = 86400; // 1 day
		if(file_exists($path)) {
			$cron_time = filemtime($path);
			if(time() - $cron_time >= $period) {
				$xml = self::getAllPhones();
				foreach($xml->url as $item) {
					if($item->priority == 0.9) {
						$needed_link = (array)$item->loc[0];
						$links[] = $needed_link[0];
					}
				}
				file_put_contents($path, Json::encode($links));
				return $links;
			} else {
				return $links = Json::decode(file_get_contents($path));
			}
		} else {
			$xml = self::getAllPhones();
			foreach($xml->url as $item) {
				if($item->priority == 0.9) {
					$needed_link = (array)$item->loc[0];
					$links[] = $needed_link[0];
				}
			}
			file_put_contents($path, Json::encode($links));
			return $links;
		}
	}
}

$urls = Gsmarena::links();

$targets = require_once(Url::to('@-parsers/gsmarena/targets.php'));

if(!empty($targets)){
	$urls = array_filter($urls, function ($v) use ($targets) {return (array_search($v,$targets) !== false);});
}
//$urls = array_filter($urls, function ($k) {return $k % 100 === 0;}, ARRAY_FILTER_USE_KEY);

$items = [];
$clerk = new Clerk('@-parsers/gsmarena/files/clerk.txt', ['total' =>count($urls)]);
foreach($urls as $k => $url)
{
	$clerk->tick(['url'=>$url]);

	// get data from cache
	$path_hash = hash('sha256', $url);
	$webpage = WebPage::find()->filterWhere(['path_hash' => $path_hash,'format'=>'json'])->one();
	if($webpage !== null && !empty($webpage->desc) && ($webpage->version == GSMarena::$version)) {
		$data = Json::decode($webpage->desc);
		$items[$url] = $data;
		continue;
	}

	if ($url==='https://www.gsmarena.com/htc_desire_830-8066.php') {
		continue;
	};

	$result_data = [];
	$html = WebPage::get($url);

	$dom = GSMarena::dom($html);

	// get name of item
	$name = GSMarena::query('//h1[@class="specs-phone-name-title"]/text()');
	if($name) {
		$full_name = explode(' ', trim($name));
		$result_data['w81a9u0'] = ucfirst($full_name[0]);
		$model_name = '';
		for($i = 1; $i <= 10; $i++) {
			if(isset($full_name[$i])) $model_name .= $full_name[$i] . ' ';
		}
		$result_data['33fksng'] = $model_name;
		$result_data['ywkph222'] = trim($result_data['w81a9u0'].' '.$result_data['33fksng']);
		$result_data['url'] = H::a($url, Url::to($url, true));
	}

	$primary_cam = GSMarena::query('//span[@data-spec="camerapixels-hl"]/text()');
	if($primary_cam) {
		$result_data['lggn0m2'] = $primary_cam;
	}

	// get data released
	$data_release_start = GSMarena::query('//span[@data-spec="released-hl"]/text()');
	if($data_release_start) {
		$data_release = preg_match_all('/[0-9]+\,\s+[\w]+/mu', $data_release_start, $output_array);
		if(strpos($output_array[0][0] ?? $data_release_start, 'ot announced y') != false) {
			$result_data['2lbcv9f'] = '-';
		} else {
			$release_date_info = trim(str_replace('Released', '', $output_array[0][0] ?? $data_release_start));
			$release_announced_info = explode(',', $release_date_info);
			if(isset($release_announced_info[1])) {
				$release_month = strftime("%m", strtotime($release_announced_info[1]));
				$result_data['2lbcv9f'] = $release_month . '/' . $release_announced_info[0];
			}
		}
	}

	// also known
	$comment_info = GSMarena::query('//p[@data-spec="comment"]');
	if($comment_info) {
		preg_match('/Versions:(.+)\s+Also/uim', $comment_info, $out_versions); // versions
		if(isset($out_versions[1]) && ! empty($out_versions[1])) {
			$result_data['y1kpha1c'] = trim($out_versions[1]);
		}
		preg_match('/Also known as (.+)/uim', $comment_info, $out_alt_name); // alt name
		if(isset($out_alt_name[1]) && ! empty($out_alt_name[1])) {
			$result_data['ywkpha1b'] = trim($out_alt_name[1]);
		}
	}

	// get weight & thin params
	$weight = GSMarena::query('//span[@data-spec="body-hl"]/text()');

	// get version OS
	$version_os = GSMarena::query('//span[@data-spec="os-hl"]/text()');

	// get hits
	$hits = GSMarena::query('//li[@class="light pattern help help-popularity"]/span/text()');
	if($hits) {
		$hits_info = str_replace('hits', '', trim($hits));
		$result_data['35fksng'] = intval(str_replace(',', '', $hits_info));
	}

	// get video url
	$video_url = GSMarena::query('//div[@class="module module-vid-review"]/iframe/@src');
	if($video_url) $result_data['video'] = [$video_url];

	// get storage info
	$storage = GSMarena::query('//span[@data-spec="storage-hl"]/text()');
	if($storage) {
		$storage_info = explode(',', $storage)[0];
		preg_match('/(\d*\W*[\d+]*\W+\d+\w+)\s+storage|(\d+\w+)\s+storage/', $storage_info, $out_internal_memory);
		if(isset($out_internal_memory[1]) && ! empty($out_internal_memory[1])) {
			$result_data['c8xo6x6'] = str_replace(';', '', $out_internal_memory[1]);
			if(isset($out_internal_memory[2]) && ! empty($out_internal_memory[2])) {
				$result_data['c8xo6x6'] .= '/' . $out_internal_memory[2];
			}
		} else {
			if(isset($out_internal_memory[2]) && ! empty($out_internal_memory[2])) {
				$result_data['c8xo6x6'] = $out_internal_memory[2];
			}
		}
	}

	// get screen size
	$screen_size = GSMarena::query('//span[@data-spec="displaysize-hl"]/text()');

	// get screen resolution
	$screen_resolution = GSMarena::query('//div[@data-spec="displayres-hl"]/text()');

	// get camera info MP
	$camera = GSMarena::query('//span[@data-spec="camerapixels-hl"]/text()');

	// get camera pixels
	$camera_pixels = GSMarena::query('//div[@data-spec="videopixels-hl"]/text()');

	// get ram size
	$ram_size = GSMarena::query('//span[@data-spec="ramsize-hl"]/text()');
	$ram_size_value = GSMarena::query('//span[@data-spec="ramsize-hl"]/following::span[1]/text()');
	if($ram_size) {
		$result_data['ej4wq1y'] = str_replace('RAM', '', $ram_size . $ram_size_value);
	}

	// get ram chipset
	$chipset = GSMarena::query('//div[@data-spec="chipset-hl"]/text()');
	if($chipset) {
		$result_data['dkg7n4e'] = $chipset;
	}

	// get battery info
	$battery_capacity = $ram_size = GSMarena::query('//span[@data-spec="batsize-hl"]/text()');
	if($battery_capacity) $result_data['wbswcml'] = $battery_capacity;
	$battery_type = $chipset = GSMarena::query('//div[@data-spec="battype-hl"]/text()');
	$result_data['63r9r99'] = str_replace(['Li-Ion', 'Li-Po', 'NiMH'], ['Lithium Ion', ' Lithium Polymer', 'Nickel-metal Hydride'], $battery_type);


### table info ###
// get through all tables on page with different info
	for($i = 1; $i <= 13; $i++) {
		$some_table = GSMarena::query("//table[$i]/tr/th");
		switch($some_table) {
			case 'Network':
				// - Technology td
				$tech_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]');

				if($tech_td == 'Technology') {
					// get technology
					$technology = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]');
					if($technology) {
						$result_data['6me3pwq'] = GSMarena::isValueExists($technology, 'GSM');
						$result_data['k6ddojx'] = GSMarena::isValueExists($technology, 'LTE');
					}
				}

				// - 2g bands td
				$bands_2g_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($bands_2g_td == '2G bands') {
					// get 2g
					$technology_2g = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td', 3);
					preg_match_all('/(\s+\d{3,}\s+\/*)+/uim', $technology_2g, $out_2g);
					if(isset($out_2g[0][0]) && ! empty($out_2g[0][0])) {
						$result_data['es77mka'] = array_map('trim', explode(',', trim(str_replace(' / ', ', ', $out_2g[0][0]))));
					}
				}

				// - 3g bands td
				$bands_3g_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 2);
				if($bands_3g_td == '3G bands') {
					// get 3g
					$technology_3g = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)][3]/td', 1);
					preg_match_all('/(\s+\d{3,}\(*[A-Z]*\)*\s+\/*)+/uim', $technology_3g, $out_3g);
					if(isset($out_3g[0][0]) && ! empty($out_3g[0][0])) {
						$result_data['lfy3yhr'] = array_map('trim', explode(',', trim(str_replace([' / ', ' /'], [', '], $out_3g[0][0]))));
					}
				}

				// - 4g bands td
				$bands_4g_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 3);
				if($bands_4g_td == '4G bands') {
					// get 4g
					$technology_4g = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)][4]/td');
					$four_g = trim(str_replace(['LTE', 'band'], '', trim($technology_4g)));

					if( ! empty($four_g)) {
						preg_match_all('/\d+\(\d+\)/mui', $four_g, $out_g);
						if(isset($out_g[0]) && ! empty($out_g[0])) {
							$result_data['w77yz4j'] = '';
							for($z = 0; $z <= count($out_g[0]) - 1; $z++) {
								if(isset($out_g[0][$z]) && ! empty($out_g[0][$z])) {
									$result_data['w77yz4j'] .= $out_g[0][$z] . ',';
								}
							}
						}
					}
					if(isset($result_data['w77yz4j'])) $result_data['w77yz4j'] = array_map('trim', explode(',', trim($result_data['w77yz4j'], ',')));
				}

				// - Speed td
				$speed_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 4);
				if($speed_td == 'Speed') {
					$speed = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)][5]/td', 1);
					$speed_info = explode(',', trim($speed));
					if(isset($speed_info[0]) && ! empty($speed_info[0]) && strpos($speed_info[0], 'HSPA') != false) {
						$speed_hspa = trim(str_replace(['HSPA'], '', $speed_info[0]));
						if( ! empty($speed_hspa)) $result_data['uointeq3'] = $speed_hspa;

					}
					if(isset($speed_info[1]) && ! empty($speed_info[1]) && strpos($speed_info[1], 'LTE') != false) {
						$speed_lte = trim(str_replace(['LTE', 'LTE-A'], '', $speed_info[1]));
						if( ! empty($speed_lte)) {
							preg_match('/\d+\s+Mbps/mui', $speed_lte, $out_speed_lte);
							if(isset($out_speed_lte[0]) && ! empty($out_speed_lte[0])) {
								$result_data['p4zld10l'] = str_ireplace(['Mbps'], '', $out_speed_lte[0]);
							}
						}
					}
				}

				// - GPRS td
				$gprs_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 5);
				if($gprs_td == 'GPRS') {
					// get gprs
					$technology_gprs = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)][6]/td', 1);
					$result_data['de60w8u'] = GSMarena::getAnswer($technology_gprs);
				}

				// - EDGE td
				$edge_td = GSMARENA::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 6);
				if($edge_td == 'EDGE') {
					// get edge
					$technology_edge = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)][7]/td', 1);
					$result_data['o3kmrtz'] = GSMarena::getAnswer($technology_edge);
				}
				break;
			case 'Launch':
				// - Announced td
				$announced_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 0);
				if($announced_td == 'Announced') {
					// get launch
					$launch_announced = GSMarena::query('//table[' . $i . ']/tr');
					preg_match_all('/[0-9]+\,\s+[\w]+/mu', $launch_announced, $output_array);
					if( ! empty($output_array[0])) {
						$launch_announced_info = $output_array[0][0];
						$launch_announced_info = explode(',', $launch_announced_info);
						$launch_month = strftime("%m", strtotime($launch_announced_info[1]));
						$result_data['zgxvylx'] = $launch_month . '/' . $launch_announced_info[0];
					}
				}

				// - Status td
				$status_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($status_td == 'Status') {
					// get status available
					$status_available = GSMarena::query('//table[' . $i . ']/tr', 1);
				}
				break;
			case 'Body':
				// - Dimensions td
				$dimensions_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 0);
				if($dimensions_td == 'Dimensions') {
					// get body dimensions
					$body_dimensions = GSMarena::query('//table[' . $i . ']/tr/td[2]');
					if($body_dimensions) {
						$body_info = explode('x', $body_dimensions);
						if(isset($body_info[0])) $result_data['qorav98'] = str_replace([' mm', 'mm', 'thickness'], [''],trim($body_info[0]));
						if(isset($body_info[1])) $result_data['65ihv16'] = str_replace([' mm', 'mm', 'thickness'], [''],trim($body_info[1]));
						if(isset($body_info[2])) {
							preg_match('/[\d]+[\.\,]*[\d]*\s+mm/ui', $body_info[2], $output_array);
							if( ! empty($output_array[0])) $result_data['vbryix7'] = str_replace([' mm', 'mm', 'thickness'], [''], $output_array[0]);
						}
					}
				}

				// - Weight td
				$weight_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($weight_td == 'Weight') {
					// get weight
					$body_weight = GSMarena::query('//table[' . $i . ']/tr/td[2]', 1);
					preg_match('/[\d]+/ui', $body_weight, $output_array);
					if( ! empty($output_array[0])) {
						$result_data['uanzwi8'] = $output_array[0];
					}
				}

				// - Build td
				$build_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 2);
				if($build_td == 'SIM') {
					// get SIM
					$sim = GSMarena::query('//table[' . $i . ']/tr/td[2]', 2);
					if($sim) {
						$sim_new = GSMarena::getSim($sim);
						if($sim_new) {
							$result_data['8q7wrlul'] = GSMarena::isValueExists($sim_new, 'Nano-SIM');
							$result_data['0q3ucnsi'] = GSMarena::isValueExists($sim_new, 'dual stand-by');
							$result_data['lawrulap'] = GSMarena::isValueExists($sim_new, 'Micro-SIM');
						}
					}
				} else if($build_td == 'Build') {
					// get body material
					$body_material = GSMarena::query('//table[' . $i . ']/tr/td[2]', 2);
					if($body_material) {
						// get body
						preg_match('/(\w+)\s+body/uim', $body_material, $out_body);
						if(isset($out_body[1]) && ! empty($out_body[1])) {
							$result_data['zwkph17b'] = (array)strtolower(trim($out_body[1]));
						}
						// get back cover
						preg_match('/back\s+(\w+)/uim', $body_material, $out_back_cover);
						if(isset($out_back_cover[1]) && ! empty($out_back_cover[1])) {
							$result_data['3bjbzry'] = ucfirst(trim($out_back_cover[1]));
						} else {
							preg_match('/,(.+)\s+[\w+]+\s+&\s+back/uim', $body_material, $out_back_cover);
							if(isset($out_back_cover[1]) && ! empty($out_back_cover[1])) {
								$result_data['3bjbzry'] = ucfirst(trim($out_back_cover[1]));
							}
						}
						// get frame
						preg_match('/,(.+)\s+frame/uim', $body_material, $out_frame);
						if(isset($out_frame[1]) && ! empty($out_frame[1])) {
							$result_data['3bjbzra'] = ucfirst(trim($out_frame[1]));
						}
					}

					// get SIM
					$sim = GSMarena::query('//table[' . $i . ']/tr/td[2]', 3);
					$result_data['8q7wrlul'] = GSMarena::isValueExists($sim, 'Nano-SIM');
					$result_data['0q3ucnsi'] = GSMarena::isValueExists($sim, 'dual stand-by');
					$result_data['lawrulap'] = GSMarena::isValueExists($sim, 'Micro-SIM');
				}

				// - 'No name' features
				$body_features_td = GSMarena::query('//table[' . $i . ']/tr/td[@class="ttl"]', 3);
				if($body_features_td != null) {
					// get body specifics
					$body_specs = GSMarena::query('//table[' . $i . ']/tr', 4);
					if($body_specs) {
						if(GSMarena::getWaterResistant($body_specs) != null) $result_data['cxeplx1'] = GSMarena::getWaterResistant($body_specs);
						if(GSMarena::getDustResistant($body_specs) != null) $result_data['cxeplx3'] = GSMarena::getDustResistant($body_specs);
					}
				}
				break;
			case 'Display':
				// - Type td
				$type_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 0);
				if($type_td && $type_td == 'Type') {
					// get display type & colors
					$display_type = GSMarena::query('//table[' . $i . ']/tr/td[2]');
					if($display_type) {
						if(strpos($display_type, 'onochrome') != false) {
							$result_data['xxyv5nx'] = 'Monochrome';
							$result_data['8vzzca7'] = 1;
						} else {
							$display = str_ireplace(['capacitive', 'touchscreen'], '', explode(',', $display_type)[0]);
							if( ! empty($display)) {
								if( ! preg_match('/\d+K|\d+M/uim', $display)) {
									$image_info = str_replace('AMOLED or SLCD', 'AMOLED', $display);
									preg_match('/\d{1,}k?\s+colors/mui', $image_info, $out_display_colors);
									if(!isset($out_display_colors[0])) {
										$result_data['xxyv5nx'] = str_replace('(single white color)','',$image_info);
									} else {
										$result_data['8vzzca7'] = str_replace('colors','',$out_display_colors);
									}
								} else {
									preg_match('/\d+K|\d+M/uim', $display, $out_colors);
									if(isset($out_colors[0]) && ! empty($out_colors[0])) $result_data['8vzzca7'] = $out_colors[0];
								}
							}
						}
						preg_match('/[\d]+M|[\d]+K/ui', $display_type, $output_array);
						if(isset($output_array[0]) && ! empty($output_array[0])) {
							$result_data['8vzzca7'] = trim($output_array[0]);
						}
					}
				}

				// - Size td
				$size_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($size_td && $size_td == 'Size') {
					// get display size
					$display_size = GSMarena::query('//table[' . $i . ']/tr/td[2]', 1);
					$display_info = explode(',', $display_size);

					if($display_info) {
						preg_match('/[\d]+[\.|\,][\d]+/ui', $display_info[0], $output_array);
						if( ! empty($output_array)) {
							$result_data['1n820fz'] = $output_array[0];
						}
					}

					if(isset($display_info[1])) {
						preg_match('/\(~([\d]+[\.|\,][\d]+)/ui', $display_info[1], $output_array);
						if( ! empty($output_array[1])) {
							$result_data['zq2ektp'] = round($output_array[1]);
						}
					}
				}

				// - Resolution td
				$resolution_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 2);
				if($resolution_td && $resolution_td == 'Resolution') {
					// get resolution
					$display_resolution = GSMarena::query('//table[' . $i . ']/tr/td[2]', 2);
					preg_match('/([\d]+\sx\s[\d]+)\spix/mi', $display_resolution, $resol_info);
					if(isset($resol_info[1]) && ! empty($resol_info[1])) {
						$width = explode('x', $resol_info[1])[0];
						$height = explode('x', $resol_info[1])[1];
						$result_data['j2p7bju'] = trim($height);
						$result_data['nggks18'] = trim($width);
					}
					preg_match('/([\d]+)\sppi/mi', $display_resolution, $ppi_info);
					if(isset($ppi_info[1]) && ! empty($ppi_info[1])) {
						$result_data['7x8x76o'] = trim($ppi_info[1]);
					}
				}

				// - Multitouch td
				$multitouch_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 3);
				if($multitouch_td && $multitouch_td == 'Multitouch') {
					// get multitouch
					$is_multitouch = GSMarena::query('//table[' . $i . ']/tr/td[2]', 3);
					$result_data['alrhep0'] = GSMarena::getAnswer($is_multitouch);
				}

				// - Protection td
				$protection_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 4);
				if($protection_td && $protection_td == 'Protection') {
					//get display protection
					$display_protection = GSMarena::query('//table[' . $i . ']/tr/td[2]', 4);
					$result_data['59e6c9r'] = str_replace(['(unspecified version)', '(market dependent)', 'To be confirmed'], [''], $display_protection);
				}

				// - 'No name' features td
				$display_features_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 5);
				if($display_features_td && $display_features_td != null) {
					// get display specs
					$display_specs = GSMarena::query('//table[' . $i . ']/tr', 5);
					if($display_specs) {
						$result_data['y23jcrlz'] = GSMarena::isValueExists($display_specs, 'Wide Colour Gamut');
						$result_data['y20jcrlz'] = GSMarena::isValueExists($display_specs, '120 Hz');
						$result_data['ywkph18a'] = GSMarena::isValueExists($display_specs, 'DCI-P3');
					}
				}
				break;
			case 'Platform':
				// - OS td
				$os_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 0);
				if($os_td && $os_td == 'OS') {
					// get platform O
					$platform_os = GSMarena::query('//table[' . $i . ']/tr/td[2]');

					// get type of device
					$not_gsm_device = GSMarena::query('//span[@id="non-gsm"]/text()');
					$watch_os = GSMarena::getWatchOs($platform_os);

					if($watch_os || ! empty($not_gsm_device)) {
						$result_data['drbmx1r'] = 2;
					} else {
						$result_data['drbmx1r'] = 1;
					}

					$available_os = explode(',', $platform_os);
					$result_data['ui65qcn'] = $available_os[0]; // get os
					if(isset($available_os[1]) && ! empty($available_os[1])) {
						$result_data['ui71qcn'] = trim(str_ireplace(['upgradable to', 'upgradаble to', 'planned upgrade to'], [''], trim($available_os[1])));
					}

					$result_data['0v8w2sz'] = GSMarena::isValueExists($platform_os, 'iOS');
					$result_data['a5sj3l2'] = GSMarena::isValueExists($platform_os, 'indow');
					$result_data['vxq3g1f'] = GSMarena::isValueExists($platform_os, 'BlackBerry');
					$result_data['llulwif'] = GSMarena::isValueExists($platform_os, 'ndroid');
				}

				// - Chipset td
				$chipset_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($chipset_td && $chipset_td == 'Chipset') {
					$chipset = GSMarena::query('//table[5]/tr', 1);
				}

				// CPU td
				$cpu_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 2);
				if($cpu_td && $cpu_td == 'CPU') {
					// get CPU
					$cpu = GSMarena::query('//table[' . $i . ']/tr/td[2]', 2);
					$cpu_info = explode(' ', $cpu);

					// core
					if(isset($cpu_info[0]) && ! empty($cpu_info[0])) {
						if(in_array($cpu_info[0], ['Deca-core', 'Dual-core', 'Quad-core', 'Octa-core', 'Hexa-core'])) {
							$result_data['y5xo6x5'] = intval(str_ireplace(['Deca-core', 'Dual-core', 'Quad-core', 'Octa-core', 'Hexa-core'], ['10', '2', '4', '8', '6'], $cpu_info[0])); // core //   +
						}
					}

					// cpu freq
					preg_match_all('/\d+x\d+\.\d+|\d+\.\d+\s+/uim', $cpu, $out_cpu_freg);
					if(isset($out_cpu_freg[0][0]) && ! empty($out_cpu_freg[0][0])) {
						$result_data['y5xo6x6'] = trim($out_cpu_freg[0][0]);
					}
					if(isset($out_cpu_freg[0][1]) && ! empty($out_cpu_freg[0][1])) {
						$result_data['y5xo6x6'] .= ' & ' . trim($out_cpu_freg[0][1]); // freq
					}

					if(isset($result_data['y5xo6x5']) && $result_data['y5xo6x5'] != '8') {
						if(isset($out_cpu_freg[0][2]) && ! empty($out_cpu_freg[0][2])) {
							$result_data['y5xo6x6'] .= ' & ' . trim($out_cpu_freg[0][2]); // freq
						}
					}

					preg_match_all('/ghz\s+(\w+-*[^)]\d*)\s*/uim', $cpu, $out_cpu_main);
					if(isset($out_cpu_main[1]) && ! empty($out_cpu_main[1])) {
						$result_data['y5xo6x4'] = trim($out_cpu_main[1][0]);
					}
					if(isset($out_cpu_main[1][1]) && ! empty($out_cpu_main[1][1])) {
						if($out_cpu_main[1][1] != $result_data['y5xo6x4'] || (trim($out_cpu_main[1][1]) == 'Cortex')) { // || trim($out_cpu_main[1][1]) != 'Cortex'
							$result_data['y5xo6x4'] .= ' & ' . trim($out_cpu_main[1][1]);
						}
					}
				}

				// GPU td
				$gpu_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 3);
				if($gpu_td && $gpu_td == 'GPU') {
					$gpu = $dom->query('//table[' . $i . ']/tr/td[2]')->item(3)->textContent ?? null;
					if($gpu) {
						$gpu_into = preg_split("/- EMEA/ui", $gpu, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
						if(isset($gpu_into[0][0]) && ! empty($gpu_into[0][0])) {
							preg_match('/Adreno\s+\d+/', $gpu_into[0][0], $out_adreno_gpu);
							if(isset($out_adreno_gpu[0]) && ! empty($out_adreno_gpu[0])) {
								$result_data['4kzmswo'][] = trim($out_adreno_gpu[0]);
							} else {
								$result_data['4kzmswo'][] = trim($gpu_into[0][0]);
							}
							if(isset($gpu_into[1][0]) && ! empty($gpu_into[1][0])) {
								preg_match('/Adreno\s+\d+/', $gpu_into[1][0], $out_other_gpu);
								if(isset($out_other_gpu[0]) && ! empty($out_other_gpu[0])) {
									$result_data['4kzmswo'][] = trim($out_other_gpu[0]);
								} else {
									$result_data['4kzmswo'][] = trim($gpu_into[1][0]);
								}
							}
						}
					}
				}
				break;
			case 'Memory':
				// - Card slot td
				$card_slot_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]');
				if($card_slot_td && $card_slot_td == 'Card slot') {
					// get memory card slot
					$is_memory_card_slot = GSMarena::query('//table[' . $i . ']/tr/td[2]');
					if(strpos($is_memory_card_slot, 'No') !== false) {
						$result_data['yz90cwl'] = '-';
					} else {
						$result_data['yz90cwl'] = '+';
					}
				}

				// - Internal
				$internal_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($internal_td && $internal_td == 'Internal') {
					$memory_internal = GSMarena::query('//table[' . $i . ']/tr/td[2]', 1);
				}
				break;
			case 'Camera':
				// - Primary td
				$primary_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]');
				if($primary_td && $primary_td == 'Primary') {
					// get camera primary
					$cam_primary = GSMarena::query('//table[' . $i . ']/tr/td[2]');
					$cam_primary_info = GSMarena::getPrimaryCamera($cam_primary);
					$result_data['gn4gn6xk'] = GSMarena::isValueExists($cam_primary_info, 'utofocus');
					$result_data['zrru3eek'] = GSMarena::isValueExists($cam_primary_info, 'flash');
					// get led flash
					$result_data['jefetfa2'] = GSMarena::isValueExists($cam_primary_info, 'LED');
				}

				// - Features td
				$features_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($features_td && $features_td == 'Features') {
					// get camera features
					$cam_features = GSMarena::query('//table[' . $i . ']/tr', 1);
					if($cam_features) {
						// get panorama
						$result_data['gn4gn6xz'] = GSMarena::isValueExists($cam_features, 'anorama');
					}
				}

				// - Video td
				$video_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 2);
				if($video_td && $video_td == 'Video') {
					// get camera video
					$cam_video = GSMarena::query('//table[' . $i . ']/tr/td[2]', 2);
					if(isset($cam_video)) {
						$cam_info_new = explode(',', $cam_video)[0]; // video 1;
						preg_match('/\d+p/mui', $cam_info_new, $out_cam);
						if(isset($out_cam[0]) && ! empty($out_cam[0])) {
							$result_data['t9q0h7hd'] = trim($out_cam[0]);
						}

						if(isset(explode(',', $cam_video)[1])) {
							$cam_video_2 = trim(explode(',', $cam_video)[1]); // video 2;
							preg_match('/\d+p/mui', $cam_video_2, $out_video_2);
							if(isset($out_video_2[0]) && ! empty($out_video_2[0])) {
								$result_data['8041luk6'] = $out_video_2[0];
							}

						}
					}
				}

				// - Secondary td
				$secondary_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 3);
				if($secondary_td && $secondary_td == 'Secondary') {
					// get camera secondary
					$cam_secondary = GSMarena::query('//table[' . $i . ']/tr/td[2]', 3);
					$result_data['c4awfagk'] = GSMarena::check($cam_secondary, 'face detection');
					$cam_secondary_info = explode(',', $cam_secondary);
					if(isset($cam_secondary_info[0])) {
						preg_match('/(\d+)\s+MP/mui', $cam_secondary_info[0], $out_sec_cam);
						if(isset($out_sec_cam[1]) && ! empty($out_sec_cam[1])) {
							$result_data['06wzu4yz'] = $out_sec_cam[1];
						}
						if(isset($cam_secondary_info[1])) {
							// get led flash
							if( ! isset($result_data['jefetfa2'])) $result_data['jefetfa2'] = GSMarena::isValueExists($cam_secondary_info[1], 'LED');
						}
					} else {
						preg_match('/(\d+)\s+MP/mui', $cam_secondary, $out_sec_cam);
						if(isset($out_sec_cam[1]) && ! empty($out_sec_cam[1])) {
							$result_data['06wzu4yz'] = $out_sec_cam[1];
						}
					}
				}

				if(empty($result_data['lggn0m2'])) $result_data['lggn0m2'] = '-';
				if(empty($result_data['t9q0h7hd'])) $result_data['t9q0h7hd'] = '-';

				break;
			case 'Sound':
				// - Alert types td
				$alert_types_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]');
				if($alert_types_td && $alert_types_td == 'Alert types') {
					// get sound alert types
					$sound_alerts = GSMarena::query('//table[' . $i . ']/tr');
					$result_data['u8sj5wc'] = GSMarena::isValueExists($sound_alerts, 'ibratio');
				}

				// - Loudspeaker td
				$loudspeaker_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 1);
				if($loudspeaker_td && $loudspeaker_td == 'Loudspeaker ') {
					// get sound loudspeaker
					$sound_loudspeaker = GSMarena::query('//table[' . $i . ']/tr/td[2]', 1);
					$result_data['8l2ljo2'] = GSMarena::getAnswer($sound_loudspeaker);
					$result_data['yh7xh42'] = GSMarena::isValueExists($sound_loudspeaker, 'THX');
					// get dual loudspeaker
					$result_data['yq2jcrl2'] = GSMarena::check($sound_loudspeaker, 'dual');
				}

				// - 3.5mm jack td
				$audio_jack_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', 2);
				if($audio_jack_td && $audio_jack_td == '3.5mm jack ') {
					// get sound 3.5m jack
					$sound_jack = GSMarena::query('//table[' . $i . ']/tr/td[2]', 2);
					$result_data['yh7xh3q'] = GSMarena::getAnswer($sound_jack);
				}

				// - 'No name' features td
				$sound_features_td = GSMarena::query('//table[' . $i . ']/tr/td[@class="ttl"]', 3);
				if($sound_features_td && $sound_features_td != null) {
					// get sound specs
					$sound_specs = GSMarena::query('//table[' . $i . ']/tr/td[2]', 3);
					if($sound_specs) {
						// get hi-fi audio
						if(strpos($sound_specs, '24-bit/192kHz')) {
							$result_data['yh7xh38'] = '+';
						}
						// get Dirac HD sound
						$result_data['yh7xh43'] = GSMarena::isValueExists($sound_specs, 'Dirac HD sound');
					}
				}

				break;
			case 'Comms':
				for($k = 0; $k <= 7; $k++) {
					$coms_value_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', $k);
					switch($coms_value_td) {
						case 'WLAN':
							// get comms wlan
							$comms_wlan = GSMarena::query('//table[' . $i . ']/tr', $k);
							if($comms_wlan) {
								$wlan_info = GSMarena::getWlan($comms_wlan);
								if($wlan_info) $result_data['2pinrcv'] = $wlan_info;
							}
							break;
						case 'Bluetooth':
							// get comms bluetooth
							$comms_bluetooth = GSMarena::query('//table[' . $i . ']/tr', $k);
							preg_match_all('/([0-9]\.[0-9])/mu', $comms_bluetooth, $output_array);
							if(isset($output_array[0][0])) $result_data['p4zld5l'] = $output_array[0][0];

							// get a2dp
							$result_data['yh7xh59'] = GSMarena::check($comms_bluetooth, 'A2DP');
							// get LE (low energy)
							$result_data['8q7wrluz'] = GSMarena::check($comms_bluetooth, 'LE');
							// get aptX
							$result_data['yh7xh60'] = GSMarena::check($comms_bluetooth, 'aptX');
							break;
						case 'GPS':
							// get comms gps & glonass
							$comms_gps = GSMarena::query('//table[' . $i . ']/tr', $k);
							$result_data['yfvshn2'] = GSMarena::getAnswer($comms_gps);
							$result_data['39ji8mm'] = GSMarena::isValueExists($comms_gps, 'GLON');
							$result_data['x1xgsbl'] = GSMarena::isValueExists($comms_gps, 'A-GPS');
							break;
						case 'NFC':
							// get comms nfc
							$comms_nfc = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							$result_data['9ee4viy'] = GSMarena::getAnswer($comms_nfc);
							break;
						case 'Infrared port':
							// get comms infrared
							$comms_nfc = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							$result_data['hwst1n7'] = GSMarena::getAnswer($comms_nfc);
							break;
						case 'Radio':
							// get comms radio
							$comms_radio = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							$result_data['tix99ot'] = GSMarena::getAnswerNo($comms_radio);
							// get RDS
							if(stripos(trim($comms_radio), 'RDS') !== false) $result_data['p4zld1l7'] = GSMarena::getAnswerNo($comms_radio);
							break;
						case 'USB':
							// get comms usb
							$comms_usb = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							preg_match('/^No$/u', $comms_usb, $output_array);
							if( ! empty($output_array[0])) {
								$result_data['2q8o92fk'] = '-';
							}

							// get type-c
							if(GSMarena::isValueExists($comms_usb, 'ype-C') == '+') {
								preg_match('/\d+[\.*|\,*]\d+[\s+\w+]+/uim', $comms_usb, $out_type_c);
								if(isset($out_type_c) && ! empty($out_type_c[0])) {
									$result_data['rmjj6m5t'] = str_ireplace(['reversible connector'], [''], trim($out_type_c[0]));
								}
							}

							$result_data['99rtfuj'] = GSMarena::isValueExists($comms_usb, 'agnetic');
							$result_data['0arcae64'] = GSMarena::isValueExists($comms_usb, '2');
							$result_data['p7s2uenu'] = GSMarena::isValueExists($comms_usb, '3');
							$result_data['p85t8s8z'] = GSMarena::isValueExists($comms_usb, 'micro');
							$result_data['9qsw0l7d'] = GSMarena::check($comms_usb, 'On-The-Go');
							break;
					}
				}
				break;
			case 'Features':
				for($k = 0; $k <= 5; $k++) {
					$features_value_td = GSMarena::query('//table[' . $i . ']/tr[not(@data-spec-optional)]/td[@class="ttl"]', $k);
					switch(true) {
						case ($features_value_td == 'Sensors'):
							// get features sensors
							$feat_sensors = GSMarena::query('//table[' . $i . ']/tr', $k);
							$result_data['h1ddzrt'] = GSMarena::check($feat_sensors, 'ccelerometer');
							$result_data['ywtcejg'] = GSMarena::check($feat_sensors, 'gyro');
							$result_data['x399jxz'] = GSMarena::check($feat_sensors, 'barometer');
							$result_data['x0xgsbn'] = GSMarena::check($feat_sensors, 'compass');
							if(!isset($result_data['c4awfagk']) || empty($result_data['c4awfagk'])) {
								$result_data['c4awfagk'] = GSMarena::check($feat_sensors, 'Face');
							}
							$result_data['rsub3l9c'] = GSMarena::check($feat_sensors, 'Fingerprint');
							$result_data['h88pkmd1'] = GSMarena::check($feat_sensors, 'oximity');
							break;
						case ($features_value_td == 'Messaging'):
							// get features messaging
							$feat_messaging = GSMarena::query('//table[' . $i . ']/tr', $k);
							if($feat_messaging) {
								$result_data['8q7wrlu5'] = GSMarena::isValueExists($feat_messaging, 'IM');
							}
							break;
						case ($features_value_td == 'Browser'):
							// get browser
							$feat_browser = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							$result_data['fdfoul1'] = str_ireplace(['Yes', 'No'], ['+', '-'], $feat_browser);
							break;
						case (strlen($features_value_td) == 2):
							// get features audio & video formats
							$feat_browser = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							$result_data['f7lsmmw9'] = GSMarena::check($feat_browser, 'MP3');
							$result_data['x055z520'] = GSMarena::check($feat_browser, 'MP4');
							$result_data['8j6be1ko'] = GSMarena::check($feat_browser, 'DivX');
							$result_data['xc4bb9kc'] = GSMarena::check($feat_browser, 'XviD');
							$result_data['crrbpcar'] = GSMarena::check($feat_browser, 'H.265');
							$result_data['xd942mit'] = GSMarena::check($feat_browser, 'WMV');
							$result_data['am1zgml8'] = GSMarena::check($feat_browser, 'WAV');
							$result_data['t1inmosa'] = GSMarena::check($feat_browser, 'FLAC');
							$result_data['lnk8dr8h'] = GSMarena::check($feat_browser, 'eAAC');
							$result_data['7zq7neoh'] = GSMarena::check($feat_browser, 'WMA');
							$result_data['tfuq45ng'] = GSMarena::check($feat_browser, 'AAX');
							$result_data['f3n8nqp4'] = GSMarena::check($feat_browser, 'AIFF');
							$result_data['f3n8nq17'] = GSMarena::check($feat_browser, 'ASF');
							$result_data['f3n8nq18'] = GSMarena::check($feat_browser, 'FLV');
							$result_data['f3n8nq19'] = GSMarena::check($feat_browser, 'M4V');
							$result_data['f3n8nq20'] = GSMarena::check($feat_browser, 'WEBM');
							$result_data['f3n8nq21'] = GSMarena::check($feat_browser, '3G2');
							$result_data['f3n8nq22'] = GSMarena::check($feat_browser, '3GP');
							$result_data['f3n8nqp5'] = GSMarena::check($feat_browser, 'AWB');
							$result_data['f3n8nqp6'] = GSMarena::check($feat_browser, 'DFF');
							$result_data['f3n8nqp7'] = GSMarena::check($feat_browser, 'IMY');
							$result_data['f3n8nqp8'] = GSMarena::check($feat_browser, 'RTX');
							$result_data['f3n8nqp9'] = GSMarena::check($feat_browser, 'OGA');
							$result_data['f3n8nq11'] = GSMarena::check($feat_browser, 'OTA');
							$result_data['f3n8nq10'] = GSMarena::check($feat_browser, 'MXMF');
							$result_data['f3n8nq13'] = GSMarena::check($feat_browser, 'AMR');
							$result_data['f3n8nq14'] = GSMarena::check($feat_browser, 'APE');
							$result_data['f3n8nq15'] = GSMarena::check($feat_browser, 'DSF');
							$result_data['f3n8nq16'] = GSMarena::check($feat_browser, 'OGG');
							$result_data['f3n8nq23'] = GSMarena::check($feat_browser, 'PCM');

							// get document viewer
							$result_data['yq2jcrl9'] = GSMarena::check($feat_browser, 'ocument viewer');

							// get document editor
							$result_data['yq2jcr10'] = GSMarena::check($feat_browser, 'ocument editor');

							// get wireless charging
							$result_data['xc2onhy'] = GSMarena::isValueExists($feat_browser, 'eless charging');

							// get features specs
							$feat_specs = GSMarena::query('//table[' . $i . ']/tr', $k);
							$result_data['27s8wl4'] = GSMarena::isValueExists($feat_specs, 'ast battery');
							break;
						case ($features_value_td == 'Alarm'):
							$alarm_info = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							if($alarm_info) $result_data['iw93r5f8'] = GSMarena::getAnswer($alarm_info);
							break;
						case ($features_value_td == 'Clock'):
							$clock_info = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							if($clock_info) $result_data['qfkph10b'] = GSMarena::getAnswer($clock_info);
							break;
					}
				}
				break;

			case 'Battery':
				// - 'No name' removable td
				$removable_td = GSMarena::query('//table[' . $i . ']/tr/td[@class="ttl"]');
				if($removable_td) {
					// get battery specs
					$battery = GSMarena::query('//table[' . $i . ']/tr/td[2]');
					$result_data['c220c9j'] = GSMarena::getBatteryReplacement($battery);
				}

				for($k = 1; $k <= 2; $k++) {
					$value_td = GSMarena::query('//table[' . $i . ']/tr/td[@class="ttl"]', $k);
					switch($value_td) {
						case 'Talk time':
							// get battery talk time
							$battery_talk_time = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							preg_match('/(\d+)\s+h\s+\(3G\)/uim', $battery_talk_time, $out_talk_time);
							if(isset($out_talk_time[1]) && ! empty($out_talk_time[1])) {
								$result_data['zuqqmwi3'] = $out_talk_time[1];
							}

							break;
						case 'Music play':
							// get battery music play time
							$battery_music_time = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							preg_match('/(\d+)/uim', $battery_music_time, $out_music_play);
							if(isset($out_music_play[0]) && ! empty($out_music_play[0])) {
								$result_data['6ojsm29w'] = $out_music_play[0];
							}

							break;
						case 'Stand-by':
							// get battery music play time
							$battery_stand_time = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							preg_match('/([\d]{2,})\s+h/umi', $battery_stand_time, $output_array);
							if(isset($output_array[1]) && ! empty($output_array[1])) {
								$result_data['qldrwtm'] = $output_array[1];
							} else {
								$result_data['qldrwtm'] = '';
							}
							break;
					}
				}
				break;
			case 'Misc':
				for($k = 0; $k <= 2; $k++) {
					$value_td = GSMarena::query('//table[' . $i . ']/tr/td[@class="ttl"]', $k);
					switch($value_td) {
						case 'Colors':
							// get misc specs
							$misc_colors = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							if($misc_colors) {
								$misc_colors_info = preg_replace('/\d+\s+-\s+/uim', "", $misc_colors);
								$result_data['ywkph10b'] = array_map('trim', explode(',', $misc_colors_info));
							}
							break;
						case 'Price':
							// get misc price
							$misc_price = GSMarena::query('//table[' . $i . ']/tr', $k);
							$price = preg_match_all('/([0-9]+\s+[A-Z]+)/mu', $misc_price, $output_array);
							if(isset($output_array[0][0])) {
								$price_info = explode(' ', $output_array[0][0]);
								$result_data['3n68sce'] = [$price_info[1] => $price_info[0]];
							}
							break;
						case 'SAR';
							// get SAR value
							$sar = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							if($sar) {
								$sar_info = preg_split('/\s{2,}/ui', trim($sar));
								$sar_info = array_diff($sar_info, array(''));
								if(isset($sar_info[0])) $result_data['5cp2ol9j'] = str_replace(['(head)', 'W/kg'], [''], $sar_info[0]);
								if(isset($sar_info[1])) $result_data['owpcmmmy'] = str_replace(['(body)', 'W/kg'], [''], $sar_info[1]);
							}
							break;
						case 'SAR EU';
							// get SAR EU value
							$sar_eu = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							if($sar_eu) {
								$sar_info = preg_split('/\s{2,}/ui', trim($sar_eu));
								$sar_info = array_diff($sar_info, array(''));
								if(isset($sar_info[0])) $result_data['psbzu2e9'] = str_replace(['(head)', 'W/kg'], '', $sar_info[0]);
								if(isset($sar_info[1])) $result_data['uuapl9gw'] = str_replace(['(body)', 'W/kg'], '', $sar_info[1]);
							}
					}
				}
				break;
			case 'Tests':
				for($k = 0; $k <= 5; $k++) {
					$tests_value_td = GSMarena::query('//table[' . $i . ']/tr/td[@class="ttl"]', $k);
					switch($tests_value_td) {
						case 'Performance':
							// get test info
							$test_performance = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);

							preg_match('/Basemark OS II:\s*(\d+)/uim', $test_performance, $out_basemark);
							if(isset($out_basemark[1]) && ! empty($out_basemark[1])) {
								$result_data['a7xo6x6'] = trim($out_basemark[1]);
							} // basemark os

							preg_match('/Basemark OS II 2.0:\s*(\d+)/uim', $test_performance, $out_basemark_two);
							if(isset($out_basemark_two[1]) && ! empty($out_basemark_two[1])) {
								$result_data['a7xo1x6'] = trim($out_basemark_two[1]);
							} // basemark os 2

							preg_match('/Basemark X:\s*(\d+)/uim', $test_performance, $out_basemark_x);
							if(isset($out_basemark_x[1]) && ! empty($out_basemark_x[1])) {
								$result_data['a8xo6x6'] = trim($out_basemark_x[1]);
							} // basemark x
							break;
						case 'Display':
							// get test display
							$test_display = GSMarena::query('//table[' . $i . ']/tr', $k);
							if($test_display) {
								preg_match('/(\d+):*\d*\s+\(nominal\)/uim', $test_display, $out_nominal_contrast);
								if(isset($out_nominal_contrast[1]) && ! empty($out_nominal_contrast[1])) $result_data['y22jcrla'] = $out_nominal_contrast[1]; // nominal contrast
								preg_match('/(\d+\.*\d*)\s+\(sunlight\)/uim', $test_display, $out_sunlight_contrast);
								if(isset($out_sunlight_contrast[1]) && ! empty($out_sunlight_contrast[1])) $result_data['y23jcrla'] = $out_sunlight_contrast[1]; // sunlight contrast
							}
							break;
						case 'Camera':
							// get test camera
							$test_camera = GSMarena::query('//table[' . $i . ']/tr', $k);
							break;
						case 'Loudspeaker':
							// get test loudspeaker
							$test_loudspeaker = GSMarena::query('//table[' . $i . ']/tr', $k);
							if($test_loudspeaker) {
								$test_loudspeaker_value = explode('/', $test_loudspeaker);
								if(isset($test_loudspeaker_value[0]) && ! empty($test_loudspeaker_value[0])) {
									preg_match('/\d+/ui', $test_loudspeaker_value[0], $out_loudspeak_voice);
									if(isset($out_loudspeak_voice[0]) && ! empty($out_loudspeak_voice[0])) $result_data['yh7xh39'] = $out_loudspeak_voice[0];
								}
								if(isset($test_loudspeaker_value[1]) && ! empty($test_loudspeaker_value[1])) {
									preg_match('/\d+/ui', $test_loudspeaker_value[1], $out_loudspeak_noise);
									if(isset($out_loudspeak_noise[0]) && ! empty($out_loudspeak_noise[0])) $result_data['yh7xh40'] = $out_loudspeak_noise[0];
								}
								if(isset($test_loudspeaker_value[2]) && ! empty($test_loudspeaker_value[2])) {
									preg_match('/\d+/ui', $test_loudspeaker_value[2], $out_loudspeak_ring);
									if(isset($out_loudspeak_ring[0]) && ! empty($out_loudspeak_ring[0])) $result_data['yh7xh41'] = $out_loudspeak_ring[0];
								}
							}
							break;
						case 'Audio quality':
							// get test audio quality
							$test_audio = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							$audio_quality_info = explode('/', $test_audio);
							$result_data['3t8uo9z6'] = (isset($audio_quality_info[0])) ? GSMarena::getNoiseCrosstalk($audio_quality_info[0]) : null;
							$result_data['m93am75k'] = (isset($audio_quality_info[1])) ? GSMarena::getNoiseCrosstalk($audio_quality_info[1]) : null;
							break;
						case 'Battery life':
							// get test battery life
							$test_battery = GSMarena::query('//table[' . $i . ']/tr/td[2]', $k);
							if($test_battery) {
								preg_match('/(\d+)/uim', $test_battery, $out_rating);
								if(isset($out_rating[0]) && ! empty($out_rating[0])) {
									$result_data['qwkph25b'] = trim($out_rating[0]);
								}
							}
							break;
					}
				}
				break;
		}
		foreach($result_data as $index => $value) {
			if(is_string($value) && empty($value)) unset($result_data[$index]);
		}
		$items[$url] = $result_data;
	}

	// get device type if don't have platform os
	if( ! isset($items[$url]['drbmx1r'])) {
		$not_gsm_device = GSMarena::query('//span[@id="non-gsm"]/p/text()', 0);
		if( ! empty($not_gsm_device)) {
			$items[$url]['drbmx1r'] = 2;
		} else {
			if(isset($items[$url]['qorav98'])) {
				if($items[$url]['qorav98'] > 60) $items[$url]['drbmx1r'] = 1;
			}
			if(isset($items[$url]['qorav98'])) {
				if($items[$url]['qorav98'] < 60) $items[$url]['drbmx1r'] = 2;
			}
		}
	}

	// save data to db
	if($webpage != null) {
		$webpage->version = GSMarena::$version;
		$webpage->desc = Json::encode($items[$url]);
		$webpage->save();
	} else {
		$webpage = new WebPage([
			'path_hash' => $path_hash,
			'source'    => 'gsmarena',
			'url'       => $url,
			'version'   => GSMarena::$version,
			'desc'      => Json::encode($items[$url]),
			'format'    => 'json',
		]);
		$webpage->save();
	}
}

$code = Sheet::rf('@config/product/specs.csv', ['indexFrom' => 'code']);
echo Render::render($items, $code, ['category','group','title_ru','title_en', 'code', 'units_en']);
//H::print_r($items);
