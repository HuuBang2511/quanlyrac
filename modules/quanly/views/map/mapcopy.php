<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\widgets\maps\LeafletMapAsset;
use app\widgets\maps\plugins\leafletlocate\LeafletLocateAsset;
use kartik\depdrop\DepDrop;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;

LeafletMapAsset::register($this);
\app\widgets\maps\plugins\leafletprint\PrintMapAsset::register($this);
\app\widgets\maps\plugins\markercluster\MarkerClusterAsset::register($this);
\app\widgets\maps\plugins\leaflet_measure\LeafletMeasureAsset::register($this);
LeafletLocateAsset::register($this);

$this->title = 'Bản đồ';
$this->params['hideHero'] = true;
?>

<style>
#map {
    width: 100%;
    height: 100vh;
}

#mapInfo {
    display: flex;
    height: 100vh;
}

#mapTong {
    width: 80%;
    transition: width 0.3s;
}

#map {
    position: relative;
    z-index: 0;
    height: 100%;
}

.leaflet-pane {
    z-index: 400;
}

.leaflet-overlay-pane {
    z-index: 650;
}

/* Tab styling */
#tabs {
    width: 20%;
    background: #fff;
    border-right: 1px solid #ccc;
    transition: transform 0.3s;
    position: relative;
}

.tab-buttons {
    display: flex;
    border-bottom: 1px solid #ccc;
}

.tab-button {
    flex: 1;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    background: #f0f0f0;
    border: none;
}

.tab-button.active {
    background: #fff;
    border-bottom: 2px solid #007bff;
}

.tab-content {
    display: none;
    padding: 10px;
    height: calc(100vh - 40px);
    overflow-y: auto;
}

.tab-content.active {
    display: block;
}

#layer-content h5,
#info-content h5 {
    margin-top: 20px;
}

#layer-control label {
    display: block;
    margin: 5px 0;
}

/* Mobile-specific back button */
#back-to-map-btn {
    display: none;
    width: 100%;
    padding: 10px;
    margin-top: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

@media screen and (max-width: 768px) {
    #mapInfo {
        flex-direction: column;
    }

    #tabs {
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        transform: translateX(-100%);
        z-index: 1001;
        height: 100vh;
    }

    #tabs.active {
        transform: translateX(0);
    }

    #mapTong {
        width: 100%;
        height: 100vh;
    }

    .tab-button {
        padding: 15px;
    }

    #back-to-map-btn {
        display: block;
    }

    #tabs {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
    }

    #tabs.active {
        transform: translateX(0);
    }

    #mapTong {
        width: 100%;
        transition: width 0.3s ease-in-out;
    }

    #layer-content,
    #info-content {
        max-height: 70vh;
        overflow-y: scroll;
    }
}

/* Toggle button styling */
#toggle-tab-btn {
    position: absolute;
    top: 10px;
    z-index: 1000;
    background: #fff;
    border: 1px solid #ccc;
    padding: 5px 10px;
    cursor: pointer;
}

div#tabs {
    display: flex;
    flex-direction: column;
}
</style>

<!-- Tải plugin Leaflet-LocateControl -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.css" />

<script src="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.js"></script>

