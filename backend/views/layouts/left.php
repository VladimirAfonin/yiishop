<?php
use backend\widgets\{CategoriesWidget, TagsWidget};
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p>Alexander Pierce</p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Тестовые данные', 'options' => ['class' => 'header']],
                    ['label' => 'Products', 'icon' => 'file-o', 'url' => ['/admin/product/index'], 'active' => Yii::$app->controller->id == 'product'],
                    ['label' => 'Test Tags', 'icon' => 'file-o', 'url' => ['/admin/test-tags/index'], 'active' => Yii::$app->controller->id == 'test-tags'],
                    ['label' => 'Category', 'icon' => 'file-o', 'url' => ['/admin/category/index'], 'active' => Yii::$app->controller->id == 'category'],
                    ['label' => 'Attribute', 'icon' => 'file-o', 'url' => ['/admin/attribute/index'], 'active' => Yii::$app->controller->id == 'attribute'],
                    ['label' => 'Attribute value', 'icon' => 'file-o', 'url' => ['/admin/attribute-value/index'], 'active' => Yii::$app->controller->id == 'attribute-value'],
                    ['label' => 'Product tag', 'icon' => 'file-o', 'url' => ['/admin/product-tag/index'], 'active' => Yii::$app->controller->id == 'attribute-value'],

                    ['label' => 'Управление', 'options' => ['class' => 'header']],
                    ['label' => 'Shop', 'icon' => 'folder', 'items' => [
                            ['label' => 'Brands', 'icon' => 'file-o', 'url' => ['/shop/brand/index'], 'active' => Yii::$app->controller->id == 'shop/brand'], // $this->context->id == 'shop/brand'($this -> View)
                            ['label' => 'Tags', 'icon' => 'file-o', 'url' => ['/shop/tag/index'], 'active' => Yii::$app->controller->id == 'shop/tag'],
                            ['label' => 'Categories', 'icon' => 'file-o', 'url' => ['/shop/category/index'], 'active' => Yii::$app->controller->id == 'shop/tag'],
                            ['label' => 'Characteristics', 'icon' => 'file-o', 'url' => ['/shop/characteristic/index'], 'active' => $this->context->id == 'shop/characteristic'],
                            ['label' => 'Products', 'icon' => 'file-o', 'url' => ['/shop/product/index'], 'active' => $this->context->id == 'shop/product'],
                        ]
                    ],
                    ['label' => 'Пользователи', 'icon' => '', 'url' => ['/user'], 'active' => Yii::$app->controller->id == 'user'],
                    ['label' => 'Главная', 'icon' => '', 'url' => ['/site'], 'active' => $this->context->id == 'site'],
                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    [
                        'label' => 'Some tools',
                        'icon' => 'share',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ) ?>

        <?php if ($this->beginCache('categories-widget', [
            'duration' => 3600,
            'variations' => [
                isset($this->params['category']) ? $this->params['category']->id : null
            ]
        ])): ?>

        <?= CategoriesWidget::widget([
                'category' => isset($this->params['category']) ? $this->params['category'] : null,
        ]) ?>

        <?php $this->endCache();
        endif; ?>

        <?= TagsWidget::widget([
                'tag' => isset($this->params['tag']) ? $this->params['tag'] : null,
        ]) ?>

    </section>

</aside>
