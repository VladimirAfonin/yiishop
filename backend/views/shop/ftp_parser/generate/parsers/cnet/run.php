<?
use app\helpers\Clerk;
use yii\helpers\Json;
use app\helpers\H;
use yii\helpers\Url;
use app\helpers\WebPage;

global $dom;

class Cnet
{
	public static $version = 3;
	public static $main_url = 'https://www.cnet.com/search/';
	public static $cnetUrl = 'https://cnet.com';

	const PHONE = 'phone';
	const VR = 'vr';

	public static $itemUrl = [
		self::PHONE => 'https://www.cnet.com/topics/phones/products{page}',
		self::VR    => 'https://www.cnet.com/topics/wearable-tech/products{page}?filter=12-product-type_vr-ar-headset',
	];

	public static $errorMsg = "Unfortunately";

	public static $cleaners = [
		'head'   => "/\<head([\s\S]+?)\<\/head\>/",
		'script' => "/\<script([\s\S]+?)\<\/script\>/",
		'style'  => "/\<style([\s\S]+?)\<\/style\>/",
	];

	public static function getDataFromApi($endpoint, $params = [], $redirect = false)
	{
		$url = self::makeUrl($endpoint, $params);
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $redirect);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}

	/**
	 * @param $url
	 * @param array $urlParams
	 * @param array $ignoreParams
	 * @return string
	 */
	public static function makeUrl($url, $urlParams = [], $ignoreParams = []): string
	{
		foreach($ignoreParams as $key) {
			unset($urlParams[$key]);
		};
		if(!empty($urlParams)) {
			$url .= "?" . http_build_query($urlParams);
		}
		return $url;
	}

	public static function query($pattern, $count = 0)
	{
		global $dom;
		return $dom->query($pattern)->item($count)->nodeValue ?? null;
	}

	/**
	 * load links with phones
	 * @param string $category
	 * @return array
	 */
	public static function getLinks($category='phone')
	{
		$flag = true;
		$res_arr = [];
		$i = 1;
		do {
			$page = $i===1?'/':"/$i/";
			WebPage::$clean = ['script'];
			$url = str_replace('{page}',$page, self::$itemUrl[$category]);
			$html = WebPage::get($url);
			$dom = WebPage::dom($html);
			// checking for 'no-result'
			$check = $dom->query('//section[@id="dfllResults"]/div[@class="items"]/h2/text()')->item(0)->nodeValue ?? null;
			if(strpos($check, Cnet::$errorMsg) !== false) $flag = false;

			$a = 0;
			do {
				$elem = $dom->query("//section[contains(@class,'col-3 searchItem product')]")->item($a)->nodeValue ?? null;
				$item_title = $dom->query("//section[contains(@class,'col-3 searchItem product')]/div[@class='itemInfo']/a/h3")->item($a)->nodeValue ?? null;
				if($item_title) $res_arr[$i][$a]['title'] = trim(preg_replace('#\s+#', ' ', $item_title));
				if($item_title) {
					$res_arr[$i][$a]['url'] = Cnet::$cnetUrl . ($dom->query("//section[contains(@class,'col-3 searchItem product')]/div[@class='itemInfo']/a/@href")->item($a)->nodeValue ?? null);
				}
				$a++;
			} while($elem);

			$i++;
		} while($flag);

		foreach($res_arr as $k => $item) {
			foreach($item as $value) {
				$products[] = $value;
			}
		}

		return $products;
	}

	/**
	 * get links from file
	 * @param string $category
	 * @return array|mixed
	 */
	public static function links($category = 'phone')
	{
		$path = Url::to("@-generate/runtime/cnet-files-map_cnet_$category.json");
		$period = 3600 * 24; // 1 day
		if(file_exists($path)) {
			$cron_time = filemtime($path);
			if(time() - $cron_time >= $period) {
				$links = Cnet::getLinks($category);
				file_put_contents($path, Json::encode($links));
				return $links;
			} else {
				return $links = Json::decode(file_get_contents($path));
			}
		} else {
			$links = Cnet::getLinks($category);
			file_put_contents($path, Json::encode($links));
			return $links;
		}
	}
}

//$products = array_merge(Cnet::links(Cnet::PHONE),$products??[]);
$products = array_merge(Cnet::links(Cnet::VR)   ,$products??[]);

//$products = array_slice($products,0,2100);
// for testing
//$products = array_filter($products, function ($k) {return $k % 250 === 0;}, ARRAY_FILTER_USE_KEY);
$clerk = new Clerk('@-generate/clerks/cnet-run.txt', ['total' =>count($products)]);
// result summary
$data = [];
/* get list of product */
foreach($products as $k => $item) {
	$clerk->tick(['url'=>$item['url']]);

	WebPage::$clean = ['script'];
	$html = WebPage::get($item['url']);
	$dom = WebPage::dom($html);
	// get rating
	$rating_div = Cnet::query("//div[contains(@class,'ratings')]");
	if($rating_div) {
		$item['rating']['Overall'] = floatval(Cnet::query("//div[contains(@class,'ratings')]/div[@class='col-1 overall']/div/span[@class='text']/text()"));
		$z = 0;
		while($res = Cnet::query('//div[@id="editorSubRating"]/ul[@class="ratingsBars edBars"]/li[@class="ratingBarStyle"]', $z)) {
			$rating_value = preg_split('#\s+#mui', trim($res));
			$item['rating'][$rating_value[0]] = floatval($rating_value[1]);
			$z++;
		}
		$item['rating'] = array_map(function ($item) {
			return is_string($item) ? trim(preg_replace('#\s+#', ' ', ($item))) : $item;
		}, $item['rating']);
	}

	// get prices
	$elem = Cnet::query('//div[@section="wtbSmall"]/h3[@class="wtbHed"]/a/@href');
	if($elem) {
		$item['url_offers'] = Cnet::$cnetUrl . $elem;
		$html_offers = WebPage::get($item['url_offers']);
		$dom = WebPage::dom($html_offers);

		$a = 0;
		do {
			$elem = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]', $a);
			$item['offers'][$a]['price'] = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]/div[@class="col-1 totalPrice"]/a/text()', $a);
			$item['offers'][$a]['seller'] = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]/div[@class="col-2 productStore"]/@title', $a);
			$item['offers'][$a]['link'] = Cnet::query('//div[@class="resellerContent"]/div[@class="row"]/div[@class="col-1 seeItBtn"]/a/@href', $a);
			$a++;
		} while($elem);
		array_pop($item['offers']);

		$data[] = $item;
	}
}

// print summary array
if(isset($data)) H::print_r($data);
file_put_contents(Url::to('@-generate/files/cnet-date.json'),JSON::encode($data));