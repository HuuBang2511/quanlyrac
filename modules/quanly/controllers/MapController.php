<?php

namespace app\modules\quanly\controllers;

use app\modules\quanly\models\Diemthugom;
use yii\web\Controller;
use yii\web\Response;
use Yii;

class MapController extends Controller
{
    public $layout = '@app/views/layouts/map/main';

    public function actionQlrac()
    {
        return $this->render('qlrac');
    }

    /**
     * Action để lấy các giá trị duy nhất cho các bộ lọc dưới dạng JSON.
     * Dữ liệu này được sử dụng để điền vào các dropdown trên giao diện lọc.
     * @return Response
     */
    public function actionGetFilterOptions()
    {
        // Đảm bảo action này chỉ trả về JSON
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Danh sách các trường cần lấy giá trị duy nhất để lọc
        $filterFields = [
            'ward',
            'area',
            'loai_thu',
            'loai_khach_hang',
            'loai_rac_thai',
            'doi_tuong',
            'nhan_vien'
        ];

        $options = [];

        foreach ($filterFields as $field) {
            // Truy vấn để lấy các giá trị không trùng lặp, không rỗng và sắp xếp theo thứ tự
            $values = Diemthugom::find()
                ->select($field)
                ->where(['is not', $field, null])
                ->andWhere(['<>', $field, ''])
                ->distinct()
                ->orderBy($field)
                ->asArray()
                ->all();
            
            // Trích xuất giá trị từ mảng kết quả
            $options[$field] = array_column($values, $field);
        }

        return $this->asJson($options);
    }
}
