<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "products".
 *
 * @property string $id
 * @property int $category_id
 * @property string $name
 * @property string $size
 * @property string $composition
 * @property string $color
 * @property int $price
 * @property string $availability
 * @property string $popular
 * @property string $additionally
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'name', 'size', 'composition', 'color', 'price', 'additionally'], 'required'],
            [['category_id', 'price'], 'integer'],
            [['availability', 'popular', 'additionally'], 'string'],
            [['name', 'size', 'composition', 'color'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'name' => 'Name',
            'size' => 'Size',
            'composition' => 'Composition',
            'color' => 'Color',
            'price' => 'Price',
            'availability' => 'Availability',
            'popular' => 'Popular',
            'additionally' => 'Additionally',
        ];
    }


    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * все свойства связянной таблицы
     */
//    public function fields()
//
//    {
//
//        $fields = parent::fields();
//        //Cвязанные таблицы в АПИ
//        $fields[] = 'category';
//
//
//        return $fields;
//
//    }

    /**
     *  свойства связянной таблицы
     */
    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
//            'category' => 'category.name' - вся категория
              'category_name' => function(){
              return $this->category->name; //Вернет только имя
              },
        ]);
    }


    protected function verbs()
    {
        return [
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH', 'POST'],
            'delete' => ['DELETE'],
            'view' => ['GET'],
            'index' => ['GET'],
        ];
    }
}
