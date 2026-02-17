<?php

namespace backend\controllers;

use Yii;
use common\models\SessionQuestion;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl; // <--- Importante adicionar isto

class SessionQuestionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['approve-question', 'delete-question'],
                        'allow' => true,
                        'roles' => ['admin', 'organizer'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve-question' => ['POST'],
                    'delete-question' => ['POST'],
                ],
            ],
        ];
    }

    public function actionApproveQuestion($id)
    {
        $question = SessionQuestion::findOne($id);

        if (!$question) {
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['success' => false, 'message' => 'Not found']);
            }
            return $this->redirect(['/session/index']);
        }

        $question->status = 'approved';

        if ($question->save()) {
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['success' => true]);
            }
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->asJson(['success' => false, 'errors' => $question->getErrors()]);
            }
        }

        return $this->redirect(['/session/view', 'id' => $question->session_id]);
    }

    public function actionDeleteQuestion($id)
    {
        $question = SessionQuestion::findOne($id);

        if ($question) {
            $sessionId = $question->session_id;
            $question->delete();

            if (Yii::$app->request->isAjax) {
                return $this->asJson(['success' => true]);
            }
            return $this->redirect(['/session/view', 'id' => $sessionId]);
        }

        return $this->redirect(['/session/index']);
    }
}