<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;

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
            [['name', 'start_date', 'end_date', 'submission_deadline', 'evaluation_deadline'], 'required'],
            [['description', 'status'], 'string'],
            [['start_date', 'end_date', 'submission_deadline', 'evaluation_deadline'], 'safe'],
            [['name'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['end_date', 'submission_deadline', 'evaluation_deadline'], 'validateDates'],
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
     * Comportamentos automáticos do Modelo
     */
    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
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
        return $this->hasMany(Session::class, ['event_id' => 'id'])
                    ->orderBy(['start_time' => SORT_ASC]);
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
     * Lista de Organizadores Extra (Equipe)
     */
    public function getOrganizers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('organizer_event', ['event_id' => 'id']);
    }

    /**
     * Lista de Participantes Inscritos
     */
    public function getParticipants()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])
            ->viaTable('registration', ['event_id' => 'id']);
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

    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }


    /**
     * Checks whether a user is a evaluator for this event.
     * @param int|null $userId
     * @return bool
     */
    public function isEvaluator($userId)
    {
        if (!$userId) {
            return false;
        }

        return EventEvaluators::find()
        ->where(['event_id' => $this->id, 'user_id' => $userId])
            ->exists();
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

    /**
     * Valida toda a cronologia do evento
     */
    public function validateDates($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $start = strtotime($this->start_date);
        $end = strtotime($this->end_date);

        // Só converte se existirem (para evitar erros em campos vazios)
        $subDeadline = $this->submission_deadline ? strtotime($this->submission_deadline) : null;
        $evalDeadline = $this->evaluation_deadline ? strtotime($this->evaluation_deadline) : null;

        // Verificamos qual campo estamos a validar agora ($attribute)
        // e aplicamos a regra específica para ele.

        switch ($attribute) {

            case 'end_date':
                if ($end < $start) {
                    $this->addError($attribute, 'End date must be after start date');
                }
                break;

            case 'submission_deadline':
                if ($subDeadline > $start) {
                    $this->addError($attribute, 'A submissão deve fechar antes do evento começar.');
                }
                break;

            case 'evaluation_deadline':
                if ($subDeadline && $evalDeadline < $subDeadline) {
                    $this->addError($attribute, 'The Evaluation Deadline must be after the Submission Deadline.');
                }
                if ($evalDeadline > $start) {
                    $this->addError($attribute, 'The Evaluation must be completed before the start of the Event.');
                }
                break;
        }
    }




}
