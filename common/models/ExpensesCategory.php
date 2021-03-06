<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "expenses_category".
 *
 * @property int $id
 * @property string $title Название
 * @property int|null $user_id Создатель
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property User $user
 */
class ExpensesCategory extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'user_id',
            ],
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expenses_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'unique'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'min' => 2, 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'user_id' => 'Создатель',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }
    
    public static function find()
    {
        return new \common\models\query\ExpensesCategoryQuery(get_called_class());
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    public static function getExpensesCategory()
    {
        return ArrayHelper::map(
            self::find()
                ->where([
                    'user_id' =>yii::$app->user->identity->getId()
                ])
                ->asArray()
                ->all(),
            'id',
            'title');
    }

    public function checkTotal($total)
    {
        if($total!=0) {
            return $total;
        }else {
            return 0;
        }
    }

    public function getTotalCategory()
    {
        $total = Expenses::find()
            ->where(['category_id' => $this->id])
            ->sum('cost');
        return $this->checkTotal($total);
    }

    public function getTotalCategoryToday()
    {
        $total = Expenses::find()
            ->where(['category_id' => $this->id,
            'date'=> date('Y-m-d')])
            ->sum('cost');
        return $this->checkTotal($total);
    }

    public function getTotalCategoryCurrentMonth()
    {
        $total = Expenses::find()
            ->where(['category_id' => $this->id])
            ->andWhere(['like', 'date', date('Y-m')])
            ->sum('cost');
        return $this->checkTotal($total);
    }
    //настройка ответа на json запрос
    public function fields()
    {
        $fields = parent::fields();
        //Удаление ненужных полей
        unset($fields['created_at']);
        unset($fields['updated_at']);
        //добавление сумм затрат за различные периоды
        $fields = array_merge($fields,[
            'total'=> function()
            {
                return $this->getTotalCategory();
            },
            'totaltoday'=>function()
            {
                return $this->getTotalCategoryToday();
            },
            'totalmonth'=>function()
            {
                return $this->getTotalCategoryCurrentMonth();
            }
        ]);
        return $fields;
    }
}
