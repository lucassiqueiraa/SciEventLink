<?php

/** @var \yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    </head>
    <body class="d-flex flex-column h-100">
    <?php $this->beginBody() ?>

    <header>
        <?php
        NavBar::begin([
                'brandLabel' => Html::img('@web/images/logoSciEventLink-noBg2.png', [
                        'alt' => Yii::$app->name,
                        'class' => 'img-fluid',
                        'style' => 'height: 50px; width: auto;', // Ajuste a altura se necessário
                ]),
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [

                        'class' => 'navbar navbar-expand-md navbar-dark bg-dark fixed-top shadow',
                'innerContainerOptions' => ['class' => 'container-fluid px-4'],
        ]]);

        $menuItems = [
                ['label' => 'Home', 'url' => ['/site/index']],
                ['label' => 'Eventos', 'url' => ['/event/index']],
                [
                        'label' => 'Inscrições / Vendas',
                    // 'icon' => 'shopping-cart',
                        'url' => ['/registration/index'],
                        'visible' => !Yii::$app->user->isGuest
                ],
        ];

        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
        } else {
            // --- ITEM RESTRITO AO ADMIN ---
            $menuItems[] = [
                    'label' => 'Gerir Utilizadores',
                    'url' => ['/user/index'],
                    'visible' => Yii::$app->user->can('admin')
            ];
        }

        echo Nav::widget([
                'options' => ['class' => 'navbar-nav me-auto mb-2 mb-md-0'],
                'items' => $menuItems,
        ]);

        if (Yii::$app->user->isGuest) {
            echo Html::tag('div',Html::a('Login',['/site/login'],['class' => ['btn btn-outline-light btn-sm text-decoration-none ms-2']]),['class' => ['d-flex']]);
        } else {
            echo Html::beginForm(['/site/logout'], 'post', ['class' => 'd-flex'])
                    . Html::submitButton(
                            'Logout (' . Yii::$app->user->identity->username . ')',
                            ['class' => 'btn btn-danger btn-sm logout text-decoration-none ms-2']
                    )
                    . Html::endForm();
        }
        NavBar::end();
        ?>
    </header>

    <main role="main" class="flex-shrink-0">
        <div class="container">
            <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </main>

    <footer class="footer mt-auto py-3 text-muted bg-light border-top">
        <div class="container">
            <p class="float-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage(); ?>