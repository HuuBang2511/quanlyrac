<?php


namespace app\modules\quanly\controllers;


use app\modules\quanly\base\QuanlyBaseController;
use app\modules\quanly\models\Diemthugom;
use Yii;

class DashboardController extends QuanlyBaseController
{
    public function actionIndex()
    {
        $count['diemthugom1'] = Diemthugom::find()->where(['status' => 1])->andWhere('geom is not null')->count();
        $count['diemthugom2'] = Diemthugom::find()->where(['status' => 2])->andWhere('geom is not null')->count();
        $count['diemthugom3'] = Diemthugom::find()->where(['status' => 3])->andWhere('geom is not null')->count();

        $statistic['phuongxa'] = Diemthugom::find()->select('count(ward) as value, ward as name')
        ->where('geom is not null')
        ->groupBy('ward')->asArray()->all();
        $statistic['loaidiemthugom'] = [
            [
                'value' => $count['diemthugom1'],
                'name' => 'Điểm thu gom rác đã xác thực không cập nhật'
            ],
            [
                'value' => $count['diemthugom2'],
                'name' => 'Điểm thu gom rác đã xác thực có cập nhật'
            ],
            [
                'value' => $count['diemthugom3'],
                'name' => 'Điểm thu gom rác chưa xác thực'
            ],
        ];

        //dd($statistic);

        return $this->render('index', [
            'count' => $count,
            'statistic' => $statistic,
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