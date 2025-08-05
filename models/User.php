<?php

namespace app\models;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string|null $email
 * @property string|null $password
 * @property string|null $token
 * @property string|null $hash
 *
 * @property Meetings[] $meetings
 * @property UsersMeetings[] $usersMeetings
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{


    /**
     * {@inheritdoc}
     */


    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required', 'on' => 'login'],
            ['email', 'email', 'on' => 'send'],
            // ['email', 'exist', 'on' => 'send'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password' => 'Password',
            'token' => 'Token',
            'hash' => 'Hash',
        ];
    }

    /**
     * Gets query for [[Meetings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMeetings()
    {
        // return $this->hasMany(Meetings::class, ['leader_id' => 'id']);
    }

    /**
     * Gets query for [[UsersMeetings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsersMeetings()
    {
        // return $this->hasMany(UsersMeetings::class, ['users_id' => 'id']);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        // return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        // return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
}
