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
            [['event_id', 'title'], 'required'],
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
            'event_id' => 'Event ID',
            'venue_id' => 'Venue ID',
            'title' => 'Title',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
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

}
