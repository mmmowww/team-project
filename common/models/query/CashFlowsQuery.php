<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CashFlows]].
 *
 * @see \common\models\CashFlows
 */
class CashFlowsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\CashFlows[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\CashFlows|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
