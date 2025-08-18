<?php

namespace app\modules\quanly\controllers;

use Yii;
use app\modules\quanly\models\Diemthugom;
use app\modules\quanly\models\DiemthugomSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\quanly\base\QuanlyBaseController;
use app\modules\quanly\models\form\FormThongkeKhonggian;
use yii\db\Query;

/**
 * DiemthugomController implements the CRUD actions for Diemthugom model.
 */
class DiemthugomController extends Controller
{

    public $title = "Điểm thu gom";
    public $layout = '@app/modules/layouts/main_khach';

    /**
     * Lists all Diemthugom models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DiemthugomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionThongkeKhonggian(){
        $request = \Yii::$app->request;
        $data = [];
        $geojson = null;
        $model = new FormThongkekhonggian();
        $queryParams['FormThongkekhonggian'] = $request->queryParams;

        $loaidiemthugom = [
            1 => 'Điểm thu gom đã xác thực không cập nhật',
            2 => 'Điểm thu gom đã xác thực có cập nhật',
            3 => 'Điểm thu gom chưa xác thực',
        ];

        $model->load($queryParams);
        if($request->isPost && $model->load($request->post())) {
            return $this->redirect([
                'thongke-khonggian',
                'geo_x' => $model->geo_x,
                'geo_y' => $model->geo_y,
                'bankinh' => $model->bankinh,
            ]);
        }

        if($queryParams['FormThongkekhonggian'] != null) {
            $sql_point = "st_transform(st_setsrid(st_makepoint($model->geo_x, $model->geo_y), 4326), 32648)";
            $sql_circle = "st_buffer($sql_point, $model->bankinh)";
            $sql_geom = "st_transform(st_setsrid(geom, 4326), 32648)";
            $sql_contains = "st_contains($sql_circle, $sql_geom)";
            

            $data = (new Query())->select(['contracts.*, ST_Distance(st_transform(st_setsrid(st_makepoint(' . $model->geo_x . ',' . $model->geo_y . '), 4326), 32648),st_transform(st_setsrid(geom, 4326),32648))  as dist'])
                ->from('contracts')
                ->andWhere($sql_contains)
                ->all();

            //dd($data);
           
            $geojson = [];
            if(sizeof($data) > 0){
                foreach($data as $i => $item){
                    $geojson[] = [
                        'type' => 'Feature',
                        'properties' => [
                            'popupContent' =>  'Địa chỉ: '.$item['address'].'<br> Khu vực: '.$item['area'].'<br> Loại điểm thu gom: '.$loaidiemthugom[$item['status']],
                        ],
                        'geometry' => [
                            'type' => 'Point',
                            "coordinates" => [$item['longitude'], $item['latitude']]
                        ],
                    ];
                }
            }

            //dd($geojson);

            $dataProvider = $model->searchKhonggian(Yii::$app->request->queryParams);
        }else{
            $dataProvider = null;
        }

        return $this->render('thongke-khonggian',[
            'model' => $model,
            'data' => $data,
            'dataProvider' => $dataProvider,
            'geojson' => json_encode($geojson),
        ]);
    }


    /**
     * Displays a single Diemthugom model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;

        $loaidiemthugom = [
            1 => 'Điểm thu gom đã xác thực không cập nhật',
            2 => 'Điểm thu gom đã xác thực có cập nhật',
            3 => 'Điểm thu gom chưa xác thực',
        ];

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Điểm thu gom #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                        'loaidiemthugom' => $loaidiemthugom,
                    ]),
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"])
                ];
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
                'loaidiemthugom' => $loaidiemthugom,
            ]);
        }
    }

    /**
     * Creates a new Diemthugom model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Diemthugom();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Thêm mới Diemthugom",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Lưu',['class'=>'btn btn-primary float-left','type'=>"submit"]).
                            Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Thêm mới Diemthugom",
                    'content'=>'<span class="text-success">Thêm mới Diemthugom thành công</span>',
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                            Html::a('Tiếp tục thêm mới',['create'],['class'=>'btn btn-primary float-left','role'=>'modal-remote'])
                ];
            }else{
                return [
                    'title'=> "Create new Diemthugom",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu',['class'=>'btn btn-primary float-left','type'=>"submit"])

                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->customer_id]);
            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }

    }

    /**
     * Updates an existing Diemthugom model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Cập nhật Diemthugom #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu',['class'=>'btn btn-primary float-left','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Diemthugom #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                            Html::a('Lưu',['update','id'=>$id],['class'=>'btn btn-primary float-left','role'=>'modal-remote'])
                ];
            }else{
                 return [
                    'title'=> "Cập nhật Diemthugom #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                                Html::button('Lưu',['class'=>'btn btn-primary float-left','type'=>"submit"])
                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->customer_id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Diemthugom model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->status = 0;

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Xóa Diemthugom #".$id,
                    'content'=>$this->renderAjax('delete', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Đóng',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                        Html::button('Xóa',['class'=>'btn btn-danger float-left','type'=>"submit"])
                ];
            }else if($request->isPost && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Diemthugom #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                        Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];
            }else{
                return [
                    'title'=> "Update Diemthugom #".$id,
                    'content'=>$this->renderAjax('delete', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-light float-right','data-bs-dismiss'=>"modal"]).
                        Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->customer_id]);
            } else {
                return $this->render('delete', [
                    'model' => $model,
                    'const' => $this->const,
                ]);
            }
        }
    }

    
    /**
     * Finds the Diemthugom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Diemthugom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Diemthugom::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
