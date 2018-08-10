<?

use app\helpers\H;
use app\helpers\WebPage;
use yii\helpers\Json;

function isEnglish($value,$code){
	if(is_string($value)){
		if(preg_match('/[^A-Za-z0-9\(\)\s\/\+\-\.\:\_\-\"\'\,\{\}\@\&\;\^\>\<\*\!]/', $value)){
			H::br([$code, $value]);
		}
	}
	if(is_array($value)){
		foreach ($value as $k=>$v){
			isEnglish($v,$code);
		}
	}
}

$webpages = WebPage::findAll(['format'=>'json']);
$data = [];
foreach ($webpages as $webpage){
	$row = JSON::decode($webpage->desc);
	unset($row['url']);
	foreach ($row as $code=>$value){
		isEnglish($value,H::a($code,$webpage->url));
	}
}