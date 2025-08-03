<?php

namespace app\controllers;

use app\models\DatesMeetings;
use app\models\Meetings;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;

class MeetingsController extends \yii\rest\ActiveController
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
                'Origin' => [isset($_SERVER['HTTP_OROGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
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
            'only' => [''],
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

    public function actionCreate()
    {
        
        $model = new Meetings();
        $post = Yii::$app->request->post();

        $model->load($post, '');
        if ($model->validate()) {
            $user = User::findOne(['email' => $post['email']]);
            if ($user) {
                if ($user->validatePassword($post['password'])) {
                    Yii::$app->response->statusCode = 403;
                }
            } else {
                $user = new User();
                $user->load($post, '');
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($user->password);
                $user->token = Yii::$app->security->generateRandomString();
                if ($user->save()) {
                } else {
                    return $this->asJson([
                        'error' => [
                            'code' => 422,
                            'message' => 'Validation error',
                            'errors' => $model->getErrors(),
                        ]
                    ]);
                };
            }
            $user->hash = Yii::$app->security->generateRandomString();
            $user->save(false);

            $model->hash = Yii::$app->security->generateRandomString();
            $model->leader_id = $user->id;
            $model->save(false);

            foreach ($model->dates as $value) {

                $model_date = new DatesMeetings();
                $model_date->meetings_id = $model->id;
                $model_date->date = date('Y-m-d', strtotime($value));
                if ($model_date->save()) {
                } else {
                    return $this->asJson([
                        'error' => [
                            'code' => 422,
                            'message' => 'Validation error',
                            'errors' => $model_date->getErrors(),
                        ]
                    ]);
                }
            }

            return $this->asJson([
                'data' => [
                    'meet' => [
                        'hash' => $model->hash,
                    ],
                    'user' => [
                        'id' => $user->id,
                        'leaderHash' => $user->hash,
                    ]
                ]
            ]);
        } else {
            return $model->getErrors();
        }
    }
}
