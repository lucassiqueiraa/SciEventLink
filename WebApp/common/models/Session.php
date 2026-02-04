<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "session".
 *
 * @property int $id
 * @property int $event_id
 * @property int|null $venue_id
 * @property string $title
 * @property string|null $start_time
 * @property string|null $end_time
 *
 * @property Event $event
 * @property SessionFeedback[] $sessionFeedbacks
 * @property SessionQuestion[] $sessionQuestions
 * @property UserSessionFavorite[] $userSessionFavorites
 * @property User[] $users
 * @property User[] $users0
 * @property Venue $venue
 */
class Session extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['venue_id', 'start_time', 'end_time'], 'default', 'value' => null],
            [['event_id', 'title', 'venue_id', 'start_time', 'end_time'], 'required'],
            [['event_id', 'venue_id'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['venue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Venue::class, 'targetAttribute' => ['venue_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event',
            'venue_id' => 'Venue',
            'title' => 'Session Title',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            ['end_time', 'compare', 'compareAttribute' => 'start_time', 'operator' => '>', 'message' => 'O fim deve ser depois do início.'],
            ['venue_id', 'validateVenueAvailability'],
        ];
    }

    /**
     * Gets query for [[Event]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    /**
     * Gets query for [[SessionFeedbacks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessionFeedbacks()
    {
        return $this->hasMany(SessionFeedback::class, ['session_id' => 'id']);
    }

    /**
     * Gets query for [[SessionQuestions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessionQuestions()
    {
        return $this->hasMany(SessionQuestion::class, ['session_id' => 'id']);
    }

    /**
     * Gets query for [[UserSessionFavorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserSessionFavorites()
    {
        return $this->hasMany(UserSessionFavorite::class, ['session_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('session_feedback', ['session_id' => 'id']);
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_session_favorite', ['session_id' => 'id']);
    }

    /**
     * Gets query for [[Venue]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVenue()
    {
        return $this->hasOne(Venue::class, ['id' => 'venue_id']);
    }

    /**
     * Verifica se a sala já está ocupada nesse horário
     */
    public function validateVenueAvailability($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }
        $conflito = Session::find()
            ->where(['venue_id' => $this->venue_id])
            ->andWhere(['<>', 'id', $this->id])
            ->andWhere(['<', 'start_time', $this->end_time])
            ->andWhere(['>', 'end_time', $this->start_time])
            ->exists();

        if ($conflito) {
            $this->addError($attribute, 'Esta sala já está ocupada neste horário!');
        }
    }

    /**
     * Limpa e formata as datas para o MySQL antes de salvar
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if (!empty($this->start_time)) {
            $timestamp = strtotime($this->start_time);

            $this->start_time = date('Y-m-d H:i:s', $timestamp);
        }

        if (!empty($this->end_time)) {
            $timestamp = strtotime($this->end_time);
            $this->end_time = date('Y-m-d H:i:s', $timestamp);
        }

        return true;
    }
}
