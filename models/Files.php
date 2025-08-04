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


    public $modelClass = '';
    public $checkExtensionByMimeType = false;


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

            [['filename'], 'file', 'extensions' => ["png", "jpg", "jpeg"], 'maxSize' => 1024 * 1024 * 2, 'on' => 'img', 'message' => "Файл слишком большой.
Размер не должен превышать 2.00 МиБ."],
            [['filename'], 'file', 'extensions' => ['pdf'], 'maxFiles' => 2, 'on' => 'files', 'message' => "Файлов слишком много. Максимально
можно сохранить 2 файла."],

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

    public function upload($file)
    {
        $path = Yii::$app->security->generateRandomString() . "$file->name";
        $file->saveAs(__DIR__ . '/uploads/' . $path);
        return $path;
    }
}
