<?php

namespace frontend\controllers;

use Yii;
use common\models\Article;
use common\models\Registration;
use common\models\Event;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;

class ArticleController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Displays the form for submitting an article to a specific event.
     */
    public function actionCreate($event_id)
    {
        $registration = Registration::find()
            ->where(['event_id' => $event_id, 'user_id' => Yii::$app->user->id])
            ->one();

        if (!$registration || ($registration->payment_status !== 'paid' && $registration->payment_status !== 'confirmed')) {
            Yii::$app->session->setFlash('error', 'Inscrição inválida ou pagamento pendente.');
            return $this->redirect(['event/view', 'id' => $event_id]);
        }

        $existingArticle = Article::findOne(['registration_id' => $registration->id]);
        if ($existingArticle) {
            Yii::$app->session->setFlash('info', 'Já submeteste um artigo para este evento. Podes editá-lo aqui.');
            return $this->redirect(['update', 'id' => $existingArticle->id]);
        }

        $model = new Article();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {

                $model->registration_id = $registration->id;
                $model->status = 'submitted';

                $model->articleFile = UploadedFile::getInstance($model, 'articleFile');

                if ($model->validate()) {

                    if ($model->articleFile) {
                        $fileName = 'article_' . $registration->id . '_' . time() . '.' . $model->articleFile->extension;
                        $folderPath = Yii::getAlias('@frontend/web/uploads/articles/');

                        if (!file_exists($folderPath)) {
                            mkdir($folderPath, 0777, true);
                        }

                        $fullPath = $folderPath . $fileName;

                        if ($model->articleFile->saveAs($fullPath)) {

                            $model->file_path = 'uploads/articles/' . $fileName;

                            if ($model->save(false)) {
                                Yii::$app->session->setFlash('success', 'Artigo submetido com sucesso!');
                                return $this->redirect(['event/view', 'id' => $event_id]);
                            }
                        }
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Erro na validação: ' . print_r($model->errors, true));
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'event_id' => $event_id,
        ]);
    }


    /**
     * Action to Edit/Replace Article
     */
    public function actionUpdate($id)
    {
        $model = Article::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Artigo não encontrado.');
        }

        if ($model->registration->user_id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('Não tens permissão para editar este artigo.');
        }

        $oldFilePath = $model->file_path;

        if ($this->request->isPost && $model->load($this->request->post())) {

            $model->articleFile = UploadedFile::getInstance($model, 'articleFile');

            if ($model->articleFile) {
                if ($model->validate()) {
                    $fileName = 'article_' . $model->registration_id . '_' . time() . '.' . $model->articleFile->extension;
                    $folderPath = Yii::getAlias('@frontend/web/uploads/articles/');

                    if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);

                    if ($model->articleFile->saveAs($folderPath . $fileName)) {
                        $model->file_path = 'uploads/articles/' . $fileName;

                        if ($oldFilePath && $oldFilePath !== $model->file_path) {

                            $absoluteOldPath = Yii::getAlias('@frontend/web/') . $oldFilePath;

                            if (file_exists($absoluteOldPath)) {
                                unlink($absoluteOldPath); // <--- ESTE É O COMANDO QUE APAGA DO DISCO
                            }
                        }
                    }
                }
            } else {
                $model->file_path = $oldFilePath;
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Artigo atualizado com sucesso.');
                return $this->redirect(['event/view', 'id' => $model->registration->event_id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'event_id' => $model->registration->event_id,
        ]);
    }

}