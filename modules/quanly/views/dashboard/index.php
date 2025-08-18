<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\widgets\echarts\EChartAsset;
use yii\bootstrap5\Modal;

EChartAsset::register($this);

// Đăng ký tài nguyên
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => \yii\web\View::POS_HEAD]);
?>

<style>
.dashboard-container {
    background-color: #f8f9fa;
    padding: 20px;
    min-height: 100vh;
}

.block-themed {
    transition: transform 0.3s, box-shadow 0.3s;
    border-radius: 10px;
    overflow: hidden;
}

.block-themed:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.chart-container {
    height: 300px;
    width: 100%;
}

#map {
    height: 400px;
    border-radius: 10px;
}

.chartStyle {
    position: relative;
    height: 600px;
    overflow: hidden;
}

#chart-none{
    font-family: Roboto !important;
}
</style>

<div class="dashboard-container">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-primary-dark d-flex align-items-center justify-content-center">
                    <i class="fas fa-gauge text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Điểm thu gom đã xác thực không cập nhật</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-primary"><?= $count['diemthugom1'] ?></div>
                    <a href="#"
                        class="btn btn-outline-primary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-success d-flex align-items-center justify-content-center">
                    <i class="fas fa-gauge text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Điểm thu gom đã xác thực có cập nhật</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-success"><?= $count['diemthugom2'] ?></div>
                    <a href="#"
                        class="btn btn-outline-success mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="block block-themed stat-card text-center">
                <div class="block-header bg-warning d-flex align-items-center justify-content-center">
                    <i class="fas fa-gauge text-white fa-2x me-2"></i>
                    <h3 class="block-title fs-4 fw-bold text-white">Điểm thu gom chưa xác thực</h3>
                </div>
                <div class="block-content p-4">
                    <div class="fs-1 fw-bold text-warning"><?= $count['diemthugom3'] ?></div>
                    <a href="#"
                        class="btn btn-outline-warning mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>
    </div>
    <div class="block block-themed">
        <div class="block-header">
            <h3>Thống kê biểu đồ</h3>
        </div>
        <div class="block-content">
            <div class="row">
                <div class="col-lg-6">
                    <?php if(isset($statistic['phuongxa']) && count($statistic['phuongxa']) > 0): ?>
                        <div id="piePhuongxa" class="chartStyle"></div>
                    <?php else: ?>
                    <div class="text-center" id="chart-none">
                        <h4>Không có dữ liệu !</h4>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <?php if(isset($statistic['loaidiemthugom']) && count($statistic['loaidiemthugom']) > 0): ?>
                        <div id="pieLoaidiemthugom" class="chartStyle"></div>
                    <?php else: ?>
                    <div class="text-center" id="chart-none">
                        <h4>Không có dữ liệu !</h4>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>  
</div>

<script type="module">
    $(document).ready(function () {
        <?php if(isset($statistic['phuongxa']) && count($statistic['phuongxa']) > 0): ?>
        initPieChart('piePhuongxa', 'Thống kê điểm thu gom theo phường', null, <?= json_encode($statistic['phuongxa'])?>,'Điểm thu gom')
        <?php endif; ?>
        <?php if(isset($statistic['loaidiemthugom']) && count($statistic['loaidiemthugom']) > 0): ?>
        initPieChart('pieLoaidiemthugom', 'Thống kê điểm thu gom theo loại', null, <?= json_encode($statistic['loaidiemthugom'])?>,'Điểm thu gom')
        <?php endif; ?>
    });

    function initPieChart(id, title, label, data, unit) {
        var chartDom = document.getElementById(id);
        var myChart = echarts.init(chartDom);
        var option;

        option = {
            title: {
                text: title,
                left: 'center',
                textStyle: {
                    fontFamily: 'Roboto'
                }
            },
            textStyle: {
                fontFamily: 'Roboto',
            },
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/> <b>{b}</b>:  {c} ({d}%)',
                textStyle: {
                    fontFamily: 'Roboto'
                }
            },
            label: {
            formatter: '{b}:{c}',
            position: 'inside',
            textStyle: {
                fontFamily: 'Roboto',
                fontSize: '8px'
            }
        },
            legend: {
                orient: 'vertical',
                left: 'left',
                top: 'bottom',
            },
            series: [
                {
                    name: unit,
                    type: 'pie',
                    radius: '50%',
                    data: data,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        option && myChart.setOption(option);
    }
</script>





