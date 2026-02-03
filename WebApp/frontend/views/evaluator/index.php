<?php
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Article[] $articles */

$this->title = 'Painel do Avaliador';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluator-index container py-4">

    <h1><i class="fas fa-gavel"></i> <?= Html::encode($this->title) ?></h1>
    <p class="text-muted">Artigos disponíveis para avaliação nos eventos onde é colaborador.</p>

    <div class="card shadow-sm mt-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Título do Artigo</th>
                        <th>Evento</th>
                        <th>Autor</th>
                        <th>Data Submissão</th>
                        <th class="text-end">Ação</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <em>Nenhum artigo pendente para avaliação.</em>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <strong><?= Html::encode($article->title) ?></strong>
                                </td>
                                <td>
                                    <?= Html::encode($article->registration->event->name) ?>
                                </td>
                                <td>
                                    <?= Html::encode($article->registration->user->username) ?>
                                </td>
                                <td>
                                    <?= Yii::$app->formatter->asDate($article->created_at ?? time()) ?>
                                </td>
                                <td class="text-end">
                                    <?= Html::a('<i class="fas fa-star-half-alt"></i> Avaliar',
                                        ['evaluate', 'id' => $article->id],
                                        ['class' => 'btn btn-sm btn-primary']
                                    ) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>