<?php
namespace frontend\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use frontend\models\Mainpage;
use yii\db\ActiveQuery;
use common\models\User;
use common\models\News;

/**
 * Site controller
 */
class MainpageController extends Controller
{
    public function actionMain(){
        $userLastRegistered = User::find()
                ->orderBy(['created_at' => SORT_DESC])
                            ->limit(8)
                            ->asArray()
                            ->all();
       $News=(new \yii\db\Query())
                   ->select(["id",
                             "Header",
                             "News",
                             "Author",
                             "Type_News",
                             "Date"])
                      ->from('News')
                      ->orderBy(['id' => SORT_DESC])
                      ->limit(3)
                      ->all();








        $userCount=(new \yii\db\Query())
            ->select(["id","username","subname"])
            ->from('user')
            ->count();
           $userLastRegisteredresult=$userLastRegistered;
            return $this->render('main', [
                'userLastRegisteredresult' => $userLastRegisteredresult,
                'userCount' => $userCount,
                'News'=>$News
            ]);
    }
}