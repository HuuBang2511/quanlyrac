<?php


namespace app\modules\quanly\controllers;


use app\modules\quanly\base\QuanlyBaseController;
use app\modules\quanly\models\aphu\DonghoKh;
use app\modules\quanly\models\aphu\NhamayNuoc;
use app\modules\quanly\models\aphu\VanMangluoi;
use app\modules\quanly\models\capnuocgd\GdDonghoKhGd;
use app\modules\quanly\models\capnuocgd\GdDonghoTongGd;
use app\modules\quanly\models\capnuocgd\GdOngcai;
use app\modules\quanly\models\capnuocgd\GdVanphanphoi;
use app\modules\quanly\models\Ktvhxh;
use app\modules\quanly\models\aphu\OngPhanphoi;
use app\modules\quanly\models\capnuocgd\GdSuco;
use app\modules\quanly\models\capnuocgd\GdTrambom;
use app\modules\quanly\models\capnuocgd\GdTramcuuhoa;
use app\modules\quanly\models\capnuocgd\GdHamkythuat;
use app\modules\quanly\models\capnuocgd\DMA;
use Yii;

class DashboardController extends QuanlyBaseController
{
    public function actionIndex()
    {
        


        //dd(($sovanDma));



        return $this->render('index', [
            
        ]);
    }

    public function actionGeojson(){
        $dmas = Yii::$app->db->createCommand('SELECT st_asgeojson(geom) as geometry, madma as ten, id  FROM "v2_4326_DMA" order by madma')->queryAll();

        $g  = [];

        foreach ($dmas as $i => $dma) {
            $geometry = json_decode($dma['geometry'], true);
            $g[$i] = [
                'type' => 'Feature',
                'id' => $dma['id'],
                'properties' => [
                    'name' => $dma['ten'],
                ],
                'geometry' => [
                    'type' => $geometry['type'],
                    'coordinates' => $geometry['coordinates'],
                ]
            ];
        }

        $e = [
            'type' => 'FeatureCollection',
            'features' => $g
        ];

        //dd($e);
        return json_encode($e, JSON_UNESCAPED_UNICODE);

        //dd($results);
    }

    public function actionChitietdma($id){

        

      
       

       return $this->renderAjax('chitietdma', [
           'id'=>$id,
           
       ]); 
   }


}