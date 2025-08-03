<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "dates_meetings".
 *
 * @property int $id
 * @property int $meetings_id
 * @property string $date
 *
 * @property Meetings $meetings
 */
class DatesMeetings extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dates_meetings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['date', 'required'],

            [['meetings_id'], 'required'],
            [['meetings_id'], 'integer'],
            [['meetings_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meetings::class, 'targetAttribute' => ['meetings_id' => 'id']],
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
            'date' => 'Date',
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
}
