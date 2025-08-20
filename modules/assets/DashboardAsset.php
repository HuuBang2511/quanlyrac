<?php

namespace app\modules\assets;

use yii\web\AssetBundle;

class DashboardAsset extends AssetBundle
{
    public $sourcePath = '@app/modules/assets';

    public $css = [
        'css/dashboard-pro.css', // File CSS mới
        'https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css', // Bộ icon mới
    ];
    
    public $js = [
        'js/dashboard-pro.js', // File JS mới
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'app\widgets\echarts\EChartAsset',
    ];
}