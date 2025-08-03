<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property int $meetings_id
 * @property string $filename
 * @property string $extension
 *
 * @property Meetings $meetings
 */
class Files extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['meetings_id', 'filename', 'extension'], 'required'],
            [['meetings_id'], 'integer'],
            [['filename', 'extension'], 'string', 'max' => 255],
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
            'filename' => 'Filename',
            'extension' => 'Extension',
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
