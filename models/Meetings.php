<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "meetings".
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $hash
 * @property int $leader_id
 * @property string $start
 * @property string $end
 * @property int $is_block
 *
 * @property DatesMeetings[] $datesMeetings
 * @property Files[] $files
 * @property Users $leader
 * @property UsersMeetings[] $usersMeetings
 */
class Meetings extends \yii\db\ActiveRecord
{
    public $password;
    public $dates;
    public $confirm;
    public $email;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'meetings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'start', 'end', 'password'], 'required'],
            [['password'], 'match', 'pattern' => '/(?=.*\*)[A-Za-z0-9]{7,10}/'],
            ['email', 'email'],
            [['confirm'], 'boolean', 'trueValue' => 1, 'falseValue' => 0],
            ['start', 'time', 'format' => 'kk:mm'],
            ['end', 'time', 'format' => 'kk:mm'],
            // [['description'], 'default', 'value' => null],
            // [['is_block'], 'default', 'value' => 0],
            // [['description'], 'string'],
            // [['leader_id', 'is_block'], 'integer'],
            // [['start', 'end'], 'safe'],
            // [['title', 'hash'], 'string', 'max' => 255],
            // [['hash'], 'unique'],
            // [['leader_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['leader_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'hash' => 'Hash',
            'leader_id' => 'Leader ID',
            'start' => 'Start',
            'end' => 'End',
            'is_block' => 'Is Block',
        ];
    }

    /**
     * Gets query for [[DatesMeetings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDatesMeetings()
    {
        // return $this->hasMany(DatesMeetings::class, ['meetings_id' => 'id']);
    }

    /**
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        // return $this->hasMany(Files::class, ['meetings_id' => 'id']);
    }

    /**
     * Gets query for [[Leader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        // return $this->hasOne(Users::class, ['id' => 'leader_id']);
    }

    /**
     * Gets query for [[UsersMeetings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersMeetings()
    {
        // return $this->hasMany(UsersMeetings::class, ['meetings_id' => 'id']);
    }

}