<div id="mapInfo">
    <div id="tabs">
        <div class="">
            <a href="<?= Yii::$app->homeUrl ?>" target="_blank">
                <img src="http://hpngis.online/resources/images/logo_hpngis.png" alt="Logo"
                    style="width: 200px; height: auto; float: left; margin-right: 10px;">
            </a>
        </div>

        <div class="tab-buttons">
            <button class="tab-button active" onclick="openTab('layer')">Lớp dữ liệu</button>
            <button class="tab-button" onclick="openTab('info')">Thông tin chi tiết</button>
        </div>
        <div id="layer-content" class="tab-content active">
            <h5>Hiển thị lớp dữ liệu</h5>
            <div id="layer-control">
                <label><input type="checkbox" onchange="toggleLayer('wmsLoogerLayer')"> Data Logger</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsDonghoKhLayer')"> Đồng hồ khách
                    hàng</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsDonghoTongLayer')"> Hầm hồ
                    tổng</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsHamLayer')"> Hầm</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsOngCaiLayer')"> Ống cái</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsOngCaiDHLayer')"> Ống cái đồng
                    hồ</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsOngNganhLayer')"> Ống ngành</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsOngTruyenDanLayer')"> Hầm</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsTrambomLayer')"> Trạm bơm</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsTramCuuHoaLayer')"> Trạm cứu
                    hỏa</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsVanPhanPhoiLayer')"> Van phân
                    phối</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsSucoLayer')"> Sự cố điểm bể</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsDMA')"> DMA</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('highlightLayer')"> Highlight</label><br>
                <button id="back-to-map-btn" onclick="toggleTabVisibility()">Quay lại map</button>
            </div>

        </div>
        <div id="info-content" class="tab-content">
            <h5>Thông tin chi tiết</h5>
            <div id="feature-info" style="height: calc(100vh - 60px); overflow-y: auto;">
                <div id="feature-details">Chọn một đối tượng trên bản đồ để xem thông tin</div>
                <button id="back-to-map-btn" onclick="toggleTabVisibility()">Quay lại map</button>
            </div>

        </div>
    </div>

    <div id="mapTong">
        <div id="map" style="height: 100vh;"></div>
    </div>
</div>

<script>
var center = [10.805279349519678, 106.71851132905113];

// Create the map
var map = L.map('map', {
    defaultExtentControl: true
}).setView(center, 16);

var baseMaps = {
    "Bản đồ nền": L.tileLayer('http://103.9.77.141:8080/geoserver/gwc/service/wmts?' +
        'layer=giscapnuoc:basemap_capnuoc&style=&tilematrixset=EPSG:900913&Service=WMTS&Request=GetTile&Version=1.0.0' +
        '&Format=image/png&TileMatrix=EPSG:900913:{z}&TileCol={x}&TileRow={y}', {
            tileSize: 256,
            minZoom: 0,
            maxZoom: 22,
            attribution: '',
            pane: 'tilePane',
            noWrap: true,
            bounds: [
                [-85.0511, -180],
                [85.0511, 180]
            ],
            interactive: false
        }),

    "Bản đồ Google": L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map),

    "Ảnh vệ tinh": L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    })
};
//Thêm lớp L.Control.Locate
var locateControl = new L.Control.Locate({
    position: 'bottomleft',
    strings: {
        title: "Hiện vị trí",
        popup: "Bạn đang ở đây"
    },
    drawCircle: true,
    follow: true,
});
map.addControl(locateControl);

var measureControl = new L.Control.Measure({
    position: 'bottomright',
    primaryLengthUnit: 'meters',
    secondaryLengthUnit: undefined,
    primaryAreaUnit: 'sqmeters',
    decPoint: ',',
    thousandsSep: '.'
});
measureControl.addTo(map);

L.control.scale({
    imperial: false,
    maxWidth: 150
}).addTo(map);
var highlightLayer = L.featureGroup().addTo(map); // Lớp để highlight đối tượng được chọn

var myPane = map.createPane('myPane');
myPane.style.zIndex = 650;

var wmsLoogerLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_data_logger',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
});

var wmsDonghoKhLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_dongho_kh_gd',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsDonghoTongLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_dongho_tong_gd',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsHamLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_hamkythuat',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsOngCaiLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_ongcai',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsOngCaiDHLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_ongcai',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: "status = 1 AND tinhtrang = 'DH'",
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsOngNganhLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_ongnganh',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsOngTruyenDanLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:v2_4326_ONGTRUYENDAN',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsTrambomLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_trambom',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsTramCuuHoaLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_tramcuuhoa',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsVanPhanPhoiLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:gd_vanphanphoi',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsSucoLayer = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:v2_gd_suco',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsDMA = L.tileLayer.wms('http://103.9.77.141:8080/geoserver/giscapnuoc/wms', {
    layers: 'giscapnuoc:v2_4326_DMA',
    format: 'image/png',
    transparent: true,
    CQL_FILTER: 'status = 1',
    pane: 'myPane'
});

