<?php

namespace common\models;

use kartik\mpdf\Pdf;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "registration".
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int $ticket_type_id
 * @property string|null $registration_date
 * @property string $payment_status
 *
 * @property Article[] $articles
 * @property Event $event
 * @property TicketType $ticketType
 * @property User $user
 */
class Registration extends ActiveRecord
{

    /**
     * ENUM field values
     */
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_CANCELLED = 'cancelled';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'registration';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_status'], 'default', 'value' => 'pending'],
            [['user_id', 'event_id', 'ticket_type_id'], 'required'],
            [['user_id', 'event_id', 'ticket_type_id'], 'integer'],
            [['registration_date'], 'safe'],
            [['payment_status'], 'string'],
            ['payment_status', 'in', 'range' => array_keys(self::optsPaymentStatus())],
            [['user_id', 'event_id'], 'unique', 'targetAttribute' => ['user_id', 'event_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['ticket_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TicketType::class, 'targetAttribute' => ['ticket_type_id' => 'id']],
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
            'event_id' => 'Event ID',
            'ticket_type_id' => 'Ticket Type ID',
            'registration_date' => 'Registration Date',
            'payment_status' => 'Payment Status',
        ];
    }

    /**
     * Gets query for [[Articles]].
     *
     * @return ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['registration_id' => 'id']);
    }

    /**
     * Gets query for [[Event]].
     *
     * @return ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'event_id']);
    }

    /**
     * Gets query for [[TicketType]].
     *
     * @return ActiveQuery
     */
    public function getTicketType()
    {
        return $this->hasOne(TicketType::class, ['id' => 'ticket_type_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * column payment_status ENUM value labels
     * @return string[]
     */
    public static function optsPaymentStatus()
    {
        return [
            self::PAYMENT_STATUS_PENDING => 'pending',
            self::PAYMENT_STATUS_PAID => 'paid',
            self::PAYMENT_STATUS_CANCELLED => 'cancelled',
        ];
    }

    /**
     * @return string
     */
    public function displayPaymentStatus()
    {
        return self::optsPaymentStatus()[$this->payment_status];
    }

    /**
     * @return bool
     */
    public function isPaymentStatusPending()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PENDING;
    }

    public function setPaymentStatusToPending()
    {
        $this->payment_status = self::PAYMENT_STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isPaymentStatusPaid()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function setPaymentStatusToPaid()
    {
        $this->payment_status = self::PAYMENT_STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function isPaymentStatusCancelled()
    {
        return $this->payment_status === self::PAYMENT_STATUS_CANCELLED;
    }

    public function setPaymentStatusToCancelled()
    {
        $this->payment_status = self::PAYMENT_STATUS_CANCELLED;
    }

    /**
     * Gera o PDF do bilhete e retorna o objeto mPDF ou envia para o browser.
     * @return mixed
     */
    public function generateTicketPdf()
    {
        // Define o caminho para a view que criámos
        // Atenção: O caminho é relativo à aplicação que chama, então usamos alias
        $content = Yii::$app->controller->renderPartial('@common/views/pdf/ticket', [
            'model' => $this
        ]);

        // Configuração do PDF
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER, // Mostra no navegador
            'content' => $content,
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Bilhete - ' . $this->event->name],
            'methods' => [
                'SetHeader' => ['SciEventLink - Bilhete Eletrónico'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        return $pdf->render();
    }
}
