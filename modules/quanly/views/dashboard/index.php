<?php
use app\modules\assets\DashboardAsset;
use yii\helpers\Json;

DashboardAsset::register($this);
$this->title = 'Bảng điều khiển chuyên nghiệp';

// CHUẨN BỊ DỮ LIỆU MẪU (BẠN SẼ THAY BẰNG DỮ LIỆU THẬT TỪ CONTROLLER)
$totalPoints = $count['diemthugom1'] + $count['diemthugom2'] + $count['diemthugom3'];
$recentActivities = [
    ['name' => 'Nguyễn Văn A', 'ward' => 'Phường Bến Nghé', 'status' => 'Đã xác thực', 'type' => 'success'],
    ['name' => 'Trần Thị B', 'ward' => 'Phường Đa Kao', 'status' => 'Chưa xác thực', 'type' => 'warning'],
    ['name' => 'Lê Văn C', 'ward' => 'Phường Tân Định', 'status' => 'Đã xác thực', 'type' => 'success'],
    ['name' => 'Phạm Thị D', 'ward' => 'Phường Cầu Ông Lãnh', 'status' => 'Đã xác thực', 'type' => 'success'],
];
// Dữ liệu cho các biểu đồ sparkline mini
$sparklineData = [
    'series1' => [5, 8, 6, 10, 7, 12, 9],
    'series2' => [15, 12, 18, 14, 20, 17, 22],
    'series3' => [4, 3, 5, 2, 6, 4, 5],
    'series4' => [30, 32, 28, 35, 33, 38, 36]
];
?>

<div class="dashboard-pro container-fluid">
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="glass-card kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Đã xác thực (K)</p>
                        <h2 class="kpi-number"><?= $count['diemthugom1'] ?></h2>
                    </div>
                    <div class="kpi-icon" style="background-color: var(--primary-color);"><i class='bx bxs-file-check'></i></div>
                </div>
                <div id="sparkline1" class="kpi-sparkline"></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="glass-card kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Đã xác thực (C)</p>
                        <h2 class="kpi-number text-success"><?= $count['diemthugom2'] ?></h2>
                    </div>
                    <div class="kpi-icon bg-success"><i class='bx bxs-file-plus'></i></div>
                </div>
                <div id="sparkline2" class="kpi-sparkline"></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="glass-card kpi-card">
                 <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Chưa xác thực</p>
                        <h2 class="kpi-number text-warning"><?= $count['diemthugom3'] ?></h2>
                    </div>
                    <div class="kpi-icon bg-warning"><i class='bx bxs-file-import'></i></div>
                </div>
                <div id="sparkline3" class="kpi-sparkline"></div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="glass-card kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="kpi-label">Tổng số điểm</p>
                        <h2 class="kpi-number text-danger"><?= $totalPoints ?></h2>
                    </div>
                    <div class="kpi-icon bg-danger"><i class='bx bxs-collection'></i></div>
                </div>
                <div id="sparkline4" class="kpi-sparkline"></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="glass-card">
                <h5 class="card-title mb-3">Hoạt động xác thực 7 ngày qua</h5>
                <div id="chart7Days" class="chart-container"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="glass-card">
                <h5 class="card-title mb-3">Tỷ lệ các loại điểm</h5>
                <div id="chartLoaiDiem" class="chart-container"></div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card">
                <h5 class="card-title mb-3">Thống kê theo Phường/Xã</h5>
                <div id="chartPhuongXa" class="chart-container"></div>
            </div>
        </div>
    </div>
</div>

<?php
// Truyền dữ liệu từ PHP vào biến JavaScript để file dashboard-pro.js sử dụng
$jsData = [
    'sparkline' => $sparklineData,
    'charts' => $statistic
];

$this->registerJs(
    'const dashboardProData = ' . Json::encode($jsData) . ';',
    \yii\web\View::POS_HEAD
);
?>