<?php

namespace app\controllers;

use app\models\DatesMeetings;
use app\models\Files;
use app\models\Meetings;
use app\models\User;
use app\models\UsersMeetings;
use Yii;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\web\UploadedFile;

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
                if (!$user->validatePassword($post['password'])) {
                    Yii::$app->response->statusCode = 403;
                }
            } else {
                $user = new User();
                $user->scenario = 'login';
                $user->load($post, '');
                $user->password = Yii::$app->getSecurity()->generatePasswordHash($user->password);
                $user->token = Yii::$app->security->generateRandomString();
                $user->hash = Yii::$app->security->generateRandomString();
                if ($user->save()) {
                } else {
                    return $this->asJson([
                        'error' => [
                            'code' => 422,
                            'message' => 'Validation error',
                            'errors' => $model->errors(),
                        ]
                    ]);
                };
            }

            $user->save(false);

            $model->hash = Yii::$app->security->generateRandomString();
            $model->leader_id = $user->id;
            $model->save(false);
            $user_meetings = new UsersMeetings();
            $user_meetings->meetings_id = $model->id;
            $user_meetings->users_id = $user->id;
            $user_meetings->availables = [0, 0, 0, 0, 0, 0, 0, 0];
            $user_meetings->save();

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
                            'errors' => $model_date->errors,
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
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => 'Validation error',
                    'errors' => $model->errors,
                ]
            ]);
        }
    }

    public function actionDeleteUser($meetHash, $leaderHash, $userId)
    {
        $model_meet = Meetings::findOne(['hash' => $meetHash]);
        $model_user_in_meet = UsersMeetings::findOne(['users_id' => $userId, 'meetings_id' => $model_meet->id]);
        $model_leader = User::findOne(['hash' => $leaderHash]);
        if ($model_user_in_meet && $model_meet && $model_leader) {
            if ($model_meet->leader_id == $model_leader->id) {
                Yii::$app->response->statusCode = 204;
                $model_user_in_meet->delete();
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionDeleteFile($meetHash, $leaderHash, $filename)
    {
        $model_meet = Meetings::findOne(['hash' => $meetHash]);
        $model_leader = User::findOne(['hash' => $leaderHash]);

        if ($model_meet) {
            if ($model_meet->leader_id == $model_leader->id) {
                $model_files = Files::findOne(['meetings_id' => $model_meet->id, 'filename' => $filename]);
                unlink(__DIR__ . "/../models/uploads/$filename");
                $model_files->delete();
                Yii::$app->response->statusCode = 204;
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionBlockMeetings($meetHash, $leaderHash)
    {
        $model_meet = Meetings::findOne(['hash' => $meetHash]);
        $model_leader = User::findOne(['hash' => $leaderHash]);

        if ($model_meet) {
            if ($model_meet->leader_id == $model_leader->id) {
                if (!$model_meet->is_block) {
                    $model_meet->is_block = 1;
                    $model_meet->save(false);
                } else {
                    Yii::$app->response->statusCode = 409;
                    return $this->asJson([
                        'error' => [
                            'code' => 409,
                            'message' => 'Встреча уже заблокированна',
                        ]
                    ]);
                }
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionUploadFiles($meetHash, $leaderHash)
    {
        $model = new Files();
        $meetings_id = Meetings::findOne(['hash' => $meetHash])->id;
        $leader_id = User::findOne(['hash' => $leaderHash])->id;

        if ($meetings_id) {
            if (Meetings::findOne(['hash' => $meetHash])->leader_id == $leader_id) {
                if (UploadedFile::getInstancesByName('img')) {
                    $model->scenario = "img";
                    $model->meetings_id = $meetings_id;
                    $model->filename = UploadedFile::getInstancesByName('img');
                    $model->extension = $model->filename[0]->extension;
                } else  if (UploadedFile::getInstancesByName('files')) {
                    $model->filename = UploadedFile::getInstancesByName('files');
                    $model->scenario = "files";
                    $model->meetings_id = $meetings_id;
                    $model->extension = 'pdf';
                } else {
                    return $this->asJson([
                        'error' => [
                            'code' => 422,
                            'message' => 'Validation error',
                            'errors' => [
                                'files' => 'Вы не загрузили файл',
                            ],
                        ]
                    ]);
                }

                if ($model->validate()) {
                    foreach (UploadedFile::getInstancesByName($model->scenario) as $value) {
                        $model = new Files();
                        $model->meetings_id = $meetings_id;
                        $model->extension = $value->extension;
                        $model->filename = $model->upload($value);
                        $model->save(false);
                    }
                    Yii::$app->response->statusCode = 204;
                } else {
                    return $this->asJson([
                        $model->errors,
                    ]);
                }
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionSendEmails($meetHash, $leaderHash)
    {
        $meetings = Meetings::findOne(['hash' => $meetHash]);

        if ($meetings) {
            $user = User::findOne(['hash' => $leaderHash]);
            if ($user) {
                $query = new Query();

                $query->select(['users_meetings.id', 'users.email']) // 
                    ->from('users_meetings')
                    ->innerJoin('users', 'users_meetings.users_id = users.id')
                    ->where(['meetings_id' => $meetings->id]);

                foreach (Yii::$app->request->post() as $value) {
                    $last = $query->where(['users.email' => $value])->all();
                    if (!$last) {
                        return $this->asJson([
                            'error' => [
                                'code' => 422,
                                'message' => 'Validation error',
                                'errors' => [
                                    "Являеться не корректным E-mail адресом",
                                ],
                            ]
                        ]);
                    };
                }
                Yii::$app->response->statusCode = 204;
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionViewMeeting($meetHash)
    {
        $model = Meetings::findOne(['hash' => $meetHash]);
        if ($model) {
            $leader = User::findOne($model->leader_id);
            $query = new Query();
            $dates = array_reduce($query
                ->select('date')
                ->from('dates_meetings')
                ->where(['meetings_id' => $model->id])
                ->all(), function ($carry, $item) {
                $carry[] = $item['date'];
                return $carry;
            });
            // $dates = array_map(fn($item) => $item, $query // как мапом делать .. {{host2}}api/meet/SM6N-pqV07NnEMAfYuqEhH6ldRWQLLHI
            //     ->select('date')
            //     ->from('dates_meetings')
            //     ->where(['meetings_id' => $model->id])
            //     ->all());
            // return $dates;
            $image = Files::findOne(['meetings_id' => $model->id, 'extension' => ['png', 'jpg', 'jpeg']]);
            if ($image) {
                $image = $image->filename;
            } else {
                $image = Null;
            }

            $files = array_reduce(Files::find()
                ->where(['meetings_id' => $model->id, 'extension' => ['pdf']])
                ->all(), function ($carry, $item) {
                $carry[] = $item['filename'];
                return $carry;
            });
            $query = new Query();

            $users = array_reduce($query
                ->select('users.email, users.id, availables')
                ->from('users_meetings')
                ->where(['meetings_id' => $model->id])
                ->innerJoin('users', 'users.id = users_meetings.users_id')
                ->all(), function ($carry, $item) {
                $carry[$item['email']] = ['id' => $item['id'], 'availables' => $item['availables']];
                return $carry;
            });


            return $this->asJson([
                'meet' => [
                    'title' => $model->title,
                    'description' => $model->description,
                    'dates' => $dates,
                    'start' => $model->start,
                    'end' => $model->end,
                    'interval' => 60,
                    'block' => $model->is_block,
                    'users' => $users,
                    'leader' => [
                        'id' => $leader->id,
                        'login' => $leader->email,
                    ],
                    'img' => $image,
                    'files' => $files,
                ]
            ]);
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionJoinMeeting($meetHash)
    {
        $model = Meetings::findOne(['hash' => $meetHash]);
        $post = Yii::$app->request->post();
        if ($model) {
            if (!$model->is_block == 1) {
                $user_model = User::findOne(['email' => $post['email']]);
                if ($user_model) {
                    $result = UsersMeetings::findOne(['meetings_id' => $model->id, 'users_id' => $user_model->id]);
                    $result_for_leader_test = Meetings::findOne(['id' => $model->id, 'leader_id' => $user_model->id]);

                    if (!$result && !$result_for_leader_test) {
                        if ($user_model->validatePassword($post['password'])) {
                            $models_new_use_meetings = new UsersMeetings();
                            $models_new_use_meetings->meetings_id = $model->id;
                            $models_new_use_meetings->users_id = $user_model->id;
                            $models_new_use_meetings->availables = [0, 0, 0, 0, 0, 0, 0, 0, 0];
                            $user_model->token = Yii::$app->security->generateRandomString();
                            $models_new_use_meetings->save(false);
                            $user_model->save(false);
                            return $this->asJson([
                                'data' => [
                                    'user' => [
                                        'id' => $user_model->id,
                                        'token' => $user_model->token,
                                    ]
                                ]
                            ]);
                        } else {
                            Yii::$app->response->statusCode = 401;
                        };
                    } else {
                        Yii::$app->response->statusCode = 403;
                    }
                } else {
                    Yii::$app->response->statusCode = 401;
                }
            } else {
                Yii::$app->response->statusCode = 400;
                return $this->asJson([
                    'error' => [
                        'code' => 400,
                        'message' => 'Встреча заблокированна',
                    ]
                ]);
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionChangeAvailables($meetHash, $userID)
    {
        $model_meet = Meetings::findOne(['hash' => $meetHash]);
        $user_model = User::findOne($userID);

        if ($model_meet && $user_model) {
            $model = UsersMeetings::findOne(['meetings_id' => $model_meet->id, 'users_id' => $user_model->id]);
            if ($model) {
                Yii::$app->response->statusCode = 204;
                $model->availables = Yii::$app->request->post()['availables'];
                $model->save(false);
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionDeleteMeeting($meetHash, $leaderHash)
    {
        $meetings = Meetings::findOne(['hash' => $meetHash]);
        $leader = User::findOne(['hash' => $leaderHash]);

        if ($meetings && $leader) {
            if (Meetings::findOne(['hash' => $meetHash, 'leader_id' => $leader->id])) {
                Yii::$app->response->statusCode = 204;
                $meetings->delete();
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionDownloadFile($meetHash, $filename)
    {
        $meetings = Meetings::findOne(['hash' => $meetHash]);
        if ($meetings) {
            $path_files = __DIR__ . "/../models/uploads/$filename";
            if (!is_file($path_files)) {
                Yii::$app->response->statusCode = 404;
            } else {
                Yii::$app->response->sendFile($path_files);
            };
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }

    public function actionCheckLeader($meetHash, $leaderHash)
    {
        $meetings = Meetings::findOne(['hash' => $meetHash]);
        $leader = User::findOne(['hash' => $leaderHash]);
        if ($meetings && $leader) {
            if ($meetings->leader_id == $leader->id) {
                Yii::$app->response->statusCode = 204;
            } else {
                Yii::$app->response->statusCode = 403;
            }
        } else {
            Yii::$app->response->statusCode = 404;
        }
    }
}
