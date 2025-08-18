<?php

use kartik\form\ActiveForm;
use kartik\typeahead\Typeahead;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\helpers\Html;
use app\widgets\maps\LeafletMapAsset;
use app\widgets\crud\CrudAsset;
use app\widgets\gridview\GridView;
use app\widgets\export\ExportMenu;
use yii\bootstrap5\BootstrapAsset;
use yii\bootstrap4\Modal;

LeafletMapAsset::register($this);
BootstrapAsset::register($this);
CrudAsset::register($this);
?>

<div class="block block-themed">
    <div class="block-header">
        <h3 class="block-title">Thống kê theo không gian</h3>
    </div>
    <div class="block-content">
        <?php $form = ActiveForm::begin() ?>
        <div class="row">
            <div class="col-lg-3">
                <?= $form->field($model, 'geo_x')->input('text', ['id' => 'geo_x_input','onchange' => 'updateMarkerPosition()'])->label('Kinh độ (Longitude)') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'geo_y')->input('text', ['id' => 'geo_y_input','onchange' => 'updateMarkerPosition()'])->label('Vĩ độ (Latitude)') ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'bankinh')->input('number') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 mb-3">
                <?= Html::submitButton('Kiểm tra', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

<div class="block block-themed">
    <div class="block-header">
    </div>
    <div class="block-content block-content-full">
        <div class="row">
            <div class="col-lg-12">
                <div id="map" style="height:80vh;width:100%"></div>
            </div>
        </div>
    </div>
</div>


<script>
var map = L.map('map').setView([10.773609646913958, 106.7091751098633], 13);


var marker = new L.marker([<?= ($model->geo_y != null) ? $model->geo_y : 10.773609646913958 ?>,
    <?= ($model->geo_x != null) ? $model->geo_x : 106.7091751098633 ?>
], {
    draggable: 'true',
    icon: new L.icon({
        iconUrl: '<?= Yii::$app->homeUrl ?>resources/images/icons8-circle-64.png',
        iconSize: [30, 30],
        iconAnchor: [0, 15],
        popupAnchor: [15, 0],
    })
}).addTo(map);

marker.on('dragend', function(event) {
    var marker = event.target;
    var position = marker.getLatLng();
    marker.setLatLng(new L.LatLng(position.lat, position.lng), {
        draggable: 'true'
    });
    map.panTo(new L.LatLng(position.lat, position.lng))
    $('#geo_y_input').val(position.lat);
    $('#geo_x_input').val(position.lng);
});

function updateMarkerPosition() {
    var geo_x = $('#geo_x_input').val();
    var geo_y = $('#geo_y_input').val();
    marker.setLatLng(new L.LatLng(geo_y, geo_x), {
        draggable: 'true'
    });
    map.panTo(new L.LatLng(geo_y, geo_x))
}

<?php if($model->geo_x != null):?>

var circle = L.circle([<?= $model->geo_y?>, <?= $model->geo_x?>], {
    color: 'green',
    fillColor: 'rgba(117,239,71,0.44)',
    fillOpacity: 0.5,
    radius: <?= $model->bankinh?>
}).addTo(map);
map.fitBounds(circle.getBounds());

function onEachFeature(feature, layer) {
    // does this feature have a property named popupContent?
    if (feature.properties && feature.properties.popupContent) {
        layer.bindPopup(feature.properties.popupContent);
    }
}

L.geoJSON(<?= $geojson?>, {
    pointToLayer: function(feature, latlng) {
        return L.marker(latlng, {
            icon: new L.icon({
                iconUrl: '<?= Yii::$app->homeUrl ?>resources/images/icons8-waste-100.png',
                iconSize: [35, 35],
                iconAnchor: [0, 15],
                popupAnchor: [15, 0],
            })
        });
    },
    onEachFeature: onEachFeature

}).addTo(map);

<?php endif;?>
L.tileLayer('https://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', { maxZoom: 22, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'] }).addTo(map);

window.addEventListener('resize', function() {
    map.invalidateSize();
});
</script>

<?php if($dataProvider != null): ?>
<div class="diemdanh-index">
    <div id="table-responsive">
        <?php $fullExportMenu = ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => require(__DIR__.'/_columns.php'),
            'target' => ExportMenu::TARGET_BLANK,
            'pjaxContainerId' => 'kv-pjax-container',
            'exportContainer' => [
                'class' => 'btn-group mr-2'
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_EXCEL => false,
                ExportMenu::FORMAT_PDF => false,
            ],
//            'columnSelectorOptions' => ['class' => 'btn btn-outline-info','label' => 'Chọn cột'],
            'dropdownOptions' => [
                'label' => 'Tải xuống',
                'itemsBefore' => [
                    '<div class="dropdown-header">Xuất tất cả dữ liệu</div>',
                ],
            ],
        ]) ?>
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' =>require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                $fullExportMenu,
                
            ],
            'striped' => true,
            'condensed' => true,
//            'responsive' => false,
            'responsiveWrap' => false,
            'panelPrefix' => 'block ',
            'toolbarContainerOptions' => ['class' => 'float-right'],
            'summaryOptions' => ['class' => 'float-right'],
            'panel' => [
                'type' => 'block-themed',
                'headingOptions' => ['class' => 'block-header'] ,
                'summaryOptions' => ['class' => 'block-options'],
                'titleOptions' => ['class' => 'block-title'] ,
                'heading' => '<i class="fa fa-list"></i> ' .  $this->title ,
            ],
            'tableOptions' => ['class' => 'table table-striped'],
            'layout' => "{items}\n{pager}",
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
<?php endif; ?>


