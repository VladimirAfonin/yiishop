<?
use backend\views\shop\test\Sheet;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use backend\entities\WebPage;
use backend\entities\Render;
use backend\views\shop\test\H;

$targets = [
    // iphone 7
//	3334=>'https://www.epey.com/akilli-telefonlar/apple-iphone-7.html',
//	3203 => 'https://www.gsmarena.com/apple_iphone_7-8064.php',
//	// iphone 8
//	3333=>'https://www.epey.com/akilli-telefonlar/apple-iphone-8.html',
//	3208=>'https://www.epey.com/akilli-telefonlar/apple-iphone-8-256gb.html',
//	3204 => 'https://www.gsmarena.com/apple_iphone_8-8573.php',
//	// iphone x
//	3320=>'https://www.epey.com/akilli-telefonlar/apple-iphone-x.html',
//	3205 => 'https://www.gsmarena.com/apple_iphone_x-8858.php',
//	// galaxy s9
//	3322=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9.html',
//	3200 => 'https://www.gsmarena.com/samsung_galaxy_s9-8966.php',
//    // razer phone
//	3330=>'https://www.epey.com/akilli-telefonlar/razer-phone.html',
//	3202 => 'https://www.gsmarena.com/_razer_phone-8923.php',
//    // xiaomi redmi 5
//	3318=>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-5a.html',
//	3207 => 'https://www.gsmarena.com/xiaomi_redmi_5a-8898.php',

	3318=>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-4x-32gb.html',
	3207 =>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-4x-64gb.html',
	3200=>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-4x.html', // main
    3210=>'https://www.gsmarena.com/xiaomi_redmi_4_(4x)-8608.php',

    4218=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-5.html', // main
    4219=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-5-dual-sim.html',
    4220=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-note-5-64gb.html',
    4221=>'https://www.gsmarena.com/samsung_galaxy_note5-7431.php',

	4000=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus.html',
    4001=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-128gb-duos.html',
    4002=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-duos.html',
    4003=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus-256gb.html',
    4004=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9-plus.html',
    3201 =>'https://www.gsmarena.com/samsung_galaxy_s9+-8967.php',




//	3339=>'https://www.epey.com/akilli-telefonlar/meizu-m6.html',
//	3325=>'https://www.epey.com/akilli-telefonlar/meizu-m6-note.html',
//	3329=>'https://www.epey.com/akilli-telefonlar/sony-xperia-xa1.html',
//	3317=>'https://www.epey.com/akilli-telefonlar/oneplus-5t.html',
//	3328=>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-4x.html',
//	3319=>'https://www.epey.com/akilli-telefonlar/lg-q6.html',
//	3321=>'https://www.epey.com/akilli-telefonlar/huawei-nova-2.html',
//	3332=>'https://www.epey.com/akilli-telefonlar/google-pixel-2-xl.html',
//	3327=>'https://www.epey.com/akilli-telefonlar/huawei-mate-10.html',


//	3299=>'https://www.epey.com/akilli-saat/huawei-honor-band-3.html',
//	3315=>'https://www.epey.com/akilli-saat/xiaomi-amazfit-cor.html',
//	3314=>'https://www.epey.com/akilli-saat/xiaomi-amazfit-bip.html',
//	3313=>'https://www.epey.com/akilli-saat/amazfit-pace.html',
//	3310=>'https://www.epey.com/akilli-saat/samsung-gear-s3-classic.html',
//	3309=>'https://www.epey.com/akilli-saat/fitbit-ionic.html',
//	3308=>'https://www.epey.com/akilli-saat/huawei-watch.html',
//	3307=>'https://www.epey.com/akilli-saat/huawei-watch-2.html',
//	3303=>'https://www.epey.com/akilli-saat/amazfit-arc.html',
//	3300=>'https://www.epey.com/akilli-saat/fitbit-charge-2.html',
//	3337=>'https://www.epey.com/akilli-saat/xiaomi-mi-band-3.html',
//	3298=>'https://www.epey.com/akilli-saat/xiaomi-mi-band-2.html',
//	3297=>'https://www.epey.com/akilli-saat/xiaomi-mi-band-1s.html',
];

$webpages = WebPage::find()
	->filterWhere([
		'source'=>'epey',
		'format'=>'json',
//		'url'=>$targets
	])
	->limit(1000)
	->all();

$data = [];
foreach ($webpages as $webpage) {
	$row = JSON::decode($webpage->desc);
//	if($webpage->source==='epey'){
//		if(!isset($row['rt6iqm1'])||$row['rt6iqm1']!==1) continue;
//	}

	if(!isset($row['33fksng'])) continue;
	$title = trim($row['w81a9u0']).' '.$row['33fksng'];

	if($webpage->source === 'epey') { // todo exadd -
        if(!preg_match('#\(2\d{3}\)#ui', $row['33fksng'])) {
           $row['33fksng'] = preg_replace('#\(.+\)#ui','', $row['33fksng']);
           $row['ywkph223'] =  $row['w81a9u0'] .' '. $row['33fksng'];
        }
	}


	$row['url'] = '';
	$row['drbmx1r'] =[];

	//Glue data fromm Epey and GSMAArena
	if(!isset($data[$title])){
			$data[$title] = $row;
		}else{
			foreach ($row as $key=>$option) {
				if (!isset($data[$title][$key])) {
					$data[$title][$key] = $option;
				} else {
					if (strlen(JSON::encode($option)) > strlen(JSON::encode($data[$title][$key]))) {
						$data[$title][$key] = $option;
					}
				}
			}
		}

	//Save sources in [drbmx1r] column
	$data[$title]['drbmx1r'][] = H::span($title.' - '.$webpage->url,'clear-left');


/*	// Save to Database
	if($row['ywkph222']!=='Pop 2 (5)') continue;
	foreach ($row['3n68sce'] as $currency=>$amount){
		$row['3n68sce'] = Currency::convert($amount,$currency);
	}

	if(($id = array_search($webpage->url,$targets)) !== false){
		$product = Product::findOne($id);
	}
	$product->options = $row;
	$product->trySave();*/

}

