<?php

namespace app\controllers;

use app\models\Meetings;
use app\models\User;
use app\models\UsersMeetings;
use Yii;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;

class UserController extends \yii\rest\ActiveController
{

    public $modelClass = '';
    public $enableCrsfValidation = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'login' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ],
        ];

        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['logout', 'profile'],
        ];
        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];



        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        $model = new User();
        $model->scenario = 'login';
        $model->load(Yii::$app->request->post(), '');

        if ($model->validate()) {
            $user = User::findOne(['email' => $model->email]);
            if ($user && $user->validatePassword($model->password)) {

                $user->token = Yii::$app->security->generateRandomString();
                $user->save(false);
                return $this->asJson([
                    'data' => [
                        'token' => $user->token,
                    ]
                ]);
            } else {
                Yii::$app->response->statusCode = 401;
            }
        } else {
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $model->getErrors(),
                ]
            ]);
        }
    }

    public function actionLogout()
    {
        $user = User::findOne(Yii::$app->user->id);
        $user->token = NULL;
        $user->save();
        Yii::$app->response->statusCode = 204;
    }

    public function actionProfile()
    {
        $user = User::findOne(Yii::$app->user->id);
        $meets_leader = Meetings::findAll(['leader_id' => Yii::$app->user->id]);
        $result_leader = [];
        foreach ($meets_leader as $value) {
            $result_leader[] = ['id' => $value->id, 'title' => $value->title, 'hash' => $value->hash, 'block' => $value->is_block];
        }
        $meets_parting = UsersMeetings::findAll(['users_id' => Yii::$app->user->id]);
        $result_parting = [];
        foreach ($meets_parting as $item) {
            $value = Meetings::findOne($item->meetings_id);
            $result_parting[] = ['id' => $value->id, 'title' => $value->title, 'hash' => $value->hash, 'block' => $value->is_block];
        }

        return $this->asJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'identifier' => $user->hash,
                'meets_leader' => $result_leader,
                'meets_partic' => $result_parting,
            ]
        ]);
    }
}
