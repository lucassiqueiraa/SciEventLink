<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */
/** @var common\models\UserProfile $profile */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'user' => $user,
            'profile' => $profile,
    ]) ?>

</div>