// ----------
/*
// logic for related: gsmarena & epey // todo exadd -
$summaryGsmarena = [];
$summaryEpey = [];
foreach($data as $title => $item) { // $item['rt6iqm1']
    $epeyArr=[];$gsmArr=[];
	if(strpos(implode('', $item['drbmx1r']), 'epey.com')) {
	    if(isset($item['rt6iqm1']) && ($item['rt6iqm1'] == 1)) {
			$epeyArr[$item['ywkph222']]['family_epey'] = $item['t40i1m6'] ?? null;
			$epeyArr[$item['ywkph222']]['main_product_epey'] = $item['g55z4fn'];
			$epeyArr[$item['ywkph222']]['is_main_prod_epey'] = $item['rt6iqm1'];
			$epeyArr[$item['ywkph222']]['name_epey'] = $item['ywkph223'] ?? trim($item['w81a9u0'].' '.$item['33fksng']);
			$epeyArr[$item['ywkph222']]['height_epey'] = $item['qorav98'] ?? null;
			$epeyArr[$item['ywkph222']]['width_epey'] = $item['65ihv16'] ?? null;
			$epeyArr[$item['ywkph222']]['depth_epey'] = $item['vbryix7'] ?? null;
			$epeyArr[$item['ywkph222']]['weight_epey'] = $item['uanzwi8'] ?? null;
			$summaryEpey[] = $epeyArr;
        }
	} else {
		$gsmArr[$item['ywkph222']]['name_gsmarena'] = $item['ywkph223'] ?? trim($item['w81a9u0'].' '.$item['33fksng']);
	    $gsmArr[$item['ywkph222']]['height_gsmarena'] = $item['qorav98'] ?? null;
	    $gsmArr[$item['ywkph222']]['width_gsmarena'] = $item['65ihv16'] ?? null;
	    $gsmArr[$item['ywkph222']]['depth_gsmarena'] = $item['vbryix7'] ?? null;
	    $gsmArr[$item['ywkph222']]['weight_gsmarena'] = $item['uanzwi8'] ?? null;
		$summaryGsmarena[] = $gsmArr;
	};
}
foreach($summaryGsmarena as $k => $value) {
	if($epeyInfo = isGsmarenaExistInEpey($value, $summaryEpey)) {
		$summaryGsmarena[$k] = array_merge($value,['relation_to_epey' => $epeyInfo]);
    }
}
function isGsmarenaExistInEpey($value, $epeyArr)
{
	foreach($epeyArr as $k => $item) {
        $gsmarena_name = trim(key($value));
        $epey_name = trim(key($item));

		$compare = levenshtein($gsmarena_name, $epey_name);
		if($compare <= 6) {
			// next logic for filter needed item: get summary score for all params
			$score = 0;
			$score += (round($value[$gsmarena_name]['height_gsmarena'])) == (round($item[$epey_name]['height_epey'])) ? 1 : 0;
			$score += (round($value[$gsmarena_name]['width_gsmarena'])) == (round($item[$epey_name]['width_epey'])) ? 1 : 0;
			$score += (round($value[$gsmarena_name]['depth_gsmarena'])) == (round($item[$epey_name]['depth_epey'])) ? 1 : 0;
			$score += (round($value[$gsmarena_name]['weight_gsmarena'])) == (round($item[$epey_name]['weight_epey'])) ? 1 : 0;
			if($score >= 3) {
				return $item;
			}
		} else if($compare > 6 && $compare < 20) {
			if((strpos($gsmarena_name, $item[$epey_name]['name_epey']) !== false) || (strpos($item[$epey_name]['name_epey'], $gsmarena_name) !== false)) {
				$score = 0;
				$score += (round($value[$gsmarena_name]['height_gsmarena'])) == (round($item[$epey_name]['height_epey'])) ? 1 : 0;
				$score += (round($value[$gsmarena_name]['width_gsmarena'])) == (round($item[$epey_name]['width_epey'])) ? 1 : 0;
				$score += (round($value[$gsmarena_name]['depth_gsmarena'])) == (round($item[$epey_name]['depth_epey'])) ? 1 : 0;
				$score += (round($value[$gsmarena_name]['weight_gsmarena'])) == (round($item[$epey_name]['weight_epey'])) ? 1 : 0;
				if($score >= 3) {
					return $item;
				}
			}
		}
	}
}
H::print_r($summaryGsmarena);
echo '<hr><br>';
H::print_r($summaryEpey);
exit('[exit]');
*/
// -------

//$specs = Sheet::rf('@config/product/specs.csv', ['indexFrom' => 'code']);
$specs = Sheet::rf('@backend/views/shop/test/specs.csv', ['indexFrom' => 'code']); // todo: changed
H::print_r($data);
?>

<?=Render::render($data,$specs) ?>
<style>
    .clear-left {
        display: inline-block;
        float: left;
        clear: left;
    }
</style>
