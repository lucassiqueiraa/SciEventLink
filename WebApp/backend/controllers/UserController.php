<?php

namespace backend\controllers;

use backend\models\OrganizerSignupForm;
use common\models\User;
use common\models\UserProfile;
use backend\models\UserProfileSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for UserProfile model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => \yii\filters\AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'roles' => ['admin'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all UserProfile models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserProfileSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserProfile model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User + UserProfile + RBAC Role.
     */
    public function actionCreate()
    {
        $user = new User();
        $profile = new UserProfile();

        // Se o formulário enviou dados para AMBOS os modelos
        if ($this->request->isPost) {
            if ($user->load($this->request->post()) && $profile->load($this->request->post())) {

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $user->setPassword($user->password_hash);
                    $user->generateAuthKey();
                    $user->generateEmailVerificationToken();
                    $user->status = 10; // Ativo

                    if (!$user->save()) {
                        throw new \Exception("Erro user: " . print_r($user->errors, true));
                    }

                    $profile->user_id = $user->id;

                    // Traduz o papel escolhido no dropdown para o código do banco
                    switch ($user->role) {
                        case 'admin':
                            $profile->role = 'ADM';
                            break;
                        case 'organizer':
                            $profile->role = 'ORG';
                            break;
                        default:
                            $profile->role = 'PART'; // Participante por padrão
                    }

                    if (!$profile->save()) {
                        throw new \Exception("Erro profile: " . print_r($profile->errors, true));
                    }

                    if (!empty($user->role)) {
                        $auth = Yii::$app->authManager;
                        $role = $auth->getRole($user->role);
                        $auth->assign($role, $user->id);
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $user->id]);

                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        } else {
            $user->loadDefaultValues();
        }

        return $this->render('create', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Updates an existing User + Profile + RBAC.
     */
    public function actionUpdate($id)
    {
        $user = $this->findModel($id);

        // 1. Busca o perfil de forma direta e segura
        $profile = UserProfile::findOne(['user_id' => $id]);

        // Se não existir, cria um novo e FORÇA O ID IMEDIATAMENTE
        if (!$profile) {
            $profile = new UserProfile();
            $profile->user_id = $user->id;
        }

        // 2. Prepara o formulário (Role e Senha)
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($id);
        if ($roles) {
            $user->role = array_key_first($roles);
        }

        $oldPasswordHash = $user->password_hash;
        $user->password_hash = '';

        // 3. Processamento do Save
        if ($this->request->isPost) {

            // Carrega os dados
            $user->load($this->request->post());
            $profile->load($this->request->post());

            $transaction = Yii::$app->db->beginTransaction();
            try {
                // Senha
                if (!empty($user->password_hash)) {
                    $user->setPassword($user->password_hash);
                } else {
                    $user->password_hash = $oldPasswordHash;
                }

                // --- DEBUG DE VALIDAÇÃO DO USER ---
                if (!$user->validate()) {
                    throw new \Exception("Erro validação User: " . print_r($user->errors, true));
                }
                if (!$user->save(false)) { // false porque já validamos acima
                    throw new \Exception("Erro ao salvar User no banco.");
                }

                // Sincroniza Role
                switch ($user->role) {
                    case 'admin': $profile->role = 'ADM'; break;
                    case 'organizer': $profile->role = 'ORG'; break;
                    case 'participant': $profile->role = 'PART'; break;
                }

                // --- GARANTIA DE LIGAÇÃO ---
                $profile->user_id = $user->id; // Redundância de segurança

                // --- DEBUG DE VALIDAÇÃO DO PROFILE ---
                if (!$profile->validate()) {
                    throw new \Exception("Erro validação Profile: " . print_r($profile->errors, true));
                }
                if (!$profile->save(false)) {
                    throw new \Exception("Erro ao salvar Profile no banco.");
                }

                // Atualiza RBAC
                $auth->revokeAll($id);
                if (!empty($user->role)) {
                    $newRole = $auth->getRole($user->role);
                    $auth->assign($newRole, $id);
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Atualizado com sucesso!');
                return $this->redirect(['view', 'id' => $user->id]);

            } catch (\Exception $e) {
                $transaction->rollBack();
                // Mostra o erro na tela para pararmos de adivinhar
                Yii::$app->session->setFlash('error', $e->getMessage());

                // Restaura senha para não bugar o form
                $user->password_hash = '';
            }
        }

        return $this->render('update', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Deletes an existing UserProfile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $profile = $this->findModel($id);

        $user = $profile->user;

        if ($user) {
            if ($user->toggleStatus()) {
                if ($user->status === User::STATUS_ACTIVE) {
                    Yii::$app->session->setFlash('success', 'Utilizador reativado com sucesso!');
                } else {
                    Yii::$app->session->setFlash('warning', 'Utilizador suspenso (bloqueado) com sucesso!');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Erro ao alterar o status do utilizador.');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the UserProfile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
