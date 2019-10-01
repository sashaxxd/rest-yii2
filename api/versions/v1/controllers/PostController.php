<?php
namespace api\versions\v1\controllers;

use common\models\Post;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;

class PostController extends ActiveController
{
    public $modelClass = 'common\models\Post';

//    public function behaviors()
//    {
//
//
//
//
//        $behaviors = parent::behaviors();
//        $behaviors['authenticator'] = [
//            'class' => QueryParamAuth::className(),
//        ];
//
//
//
//        return $behaviors;
//    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['view', 'index'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
                'languages' => [
                    'en',
                    'de',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(
            parent::actions(),
            [
                'index' => [
                    'class' => 'yii\rest\IndexAction',

                    'modelClass' => $this->modelClass,
                    'checkAccess' => [$this, 'checkAccess'],
                    'prepareDataProvider' => function ($action) {
                        /* @var $model Post */
                        $model = new $this->modelClass;
                        $query = $model::find();
                        $dataProvider = new ActiveDataProvider(['query' => $query]);

                        $model->setAttribute('title', @$_GET['title']);
                        $query->andFilterWhere(['like', 'title', $model->title]);

                        return $dataProvider;
                    }
                ]
            ]
        );
    }
}
