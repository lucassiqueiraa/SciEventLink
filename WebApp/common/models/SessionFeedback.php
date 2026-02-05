<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "session_feedback".
 *
 * @property int $id
 * @property int $session_id
 * @property int $user_id
 * @property int $rating
 * @property string|null $comment
 * @property string|null $created_at
 *
 * @property Session $session
 * @property User $user
 */
class SessionFeedback extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session_feedback';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['comment'], 'default', 'value' => null],
            [['session_id', 'user_id', 'rating'], 'required'],
            [['session_id', 'user_id', 'rating'], 'integer'],
            [['comment'], 'string'],
            ['rating', 'in', 'range' => [1, 2, 3, 4, 5], 'message' => 'A nota deve ser entre 1 e 5.'],
            [['created_at'], 'safe'],
            [['session_id', 'user_id'], 'unique', 'targetAttribute' => ['session_id', 'user_id']],
            [['session_id'], 'exist', 'skipOnError' => true, 'targetClass' => Session::class, 'targetAttribute' => ['session_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'session_id' => 'Session ID',
            'user_id' => 'User ID',
            'rating' => 'Rating',
            'comment' => 'Comment',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Session]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSession()
    {
        return $this->hasOne(Session::class, ['id' => 'session_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
