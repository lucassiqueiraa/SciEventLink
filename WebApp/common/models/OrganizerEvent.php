<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "organizer_event".
 *
 * @property int $user_id
 * @property int $event_id
 * @property string|null $role_description
 *
 * @property Event $event
 * @property User $user
 */
class OrganizerEvent extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'organizer_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_description'], 'default', 'value' => null],
            [['user_id', 'event_id'], 'required'],
            [['user_id', 'event_id'], 'integer'],
            [['role_description'], 'string', 'max' => 100],
            [['user_id', 'event_id'], 'unique', 'targetAttribute' => ['user_id', 'event_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'event_id' => 'Event ID',
            'role_description' => 'Role Description',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
