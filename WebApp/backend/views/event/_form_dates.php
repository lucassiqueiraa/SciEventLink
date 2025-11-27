<div class="pt-3">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">Duração do Evento</div>
                <div class="card-body">
                    <?= $form->field($model, 'start_date')->textInput(['type' => 'date']) ?>
                    <?= $form->field($model, 'end_date')->textInput(['type' => 'date']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-warning text-dark">Prazos Científicos</div>
                <div class="card-body">
                    <?= $form->field($model, 'submission_deadline')->textInput(['type' => 'date']) ?>
                    <?= $form->field($model, 'evaluation_deadline')->textInput(['type' => 'date']) ?>
                </div>
            </div>
        </div>
    </div>
</div>