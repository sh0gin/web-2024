<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "users_meetings".
 *
 * @property int $id
 * @property int $meetings_id
 * @property int $users_id
 * @property string $availables
 *
 * @property Meetings $meetings
 * @property Users $users
 */
class UsersMeetings extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_meetings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['meetings_id', 'users_id', 'availables'], 'required'],
            [['meetings_id', 'users_id'], 'integer'],
            [['availables'], 'safe'],
            [['meetings_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meetings::class, 'targetAttribute' => ['meetings_id' => 'id']],
            [['users_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['users_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'meetings_id' => 'Meetings ID',
            'users_id' => 'Users ID',
            'availables' => 'Availables',
        ];
    }

    /**
     * Gets query for [[Meetings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeetings()
    {
        return $this->hasOne(Meetings::class, ['id' => 'meetings_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasOne(Users::class, ['id' => 'users_id']);
    }

}
