<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "venue".
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property int|null $capacity
 *
 * @property Event $event
 * @property Session[] $sessions
 */
class Venue extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'venue';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['capacity'], 'default', 'value' => null],
            [['event_id', 'name'], 'required'],
            [['event_id', 'capacity'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
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
            'name' => 'Name',
            'capacity' => 'Capacity',
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
     * Gets query for [[Sessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::class, ['venue_id' => 'id']);
    }

}
