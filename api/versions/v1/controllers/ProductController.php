<?php

namespace api\versions\v1\controllers;

use common\models\Products;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\Response;

class ProductController extends ActiveController


{

    public function behaviors()

    {

        return [

            [

                'class' => \yii\filters\ContentNegotiator::className(),
// Походу убирает запросы
//                'only' => ['index', 'view'],

                'formats' => [

                    'application/json' => \yii\web\Response::FORMAT_JSON,

                ],


            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['post'],
                    'update' => ['PUT'],
                    'delete' => ['delete'],
                    'search' => ['get']
                ],
            ]


        ];
    }

    public $modelClass = 'common\models\Products';


//    Функция количества записей API
    public function afterAction($action, $result)
    {
        if (isset($result->pagination) && ($result->pagination !== false)) {
            $result->pagination->setPageSize(56);
        }
        return parent::afterAction($action, $result);
    }


    public function actions()
    {
        $actions = parent::actions();
        // удаляем дефолтные акшэны
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);


        //Пишем свои
        return ArrayHelper::merge($actions, [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
//                'prepareDataProvider' => function () {
//                    $model = Products::find()->joinWith('category');
//                    return $model;
//                }
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
//                'prepareDataProvider' => null
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
//                'prepareDataProvider' => null
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => $this->modelClass,
//                'prepareDataProvider' => null
            ],

        ]);
    }


    //Cвой экшен - можно составить любую выбоку
    public function actionNew()
    {
        $result = Products::find()->where(['id' => 38])->all();
        return $result;
    }

//Cвой экшен для поиска по GET
//http://yii2-rest-api/products/search?color=%D0%9A%D0%B0%D0%BA%D0%B0%D0%BE&price=4700
    public function actionSearch()
    {
        if (!empty($_GET)) {
            $model = new $this->modelClass;
            foreach ($_GET as $key => $value) {
                if (!$model->hasAttribute($key)) {
                    throw new \yii\web\HttpException(404, 'Invalid attribute:' . $key);
                }
            }
            try {

                $query = $model->find();
                foreach ($_GET as $key => $value) {
                    if ($key != 'age') {
                        $query->andWhere(['like', $key, $value]);
                    }
                    if ($key == 'age') {
                        $agevalue = explode('-', $value);
                        $query->andWhere(['between', $key, $agevalue[0], $agevalue[1]]);

                    }

                }

                $provider = new ActiveDataProvider([
                    'query' => $query,
                    'sort' => [
                        'defaultOrder' => [
                            'id' => SORT_DESC
                        ]
                    ],
                    'pagination' => [
                        'defaultPageSize' => 20,
                    ],
                ]);
            } catch (Exception $ex) {
                throw new \yii\web\HttpException(500, 'Internal server error');
            }

            if ($provider->getCount() <= 0) {
                throw new \yii\web\HttpException(404, 'No entries found with this query string');
            } else {
                return $provider;
            }
        } else {
            throw new \yii\web\HttpException(400, 'There are no query string');
        }
    }


}
