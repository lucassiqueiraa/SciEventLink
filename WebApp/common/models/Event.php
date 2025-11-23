<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $start_date
 * @property string $end_date
 * @property string|null $submission_deadline
 * @property string|null $evaluation_deadline
 * @property string $status
 *
 * @property OrganizerEvent[] $organizerEvents
 * @property Registration[] $registrations
 * @property Session[] $sessions
 * @property TicketType[] $ticketTypes
 * @property User[] $users
 * @property User[] $users0
 * @property Venue[] $venues
 */
class Event extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_RUNNING = 'running';
    const STATUS_FINISHED = 'finished';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['description', 'submission_deadline', 'evaluation_deadline'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'open'],
            [['name', 'start_date', 'end_date'], 'required'],
            [['description', 'status'], 'string'],
            [['start_date', 'end_date', 'submission_deadline', 'evaluation_deadline'], 'safe'],
            [['name'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'submission_deadline' => 'Submission Deadline',
            'evaluation_deadline' => 'Evaluation Deadline',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[OrganizerEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizerEvents()
    {
        return $this->hasMany(OrganizerEvent::class, ['event_id' => 'id']);
    }

    /**
     * Gets query for [[Registrations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrations()
    {
        return $this->hasMany(Registration::class, ['event_id' => 'id']);
    }

    /**
     * Gets query for [[Sessions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSessions()
    {
        return $this->hasMany(Session::class, ['event_id' => 'id']);
    }

    /**
     * Gets query for [[TicketTypes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketTypes()
    {
        return $this->hasMany(TicketType::class, ['event_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('organizer_event', ['event_id' => 'id']);
    }

    /**
     * Gets query for [[Users0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('registration', ['event_id' => 'id']);
    }

    /**
     * Gets query for [[Venues]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVenues()
    {
        return $this->hasMany(Venue::class, ['event_id' => 'id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_OPEN => 'open',
            self::STATUS_CLOSED => 'closed',
            self::STATUS_RUNNING => 'running',
            self::STATUS_FINISHED => 'finished',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function setStatusToOpen()
    {
        $this->status = self::STATUS_OPEN;
    }

    /**
     * @return bool
     */
    public function isStatusClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function setStatusToClosed()
    {
        $this->status = self::STATUS_CLOSED;
    }

    /**
     * @return bool
     */
    public function isStatusRunning()
    {
        return $this->status === self::STATUS_RUNNING;
    }

    public function setStatusToRunning()
    {
        $this->status = self::STATUS_RUNNING;
    }

    /**
     * @return bool
     */
    public function isStatusFinished()
    {
        return $this->status === self::STATUS_FINISHED;
    }

    public function setStatusToFinished()
    {
        $this->status = self::STATUS_FINISHED;
    }
}
