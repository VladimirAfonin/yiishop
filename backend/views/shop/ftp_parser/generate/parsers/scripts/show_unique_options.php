<?
use app\helpers\Sheet;
use app\helpers\WebPage;
use yii\helpers\Json;


$webpages = WebPage::findAll(['format'=>'json']);
$data = [];
foreach ($webpages as $webpage){
	$row = JSON::decode($webpage->desc);
	if($webpage->source==='epey'){
		if(!isset($row['is_main_prod'])||$row['is_main_prod']!==1) continue;
	}

	if(!isset($row['33fksng'])) continue;
	$device = trim($row['w81a9u0']).' '.$model;
	$row['url'] = '';
	$row['drbmx1r'] =[];
	$data[$webpage->url] = $row;
}
$specs = Sheet::rf('@config/product/specs.csv', ['indexFrom' => 'code']);


// Select only unique values
$s = [];
foreach ($data as $name=>$row){
	foreach ($row as $code=>$value){
		if(is_array($value)){
			$value = '[]';
		}
		if(is_object($value)){
			$value = '{}';
		}
		$s[$code][]= (string)$value;
	}
}
$r =[];
foreach ($s as $code=>$value){
	$r[$code] = count($value);
}
arsort($r);?>
<table class="table table-bordered table-micro">
	<? foreach ($specs as $code=>$value):?>
		<tr>
			<td><?=$specs[$code]['category']??'' ?></td>
			<td><?=$specs[$code]['group']??'' ?></td>
			<td><?=$code?></td>
			<td><?=$specs[$code]['title_ru']??'' ?></td>
			<td>
				<?=number_format(100*($r[$code]??0)/count($data),2); ?>
			</td>
			<td>
				<? $count = array_count_values($s[$code]??[]) ?>
				<? arsort($count);?>
				<? foreach ($count as $k=>$v): ?>
					■<?=$k ?> <span class="muted"><?=round(100*$v/count($data),2) ?>%</span>
				<? endforeach; ?>
				<?//=implode('■',) ?>
			</td>
		</tr>
	<? endforeach;?>
</table>
