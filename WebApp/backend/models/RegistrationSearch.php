<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Registration;
use Yii;

class RegistrationSearch extends Registration
{
    public $user_email;
    public $event_name;

    public function rules()
    {
        return [
            [['id', 'user_id', 'event_id', 'ticket_type_id'], 'integer'],
            [['registration_date', 'payment_status', 'user_email', 'event_name'], 'safe'],
        ];
    }

    public function search($params)
    {
        // Join com Evento (para filtrar por organizador) e User (para mostrar nome)
        $query = Registration::find()
            ->joinWith(['event', 'user']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['registration_date' => SORT_DESC]],
        ]);

        // Configura ordenação nas colunas relacionadas
        $dataProvider->sort->attributes['event_name'] = [
            'asc' => ['event.name' => SORT_ASC],
            'desc' => ['event.name' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['user_email'] = [
            'asc' => ['user.email' => SORT_ASC],
            'desc' => ['user.email' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Se NÃO for Admin (assumimos que é Organizador),
        // filtra apenas os eventos criados por ele.
        if (!Yii::$app->user->can('admin')) {
            $query->andWhere(['event.created_by' => Yii::$app->user->id]);
        }
        // -------------------------------------

        // Filtros da Grid
        $query->andFilterWhere([
            'registration.id' => $this->id,
            'payment_status' => $this->payment_status,
        ]);

        $query->andFilterWhere(['like', 'event.name', $this->event_name])
            ->andFilterWhere(['like', 'user.email', $this->user_email]);

        return $dataProvider;
    }
}