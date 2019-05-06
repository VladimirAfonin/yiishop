<?

use app\helpers\H;
use app\helpers\Sheet;
use app\helpers\WebPage;
use app\nee\models\Media;
use app\nee\models\Product;
use yii\db\Expression;
use yii\helpers\Json;

$targets = [
	3334=>'https://www.epey.com/akilli-telefonlar/apple-iphone-7.html',
	3333=>'https://www.epey.com/akilli-telefonlar/apple-iphone-8.html',
	3320=>'https://www.epey.com/akilli-telefonlar/apple-iphone-x.html',
	3322=>'https://www.epey.com/akilli-telefonlar/samsung-galaxy-s9.html',
	3339=>'https://www.epey.com/akilli-telefonlar/meizu-m6.html',
	3325=>'https://www.epey.com/akilli-telefonlar/meizu-m6-note.html',
	3330=>'https://www.epey.com/akilli-telefonlar/razer-phone.html',
	3329=>'https://www.epey.com/akilli-telefonlar/sony-xperia-xa1.html',
	3317=>'https://www.epey.com/akilli-telefonlar/oneplus-5t.html',
	3318=>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-5a.html',
	3328=>'https://www.epey.com/akilli-telefonlar/xiaomi-redmi-4x.html',
	3319=>'https://www.epey.com/akilli-telefonlar/lg-q6.html',
	3321=>'https://www.epey.com/akilli-telefonlar/huawei-nova-2.html',
	3332=>'https://www.epey.com/akilli-telefonlar/google-pixel-2-xl.html',
	3327=>'https://www.epey.com/akilli-telefonlar/huawei-mate-10.html',


	3299=>'https://www.epey.com/akilli-saat/huawei-honor-band-3.html',
	3315=>'https://www.epey.com/akilli-saat/xiaomi-amazfit-cor.html',
	3314=>'https://www.epey.com/akilli-saat/xiaomi-amazfit-bip.html',
	3313=>'https://www.epey.com/akilli-saat/amazfit-pace.html',
	3310=>'https://www.epey.com/akilli-saat/samsung-gear-s3-classic.html',
	3309=>'https://www.epey.com/akilli-saat/fitbit-ionic.html',
	3308=>'https://www.epey.com/akilli-saat/huawei-watch.html',
	3307=>'https://www.epey.com/akilli-saat/huawei-watch-2.html',
	3303=>'https://www.epey.com/akilli-saat/amazfit-arc.html',
	3300=>'https://www.epey.com/akilli-saat/fitbit-charge-2.html',
	3337=>'https://www.epey.com/akilli-saat/xiaomi-mi-band-3.html',
	3298=>'https://www.epey.com/akilli-saat/xiaomi-mi-band-2.html',
	3297=>'https://www.epey.com/akilli-saat/xiaomi-mi-band-1s.html',
];

$webpages = WebPage::find()->filterWhere([
//	'source'=>'epey',
	'format'=>'json',
//	'url'=>$targets
])
	//->andWhere(['like','desc','One A9'])
//	->limit(200)
	->all();
foreach ($webpages as $webpage){
	$row = JSON::decode($webpage->desc);

	if(($id = array_search($webpage->url,$targets)) !== false){
		$product = Product::findOne($id);
	}else{
		continue;
	}


	$row['photos'] = $row['photos']??[];
//	$row['photos'] = array_reverse($row['photos']);
	foreach($row['photos'] as $i=>$photo){
//		$priority = 6-$i;
		$media = Media::findOne(['source'=>$photo]);
		if($media) continue;
		H::br([H::a($product->name,$product->url),$webpage->url,H::a($photo,$photo)]);
		$media = new Media([
			'scenario' => Media::SCENARIO_ACTION_ADD,
			'name'=>$product->name,
			'path'=>$product->slug.'__'.Yii::$app->security->generateRandomString(4),
			'source_url'=>$photo,
			'product_id' => $id,
			'type'=>110,
			'priority' => 0,
			'discovery' => 4,
			'created_at' => new Expression('NOW()'),
			'mark' => 4,
		]);
		$media->pathGenerated = true;
		$media->trySave();;
	}
}
?>

