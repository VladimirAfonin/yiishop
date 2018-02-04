<?php
use yii\helpers\{Html, Url};
use yii\widgets\LinkPager;
?>
<div class="row">
    <div class="col-md-2 col-sm-6 hidden-xs">
        <div class="btn-group btn-group-sm">
            <button type="button" id="list-view" class="btn btn-default" data-toggle="tooltip" title="List"><i class="fa fa-th-list"></i></button>
            <button type="button" id="grid-view" class="btn btn-default" data-toggle="tooltip" title="Grid"><i class="fa fa-th"></i></button>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="form-group">
            <a href="/index.php?route=product/compare" id="compare-total" class="btn btn-link">Product Compare (0)</a>
        </div>
    </div>
    <div class="col-md-4 col-xs-6">
        <div class="form-group input-group input-group-sm">
            <label class="input-group-addon" for="input-sort">Sort By:</label>
            <select id="input-sort" class="form-control" onchange="location = this.value;">
                <?php
                $values = [
                    '' => 'Default',
                    'name' => 'Name(a-z)',
                    '-name' => 'Name(z-a)',
                    'price' => 'Price(low &gt; high)',
                    '-price' => 'Price(high &gt; low)',
                    '-rating' => 'rating(highest)',
                    'rating' => 'rating(lowest)'
                ];
                $current = Yii::$app->request->get('sort');
                ?>
                <?php foreach($values as $k => $value): ?>
                    <option value="<?= Html::encode(Url::current(['sort' => $k ?: null])) ?>" <?php if($current == $k) echo ' selected="selected"'?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="col-md-3 col-xs-6">
        <div class="form-group input-group input-group-sm">
            <label class="input-group-addon" for="input-limit">Show:</label>
            <select id="input-limit" class="form-control" onchange="location = this.value;">
                <?php
                $values = [15, 25, 50, 75, 100];
                $current = $dataProvider->getPagination()->getPageSize();
                ?>
                <?php foreach($values as $value): ?>
                    <option value="<?= Html::encode(Url::current(['per-page' => $value])) ?>" <?php if($current == $value) echo ' selected="selected"'?>><?= $value ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <?php foreach($dataProvider->getModels() as $product): ?>
        <?= $this->render('_product', ['product' => $product]) ?>
    <?php endforeach; ?>
</div>
<div class="row">
    <div class="col-sm-6 text-left">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->getPagination()
        ]) ?>
    </div>
    <div class="col-sm-6 text-right">Showing <?= $dataProvider->getCount() ?>  of <?= $dataProvider->getTotalCount() ?></div>
</div>
