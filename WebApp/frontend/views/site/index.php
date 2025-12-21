<?php

use common\models\EventSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var EventSearch $searchModel */

$this->title = 'SciEventLink - Próximos Eventos';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Próximos Eventos Científicos</h1>
        <p class="lead">Descubra, participe e expanda o seu conhecimento.</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 offset-md-3">
            <form method="get" action="<?= Url::to(['site/index']) ?>">
                <div class="input-group">

                    <input type="text"
                           name="EventSearch[name]"
                           class="form-control"
                           placeholder="Pesquisar eventos..."
                           value="<?= Html::encode($searchModel->name)?>">

                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="body-content">
        <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'itemView' => '_event_item',
                'layout' => "{summary}\n<div class='row'>{items}</div>\n<div class='d-flex justify-content-center'>{pager}</div>",


                'itemOptions' => [
                        'class' => 'col-lg-4 col-md-6 mb-4'
                ],
        ]) ?>
    </div>
</div>