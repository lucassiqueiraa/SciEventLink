<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $nif
 * @property string|null $phone
 * @property string $role
 *
 * @property User $user
 */
class UserProfile extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ROLE_ADM = 'ADM';
    const ROLE_ORG = 'ORG';
    const ROLE_PART = 'PART';
    const ROLE_EVAL = 'EVAL';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nif', 'phone'], 'default', 'value' => null],
            [['role'], 'default', 'value' => 'PART'],
            [['user_id', 'name'], 'required'],
            [['user_id'], 'integer'],
            [['role'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['nif'], 'string', 'max' => 9],
            [['phone'], 'string', 'max' => 20],
            ['role', 'in', 'range' => array_keys(self::optsRole())],
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
            'user_id' => 'User ID',
            'name' => 'Name',
            'nif' => 'Nif',
            'phone' => 'Phone',
            'role' => 'Role',
        ];
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


    /**
     * column role ENUM value labels
     * @return string[]
     */
    public static function optsRole()
    {
        return [
            self::ROLE_ADM => 'ADM',
            self::ROLE_ORG => 'ORG',
            self::ROLE_PART => 'PART',
            self::ROLE_EVAL => 'EVAL',
        ];
    }

    /**
     * @return string
     */
    public function displayRole()
    {
        return self::optsRole()[$this->role];
    }

    /**
     * @return bool
     */
    public function isRoleAdm()
    {
        return $this->role === self::ROLE_ADM;
    }

    public function setRoleToAdm()
    {
        $this->role = self::ROLE_ADM;
    }

    /**
     * @return bool
     */
    public function isRoleOrg()
    {
        return $this->role === self::ROLE_ORG;
    }

    public function setRoleToOrg()
    {
        $this->role = self::ROLE_ORG;
    }

    /**
     * @return bool
     */
    public function isRolePart()
    {
        return $this->role === self::ROLE_PART;
    }

    public function setRoleToPart()
    {
        $this->role = self::ROLE_PART;
    }

    /**
     * @return bool
     */
    public function isRoleEval()
    {
        return $this->role === self::ROLE_EVAL;
    }

    public function setRoleToEval()
    {
        $this->role = self::ROLE_EVAL;
    }
}