function toggleLayer(layerName) {
    var layerMap = {
        "wmsLoogerLayer": wmsLoogerLayer,
        "wmsDonghoKhLayer": wmsDonghoKhLayer,
        "wmsDonghoTongLayer": wmsDonghoTongLayer,
        "wmsHamLayer": wmsHamLayer,
        "wmsOngCaiLayer": wmsOngCaiLayer,
        "wmsOngCaiDHLayer": wmsOngCaiDHLayer,
        "wmsOngNganhLayer": wmsOngNganhLayer,
        "wmsOngTruyenDanLayer": wmsOngTruyenDanLayer,
        "wmsTrambomLayer": wmsTrambomLayer,
        "wmsTramCuuHoaLayer": wmsTramCuuHoaLayer,
        "wmsVanPhanPhoiLayer": wmsVanPhanPhoiLayer,
        "wmsSucoLayer": wmsSucoLayer,
        "wmsDMA": wmsDMA,
        "highlightLayer": highlightLayer
    };

    var checkbox = event.target;
    if (checkbox.checked) {
        layerMap[layerName].addTo(map);
    } else {
        map.removeLayer(layerMap[layerName]);
    }
}

function getFeatureInfoUrl(layer, latlng, url) {
    let size = map.getSize();
    let bbox = map.getBounds().toBBoxString();
    let point = map.latLngToContainerPoint(latlng, map.getZoom());

    const FeatureInfoUrl = url +
        `?SERVICE=WMS` +
        `&VERSION=1.1.1` +
        `&REQUEST=GetFeatureInfo` +
        `&LAYERS=${layer}` +
        `&QUERY_LAYERS=${layer}` +
        `&STYLES=` +
        `&BBOX=${bbox}` +
        `&FEATURE_COUNT=5` +
        `&HEIGHT=${size.y}` +
        `&WIDTH=${size.x}` +
        `&FORMAT=image/png` +
        `&INFO_FORMAT=application/json` +
        `&SRS=EPSG:4326` +
        `&X=${Math.floor(point.x)}` +
        `&Y=${Math.floor(point.y)}`;

    return FeatureInfoUrl;
}

