<?php

namespace common\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property int $registration_id
 * @property string $title
 * @property string|null $abstract
 * @property string $file_path
 * @property string $status
 *
 * @property Evaluation[] $evaluations
 * @property User[] $evaluators
 * @property Registration $registration
 */
class Article extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';

    /**
     * @var UploadedFile
     */
    public $articleFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['abstract'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'submitted'],
            [['registration_id', 'title'], 'required'],
            [['registration_id'], 'integer'],
            [['abstract', 'status'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['file_path'], 'string', 'max' => 512],
            [['articleFile'], 'file',
                'skipOnEmpty' => false,
                'extensions' => 'pdf',
                'checkExtensionByMimeType' => false,
                'maxSize' => 1024 * 1024 * 10
            ],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['registration_id'], 'exist', 'skipOnError' => true, 'targetClass' => Registration::class, 'targetAttribute' => ['registration_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'registration_id' => 'Registration ID',
            'title' => 'Title',
            'abstract' => 'Abstract',
            'file_path' => 'File Path',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Evaluations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluations()
    {
        return $this->hasMany(Evaluation::class, ['article_id' => 'id']);
    }

    /**
     * Gets query for [[Evaluators]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluators()
    {
        return $this->hasMany(User::class, ['id' => 'evaluator_id'])->viaTable('evaluation', ['article_id' => 'id']);
    }

    /**
     * Gets query for [[Registration]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegistration()
    {
        return $this->hasOne(Registration::class, ['id' => 'registration_id']);
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_SUBMITTED => 'submitted',
            self::STATUS_IN_REVIEW => 'in_review',
            self::STATUS_ACCEPTED => 'accepted',
            self::STATUS_REJECTED => 'rejected',
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
    public function isStatusSubmitted()
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    public function setStatusToSubmitted()
    {
        $this->status = self::STATUS_SUBMITTED;
    }

    /**
     * @return bool
     */
    public function isStatusInreview()
    {
        return $this->status === self::STATUS_IN_REVIEW;
    }

    public function setStatusToInreview()
    {
        $this->status = self::STATUS_IN_REVIEW;
    }

    /**
     * @return bool
     */
    public function isStatusAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function setStatusToAccepted()
    {
        $this->status = self::STATUS_ACCEPTED;
    }

    /**
     * @return bool
     */
    public function isStatusRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function setStatusToRejected()
    {
        $this->status = self::STATUS_REJECTED;
    }
}
