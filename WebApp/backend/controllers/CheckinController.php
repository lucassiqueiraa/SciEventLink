<?php

namespace backend\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;

class CheckinController extends Controller
{
    /**
     * Access Control: only logged-in users (Staff/Admin) can see this page
     */
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
     * Render the QR Code reading page
     * index.php?r=checkin/scan
     */
    public function actionScan()
    {
        return $this->render('scan');
    }
}