map.on('click', function(e) {
    const layers = map._layers;
    const isMobile = window.innerWidth <= 768;
    let tabShown = false;

    for (const idx in layers) {
        const layer = layers[idx];
        if (layer.wmsParams && layer._url && layer.wmsParams.layers != "") {
            let url = getFeatureInfoUrl(layer.wmsParams.layers, e.latlng, layer._url);

            let layerName = layer.wmsParams.layers;
            layerName = layerName.split(':');
            layerName = String(layerName[1]);

            fetch(url)
                .then(function(res) {
                    return res.json()
                })
                .then(function(geojsonData) {
                    if (geojsonData.features && geojsonData.features.length > 0) {
                        var properties = geojsonData.features[0].properties;

                        if (layer.wmsParams.layers) {
                            switch (layerName) {
                                case 'gd_data_logger':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Chức năng:</strong></td><td>" +
                                        properties.chucnang + "</td></tr>" +
                                        "<tr><td><strong>Vị trí:</strong></td><td>" +
                                        properties.vitri + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrang + "</td></tr>" +
                                        "<tr><td><strong>Ghi chú:</strong></td><td>" +
                                        properties.ghichu + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_dongho_kh_gd':
                                    var feature = geojsonData.features[0];
                                    var properties = feature.properties;

                                    var featureId = '';
                                    if (feature.id && feature.id.includes('.')) {
                                        featureId = feature.id.split('.')[1];
                                    } else {
                                        console.warn('ID không hợp lệ hoặc không tồn tại:', feature.id);
                                    }

                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Danh bạ:</strong></td><td>" +
                                        properties.dbdonghonu + "</td></tr>" +
                                        "<tr><td><strong>Số thân đồng:</strong></td><td>" +
                                        properties.sothandong + "</td></tr>" +
                                        "<tr><td><strong>Tên KH:</strong></td><td>" +
                                        properties.tenkhachha + "</td></tr>" +
                                        "<tr><td><strong>ĐTDD:</strong></td><td>" +
                                        properties.dtdd + "</td></tr>" +
                                        "<tr><td><strong>Địa chỉ:</strong></td><td>" +
                                        properties.diachi + "</td></tr>" +
                                        "<tr><td><strong>Hiệu:</strong></td><td>" +
                                        properties.hieudongho + "</td></tr>" +
                                        "<tr><td><strong>Vị trí lắp đặt:</strong></td><td>" +
                                        properties.vitrilapda + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrang + "</td></tr>" +
                                        "<tr><td><strong>Bản Vẽ:</strong></td><td><p><a href=\"https://gisapi.giadinhwater.vn/gdw/banvehoancong/14091476272\" target=\"_blank\">Hoàn Công</a></p></td></tr>" +
                                        "<tr><td><strong>Xem chi tiết</strong></td><td><p><a href=\"http://hpngis.online/quanly/capnuocgd/gd-dongho-kh-gd/view?id=" +
                                        featureId +
                                        "\" target=\"_blank\">Thông tin chi tiết</a></p></td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;

                                case 'gd_dongho_tong_gd':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Hiệu:</strong></td><td>" +
                                        properties.hieudongho + "</td></tr>" +
                                        "<tr><td><strong>Ngày lắp đặt:</strong></td><td>" +
                                        properties.ngaylapdat + "</td></tr>" +
                                        "<tr><td><strong>Vị trí:</strong></td><td>" +
                                        properties.vitrilapda + "</td></tr>" +
                                        "<tr><td><strong>Đơn vị thi công:</strong></td><td>" +
                                        properties.donvithico + "</td></tr>" +
                                        "<tr><td><strong>Cỡ ĐH:</strong></td><td>" +
                                        properties.codongho + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrang + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_hamkythuat':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Tên hầm:</strong></td><td>" +
                                        properties.tenhamkyth + "</td></tr>" +
                                        "<tr><td><strong>Kích thước:</strong></td><td>" +
                                        properties.kichthuoch + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrangh + "</td></tr>" +
                                        "<tr><td><strong>Ghi chú:</strong></td><td>" +
                                        properties.ghichu + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_ongcai':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Cỡ ống:</strong></td><td>" +
                                        properties.coong + "</td></tr>" +
                                        "<tr><td><strong>Vật liệu:</strong></td><td>" +
                                        properties.vatlieu + "</td></tr>" +
                                        "<tr><td><strong>Tên công trình:</strong></td><td>" +
                                        properties.tencongtri + "</td></tr>" +
                                        "<tr><td><strong>Đơn vị thi công:</strong></td><td>" +
                                        properties.donvithico + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrang + "</td></tr>" +
                                        "<tr><td><strong>Ghi chú:</strong></td><td>" +
                                        properties.ghichu + "</td></tr>" +
                                        "<tr><td><strong>Năm lắp đặt:</strong></td><td>" +
                                        properties.namlapdat + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_ongnganh':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>ID ống:</strong></td><td>" +
                                        properties.idduongong + "</td></tr>" +
                                        "<tr><td><strong>Vật liệu:</strong></td><td>" +
                                        properties.vatlieu + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrang + "</td></tr>" +
                                        "<tr><td><strong>Năm lắp đặt:</strong></td><td>" +
                                        properties.namlapdat + "</td></tr>" +
                                        "<tr><td><strong>Cống:</strong></td><td>" +
                                        properties.coong + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'v2_4326_ONGTRUYENDAN':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Vật liệu:</strong></td><td>" +
                                        properties.vatlieu + "</td></tr>" +
                                        "<tr><td><strong>Cỡ ống:</strong></td><td>" +
                                        properties.coong + "</td></tr>" +
                                        "<tr><td><strong>Tên công trình:</strong></td><td>" +
                                        properties.tencongtri + "</td></tr>" +
                                        "<tr><td><strong>Năm lắp đặt:</strong></td><td>" +
                                        properties.namlapdat + "</td></tr>" +
                                        "<tr><td><strong>Đơn vị thiết kế:</strong></td><td>" +
                                        properties.donvithiet + "</td></tr>" +
                                        "<tr><td><strong>Đơn vị thi công:</strong></td><td>" +
                                        properties.donvithico + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_vanphanphoi':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>ID hầm:</strong></td><td>" +
                                        properties.idhamkythu + "</td></tr>" +
                                        "<tr><td><strong>cochiakhoa:</strong></td><td>" +
                                        properties.cochiakhoa + "</td></tr>" +
                                        "<tr><td><strong>Vật liệu:</strong></td><td>" +
                                        properties.vatlieu + "</td></tr>" +
                                        "<tr><td><strong>Mã DMA:</strong></td><td>" +
                                        properties.madma + "</td></tr>" +
                                        "<tr><td><strong>Vị trí:</strong></td><td>" +
                                        properties.vitrivan + "</td></tr>" +
                                        "<tr><td><strong>Tình trạng:</strong></td><td>" +
                                        properties.tinhtrang + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_trambom':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Tên:</strong></td><td>" +
                                        properties.tentram + "</td></tr>" +
                                        "<tr><td><strong>Số lượng bom:</strong></td><td>" +
                                        properties.soluongbom + "</td></tr>" +
                                        "<tr><td><strong>Đơn vị quản lý:</strong></td><td>" +
                                        properties.donviquanl + "</td></tr>" +
                                        "<tr><td><strong>Ghi chú:</strong></td><td>" +
                                        properties.ghichu + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'gd_tramcuuhoa':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>ID trạm:</strong></td><td>" +
                                        properties.idtruhong + "</td></tr>" +
                                        "<tr><td><strong>Kích cỡ:</strong></td><td>" +
                                        properties.kichco + "</td></tr>" +
                                        "<tr><td><strong>Kích thước:</strong></td><td>" +
                                        properties.kcmiengphu + "</td></tr>" +
                                        "<tr><td><strong>Loại trụ:</strong></td><td>" +
                                        properties.loaitruhon + "</td></tr>" +
                                        "<tr><td><strong>Hiệu:</strong></td><td>" +
                                        properties.hieu + "</td></tr>" +
                                        "<tr><td><strong>Tiêu chuẩn:</strong></td><td>" +
                                        properties.tieuchuan + "</td></tr>" +
                                        "<tr><td><strong>Mã DMA:</strong></td><td>" +
                                        properties.madma + "</td></tr>" +
                                        "<tr><td><strong>Vật liệu:</strong></td><td>" +
                                        properties.vatlieu + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;

                                case 'v2_gd_suco':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Mã sự cố:</strong></td><td>" +
                                        properties.masuco + "</td></tr>" +
                                        "<tr><td><strong>Số nhà:</strong></td><td>" +
                                        properties.sonha + "</td></tr>" +
                                        "<tr><td><strong>Đường:</strong></td><td>" +
                                        properties.duong + "</td></tr>" +
                                        "<tr><td><strong>Ngày phát hiện:</strong></td><td>" +
                                        properties.ngayphathien + "</td></tr>" +
                                        "<tr><td><strong>Người phát hiện:</strong></td><td>" +
                                        properties.nguoiphathien + "</td></tr>" +
                                        "<tr><td><strong>Ngày sửa chữa:</strong></td><td>" +
                                        properties.ngaysuachua + "</td></tr>" +
                                        "<tr><td><strong>Đơn vị:</strong></td><td>" +
                                        properties.donvisuachua + "</td></tr>" +
                                        "<tr><td><strong>Vị trí phát hiện:</strong></td><td>" +
                                        properties.vitriphathien + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                                case 'v2_4326_DMA':
                                    var popupContent = "<div class='popup-content'>" +
                                        "<table>" +
                                        "<tr><td><strong>Mã DMA:</strong></td><td>" +
                                        properties.madma + "</td></tr>" +
                                        "<tr><td><strong>Số van:</strong></td><td>" +
                                        properties.sovan + "</td></tr>" +
                                        "<tr><td><strong>Số trụ:</strong></td><td>" +
                                        properties.sotru + "</td></tr>" +
                                        "<tr><td><strong>Số đầu nối:</strong></td><td>" +
                                        properties.sodaunoi + "</td></tr>" +
                                        "</table>" +
                                        "</div>";
                                    break;
                            }
                        }
                        document.getElementById('feature-details').innerHTML = popupContent;
                        if (isMobile) {
                            openTab('info');
                            // Chỉ toggle tab nếu nó đang ẩn
                            const tabs = document.getElementById('tabs');
                            if (tabs.classList.contains('active')) {
                                // Nếu tab đã hiển thị, không cần toggle lại
                            } else {
                                toggleTabVisibility(); // Mở tab nếu đang ẩn
                                tabShown = true; // Đánh dấu tab đã được hiển thị
                            }
                        }
                    } else if (isMobile && tabShown) {
                        // Nếu không có dữ liệu và tab đã được mở, giữ nguyên
                    } else if (isMobile) {
                        // Nếu không có dữ liệu và tab chưa mở, không làm gì cả
                    }
                })
        }
    }
});

