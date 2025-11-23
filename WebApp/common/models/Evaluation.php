<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "evaluation".
 *
 * @property int $id
 * @property int $article_id
 * @property int $evaluator_id
 * @property float|null $score
 * @property string|null $comments
 * @property string|null $evaluation_date
 *
 * @property Article $article
 * @property User $evaluator
 */
class Evaluation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evaluation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['score', 'comments'], 'default', 'value' => null],
            [['article_id', 'evaluator_id'], 'required'],
            [['article_id', 'evaluator_id'], 'integer'],
            [['score'], 'number'],
            [['comments'], 'string'],
            [['evaluation_date'], 'safe'],
            [['article_id', 'evaluator_id'], 'unique', 'targetAttribute' => ['article_id', 'evaluator_id']],
            [['article_id'], 'exist', 'skipOnError' => true, 'targetClass' => Article::class, 'targetAttribute' => ['article_id' => 'id']],
            [['evaluator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['evaluator_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'evaluator_id' => 'Evaluator ID',
            'score' => 'Score',
            'comments' => 'Comments',
            'evaluation_date' => 'Evaluation Date',
        ];
    }

    /**
     * Gets query for [[Article]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArticle()
    {
        return $this->hasOne(Article::class, ['id' => 'article_id']);
    }

    /**
     * Gets query for [[Evaluator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluator()
    {
        return $this->hasOne(User::class, ['id' => 'evaluator_id']);
    }

}
