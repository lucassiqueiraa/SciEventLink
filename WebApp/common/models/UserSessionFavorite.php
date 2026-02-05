<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_session_favorite".
 *
 * @property int $id
 * @property int $user_id
 * @property int $session_id
 * @property string|null $created_at
 */
class UserSessionFavorite extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_session_favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'session_id'], 'required'],
            [['user_id', 'session_id'], 'integer'],
            [['created_at'], 'safe'],
            [['user_id', 'session_id'], 'unique', 'targetAttribute' => ['user_id', 'session_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'session_id' => 'Session ID',
            'created_at' => 'Created At',
        ];
    }

    public function getSession()
    {
        return $this->hasOne(Session::class, ['id' => 'session_id']);
    }

}
