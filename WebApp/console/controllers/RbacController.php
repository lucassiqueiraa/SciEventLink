<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    /**
     * Inicializa a hierarquia de RBAC (Roles e Permissions).
     * Comando: php yii rbac/init
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        // =========================================================
        // 1. PERMISSÕES
        // =========================================================

        // Permissão para entrar no Backend (Painel Administrativo)
        $loginToBackend = $auth->createPermission('loginToBackend');
        $loginToBackend->description = 'Pode fazer login no Backoffice';
        $auth->add($loginToBackend);

        // outras permissões

        // =========================================================
        // 2. ROLES
        // =========================================================

        // --- PARTICIPANTE
        // Só tem acesso ao Frontend. Não tem permissões especiais ainda.
        $participant = $auth->createRole('participant');
        $participant->description = 'Participante (Frontend)';
        $auth->add($participant);

        // --- ORGANIZADOR ---
        $organizer = $auth->createRole('organizer');
        $organizer->description = 'Organizador de Eventos';
        $auth->add($organizer);

        // Herança: O Organizador também é um Participante (pode inscrever-se noutros eventos)
        $auth->addChild($organizer, $participant);

        // Permissões Específicas:
        $auth->addChild($organizer, $loginToBackend); // Ele pode entrar no Backend

        // --- ADMIN ---
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrador do Sistema';
        $auth->add($admin);

        // Herança: O Admin herda tudo do Organizador (e por consequência do Participante)
        $auth->addChild($admin, $organizer);

        // =========================================================
        // 3. ATRIBUIÇÃO INICIAL (Opcional, para testes)
        // =========================================================

        // atribuir o Admin ao Utilizador com ID 1

        $auth->assign($admin, 1);
        echo "Atribuído papel de Admin ao user ID 1.\n";

        echo "Sucesso! Hierarquia RBAC criada: Admin > Organizer > Participant.\n";
    }
}