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
    </tr>
    </thead>
    <tbody>
    <?php if (isset($models)): ?>
        <?php foreach ($models as $m): ?>
            <tr>
                <td><?= $m->id ?></td>
                <td><a href="<?= $m->link_wiki ?>"><?= $m->name ?></a></td>
                <td><h3><span class="label label-default"><?= $m->wiki_website ?></span></h3></td>
                <td><h3><span class="label label-default"><?= $m->google_website ?></span></h3></td>
                <td><h3><span class="label label-default"><?= $m->db_website ?></span></h3></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>items not found!</p>
    <?php endif; ?>
    </tbody>
</table>