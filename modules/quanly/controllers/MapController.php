<?php


namespace app\modules\quanly\controllers;


use app\modules\quanly\base\QuanlyBaseController;
use yii\web\Controller;
use yii;

class MapController extends Controller
{
    public $layout = '@app/views/layouts/map/main';


    public function actionQlrac()
    {
        return $this->render('qlrac');
    }

}