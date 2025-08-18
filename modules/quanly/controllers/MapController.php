<?php


namespace app\modules\quanly\controllers;


use app\modules\quanly\base\QuanlyBaseController;
use yii\web\Controller;
use yii;

class MapController extends Controller
{
    public $layout = '@app/views/layouts/map/main';

    public function actionDuctrong()
    {
        return $this->render('ductrong');
    }

    public function actionGiadinh()
    {
        return $this->render('giadinh');
    }

    public function actionQlrac()
    {
        return $this->render('qlrac');
    }

    public function actionMaptest()
    {   
        return $this->render('mapcopy');
    }

    public function actionCamau()
    {
        if((Yii::$app->user->identity->phuongxa != null)){
            //dd(Yii::$app->user->identity->phuongxa);
            return $this->render((Yii::$app->user->identity->phuongxa));
        }else{
            return $this->render('camau');
        }
        
    }
    public function actionVinhloi()
    {
        return $this->render('vinhloi');
    }
}