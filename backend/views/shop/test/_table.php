<h3 class="text-center">Таблица конфликтов</h3>
<br>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Название вуза</th>
        <th>Ссылка на wikipedia</th>
        <th>Сайт вуза в бд</th>
        <th>Сайт вуза в wiki</th>
        <th>Сайт вуза в google kp</th>
        <th>Конфликт</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?= $nameUni ?></td>
<td><a href="<?= $linkToWiki ?>"><?= $nameUni?></a></td>
<td><h3><span class="label label-default"><?= $from_wiki ?? null ?></span></h3></td>
<td><h3><span class="label label-default"><?= $from_db ?? null ?></span></h3></td>
<td><h3><span class="label label-default"><?= $from_parsing ?? '-' ?></span></h3></td>
<td>
    <?php if (isset($data)): ?>
        <?php if ($data > 98): ?>
            <span class="label label-success">no</span>
        <?php else: ?>
            <span class="label label-danger">yes</span>
        <?php endif; ?>
    <?php endif; ?>
</td>
</tr>
</tbody>
</table>