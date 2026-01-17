<?php

namespace backend\modules\api;

use yii\base\Module;

class ModuleAPI extends Module
{
    public $controllerNamespace = 'backend\modules\api\controllers';

    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
    }
}