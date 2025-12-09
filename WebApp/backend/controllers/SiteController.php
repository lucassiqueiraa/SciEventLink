<?php

namespace backend\controllers;

use common\models\Event;
use common\models\LoginForm;
use common\models\Session;
use common\models\User;
use common\models\UserProfile;
use common\models\Venue;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // TODO: Na próxima sprint implementar RBAC: Yii::$app->user->can('admin')
        $currentUser = Yii::$app->user->identity;
        $isAdmin = ($currentUser && $currentUser->username === 'admin');
        $userId = Yii::$app->user->id;

        if ($isAdmin) {
            $totalParticipants = User::find()->count();
            $totalOrganizers = User::find()->where(['!=', 'username', 'admin'])->count(); // Exemplo
            $totalEvents = Event::find()->count();
            $suspendedUsers = User::find()->where(['status' => 0])->count();

            return $this->render('index', [
                'isAdmin' => true,
                'totalParticipants' => $totalParticipants,
                'totalOrganizers' => $totalOrganizers,
                'totalEvents' => $totalEvents,
                'suspendedUsers' => $suspendedUsers,
                // Variáveis do organizador vão nulas para não dar erro na view
                'myEvents' => 0, 'mySessions' => 0, 'myVenues' => 0
            ]);
        }

        else {
            $myEvents = Event::find()->where(['created_by' => $userId])->count();

            $mySessions = Session::find()
                ->joinWith('event')
                ->where(['event.created_by' => $userId])
                ->count();

            $myVenues = Venue::find()
                ->joinWith('event')
                ->where(['event.created_by' => $userId])
                ->count();

            return $this->render('index', [
                'isAdmin' => false,
                'myEvents' => $myEvents,
                'mySessions' => $mySessions,
                'myVenues' => $myVenues,
                // Variáveis do admin vão nulas
                'totalParticipants' => 0, 'totalOrganizers' => 0, 'totalEvents' => 0, 'suspendedUsers' => 0
            ]);
        }
    }

    /**
     * Login action.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