function openTab(tabName) {
    var tabs = document.getElementsByClassName('tab-content');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].classList.remove('active');
    }
    document.getElementById(tabName + '-content').classList.add('active');

    var buttons = document.getElementsByClassName('tab-button');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('active');
    }
    document.querySelector(`[onclick="openTab('${tabName}')"]`).classList.add('active');
}

function toggleTabVisibility() {
    var tabs = document.getElementById('tabs');
    var mapTong = document.getElementById('mapTong');
    var isActive = tabs.classList.contains('active');

    if (isActive) {
        tabs.classList.remove('active');
        mapTong.style.width = '100%';
    } else {
        tabs.classList.add('active');
        mapTong.style.width = '80%';
    }
}

// Add toggle button for tabs
var toggleTabBtn = L.control({
    position: 'topleft'
});
toggleTabBtn.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'leaflet-bar');
    div.innerHTML =
        '<button id="toggle-tab-btn" style="background: #fff; border: 1px solid #ccc; padding: 5px 10px; cursor: pointer;">☰</button>';
    return div;
};
toggleTabBtn.addTo(map);

document.getElementById('toggle-tab-btn').addEventListener('click', toggleTabVisibility);

// Tạo legend control
var legendControl = L.control({
    position: 'bottomright'
});

legendControl.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'legend');
    div.innerHTML += '<h4>Legend</h4>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_dongho_kh_gd"> Đồng hồ KH<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_dongho_tong_gd"> Đồng hồ tổng<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_trambom"> Trạm bơm<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_tramcuuhoa"> Trạm cứu hỏa<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_vanphanphoi"> Van phân phối<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_hamkythuat"> Hầm kỹ thuật<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_ongcai"> Ống cái<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_ongnganh"> Ống ngánh<br>';
    div.innerHTML +=
        '<img src="http://103.9.77.141:8080/geoserver/giadinh/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=giadinh:gd_suco"> Sự cố<br>';
    return div;
};

