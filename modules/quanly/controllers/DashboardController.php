<?php

namespace app\modules\quanly\controllers;

use app\modules\quanly\models\Diemthugom;
use yii\web\Controller;
use Yii;
use yii\web\Response;

class DashboardController extends Controller
{
    public $layout = '@app/modules/layouts/main_khach';

    public function actionIndex()
    {
        // === TỐI ƯU 1: Gộp 3 truy vấn đếm thành 1 truy vấn duy nhất ===
        // Lấy tất cả các loại count chỉ bằng một lần gọi DB, hiệu quả hơn nhiều.
        $countsByStatusRaw = Diemthugom::find()
            ->select(['status', 'COUNT(*) as total'])
            ->where(['status' => ['1', '2', '3']])
            ->andWhere('geom IS NOT NULL')
            ->groupBy('status')
            ->asArray()
            ->all();

        // Chuyển đổi kết quả về định dạng mảng ['status' => 'total'] để dễ sử dụng
        $countsByStatus = array_column($countsByStatusRaw, 'total', 'status');

        // Gán giá trị, sử dụng toán tử `?? 0` để tránh lỗi nếu status nào đó không có bản ghi
        $count = [
            'diemthugom1' => (int)($countsByStatus['1'] ?? 0),
            'diemthugom2' => (int)($countsByStatus['2'] ?? 0),
            'diemthugom3' => (int)($countsByStatus['3'] ?? 0),
        ];

        // === TỐI ƯU 2: Dọn dẹp điều kiện truy vấn cho thống kê phường xã ===
        $statistic['phuongxa'] = Diemthugom::find()
            ->select(['ward as name', 'COUNT(ward) as value'])
            // Điều kiện `ward IS NOT NULL AND ward != ''` được gộp lại
            ->where("ward IS NOT NULL AND ward <> '' AND geom IS NOT NULL")
            ->groupBy('ward')
            ->orderBy(['value' => SORT_DESC])
            ->limit(10)
            ->asArray()
            ->all();

        // Thống kê theo loại điểm thu gom (tận dụng kết quả đã có)
        $statistic['loaidiemthugom'] = [
            ['value' => $count['diemthugom1'], 'name' => 'Đã xác thực (K)'],
            ['value' => $count['diemthugom2'], 'name' => 'Đã xác thực (C)'],
            ['value' => $count['diemthugom3'], 'name' => 'Chưa xác thực'],
        ];

        // Thống kê 7 ngày gần nhất (truy vấn này đã tốt, giữ nguyên)
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $statistic['tinhhinhthugom'] = Yii::$app->db->createCommand(
            "SELECT TO_CHAR(updated_at, 'YYYY-MM-DD') AS date, COUNT(*) AS value 
             FROM contracts 
             WHERE updated_at >= :seven_days_ago AND status IN ('1', '2')
             GROUP BY date 
             ORDER BY date ASC",
            [':seven_days_ago' => $sevenDaysAgo]
        )->queryAll();
// dd($statistic['tinhhinhthugom']);
        return $this->render('index', [
            'count' => $count,
            'statistic' => $statistic,
        ]);
    }

    public function actionGeojson()
    {
        $dmas = Yii::$app->db->createCommand('SELECT st_asgeojson(geom) as geometry, madma as ten, id FROM "v2_4326_DMA" order by madma')->queryAll();

        // === TỐI ƯU 3: Sử dụng array_map để mã ngắn gọn và biểu cảm hơn ===
        $features = array_map(function ($dma) {
            $geometry = json_decode($dma['geometry'], true);
            return [
                'type' => 'Feature',
                'id' => $dma['id'],
                'properties' => [
                    'name' => $dma['ten'],
                ],
                'geometry' => $geometry, // Gán trực tiếp mảng đã decode
            ];
        }, $dmas);

        // === TỐI ƯU 4: Sử dụng Response của Yii để trả về JSON đúng chuẩn ===
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        // Trả về một mảng, Yii sẽ tự động encode thành JSON với header chính xác
        return [
            'type' => 'FeatureCollection',
            'features' => $features,
        ];
    }

    public function actionChitietdma($id)
    {
        // Action này đang trống, bạn có thể thêm logic truy vấn dữ liệu chi tiết cho DMA tại đây
        // Ví dụ: Lấy thông tin của DMA có id = $id
        // $dmaDetails = ...
        
        return $this->renderAjax('chitietdma', [
            'id' => $id,
            // 'dmaDetails' => $dmaDetails,
        ]);
    }
}