legendControl.addTo(map);

var legendToggleControl = L.control({
    position: 'bottomright'
});

legendToggleControl.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'legend-toggle');
    div.innerHTML = '<button id="legend-toggle-btn"> Chú thích</button>';
    return div;
};

legendToggleControl.addTo(map);

document.getElementById('legend-toggle-btn').addEventListener('click', function() {
    var legendDiv = document.querySelector('.legend');
    if (legendDiv.style.display === 'none' || legendDiv.style.display === '') {
        legendDiv.style.display = 'block';
    } else {
        legendDiv.style.display = 'none';
    }
});

var layerControl = L.control.layers(baseMaps).addTo(map);
</script>

<style>
.popup-content {
    font-size: 16px;
    max-width: 100%;
    overflow-x: auto;
}

.popup-table {
    width: 100%;
    border-collapse: collapse;
}

.popup-table th {
    background-color: #f2f2f2;
    padding: 8px;
    text-align: left;
}

.popup-table td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

.popup-table tr:nth-child(even) {
    background-color: #f2f2f2;
}

.popup-table th:hover {
    background-color: #ddd;
}

@media screen and (max-width: 600px) {
    .popup-content {
        width: 100%;
    }

    .popup-table {
        overflow-x: auto;
    }
}

.legend {
    background-color: white;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    display: none;
}

.legend img {
    width: 20px;
    height: auto;
    margin-right: 5px;
}
</style>