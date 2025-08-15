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
    position: relative;
    z-index: 1000;
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
    transition: transform 0.3s ease-in-out;
    position: relative;
    transform: translateX(0);
}

#tabs.toggling {
    pointer-events: none;
    transition: transform 0.3s ease-in-out;
}

#tabs.active {
    transform: translateX(0); /* Fully visible */
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

#layer-content h5, #info-content h5 {
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

.tabs-header{
    display:flex;
    justify-content: space-between;
}

#back-to-map-mobile-btn{
    display:none;
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
        transform: translateX(-100%); /* Hidden by default on mobile */
        z-index: 1001;
        height: 100vh;
        background: #fff;
    }

    #tabs.active {
        transform: translateX(0); /* Visible when active */
    }

    #mapTong {
        width: 100%;
        transition: width 0.3s;
    }

    #mapTong.toggling {
        transition: width 0.3s;
    }

    .tab-button {
        padding: 15px;
    }

    #back-to-map-btn {
        display: block;
    }

    #layer-content, #info-content {
        max-height: 70vh;
        overflow-y: scroll;
    }

    #back-to-map-mobile-btn{
        display:block;
        margin-top: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        height: 30px;
    }

    .leaflet-bottom.leaflet-right {
        margin-bottom: 80px;
    }

    .leaflet-bottom.leaflet-left{
        margin-bottom: 100px;
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

img.leaflet-marker-icon.leaflet-zoom-animated.leaflet-interactive {
    z-index: 800 !important;
}
</style>

<!-- Tải plugin Leaflet-LocateControl -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.css" />
<script src="https://unpkg.com/leaflet.locatecontrol@0.79.0/dist/L.Control.Locate.min.js"></script>

<div id="mapInfo">
    <div id="tabs">
        <div class="tabs-header">
            <a href="<?= Yii::$app->homeUrl ?>" target="_blank">
                <img src="https://gis.nongdanviet.net/resources/images/logo_map.jpg" alt="Logo" style="width: 200px; height: auto; float: left; margin-right: 10px;">
            </a>
            <button id="back-to-map-mobile-btn" onclick="toggleTabVisibility()">X</button>
        </div>
        
        <div class="tab-buttons">
            <button class="tab-button active" onclick="openTab('layer')">Lớp dữ liệu</button>
            <button class="tab-button" onclick="openTab('info')">Thông tin chi tiết</button>
        </div>
        <div id="layer-content" class="tab-content active">
            <h5>Hiển thị lớp dữ liệu</h5>
            <div id="layer-control">
                <label><input type="checkbox" checked onchange="toggleLayer('wmsPhuongxaLayer')"> Phường xã</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsVungbien')"> Vùng biển</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsTrusotinhLayer')"> Trụ sở tỉnh</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsTrusophuongxaLayer')"> Trụ sở phường xã</label><br>
                <label><input type="checkbox" checked onchange="toggleLayer('wmsDebienLayer')"> Đê biển</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsTongiaoLayer')"> Tôn giáo</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsToanhaLayer')"> Tòa nhà</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsThuyheLayer')"> Thủy hệ</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsSanbayLayer')"> Sân bay</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsRungLayer')"> Rừng</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsPolvhxhLayer')"> Vùng kinh tế văn hóa xã hội</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsPoivhxhLayer')"> Điểm kinh tế văn hóa xã hội</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsDentinhieuLayer')"> Đèn tín hiệu</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsGiaothongLayer')"> Giao thông</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsBenxeLayer')"> Bến xe</label><br>
                <label><input type="checkbox" onchange="toggleLayer('wmsDaoLayer')"> Đảo</label><br>
                
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
var center = [9.15848, 105.21332];

// Create the map
var map = L.map('map', {
    defaultExtentControl: true
}).setView(center, 11);

// Tạo pane cho highlightLayer với zIndex cao hơn
map.createPane('highlightPane');
map.getPane('highlightPane').style.zIndex = 700; // Tăng lên 700 để ưu tiên hơn

var baseMaps = {
    "Bản đồ Google": L.tileLayer('http://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(map),

    "Ảnh vệ tinh": L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    })
};

// Thêm lớp L.Control.Locate
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

// Tạo highlightLayer với pane riêng
var highlightLayer = L.featureGroup([], { pane: 'highlightPane' }).addTo(map);

var myPane = map.createPane('myPane');
myPane.style.zIndex = 650;

var wmsPhuongxaLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_px',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsVungbien = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_vungbien',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsTrusotinhLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_truso_tinh',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsTrusophuongxaLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_truso_px',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsDebienLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_debien',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
}).addTo(map);

var wmsTongiaoLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_tongiao',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsToanhaLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_toanha',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsThuyheLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_thuyhe',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsSanbayLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_sanbay',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsRungLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_rung',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsPolvhxhLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_poi_polygon',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsPoivhxhLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_poi_point',
    format: 'image/png',
    transparent: true,
    maxZoom: 22,
    pane: 'myPane'
});

var wmsDentinhieuLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_dentinhieu',
    format: 'image/png',
    transparent: true,
    pane: 'myPane'
});

var wmsGiaothongLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_gt',
    format: 'image/png',
    transparent: true,
    pane: 'myPane'
});

var wmsBenxeLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_benxe',
    format: 'image/png',
    transparent: true,
    pane: 'myPane'
});

var wmsDaoLayer = L.tileLayer.wms('https://nongdanviet.net/geoserver/gis_camau/wms', {
    layers: 'gis_camau:camau_dao',
    format: 'image/png',
    transparent: true,
    pane: 'myPane'
});

function toggleLayer(layerName) {
    var layerMap = {
        "wmsPhuongxaLayer": wmsPhuongxaLayer,
        "wmsVungbien": wmsVungbien,
        "wmsTrusotinhLayer": wmsTrusotinhLayer,
        "wmsTrusophuongxaLayer": wmsTrusophuongxaLayer,
        "wmsDebienLayer": wmsDebienLayer,
        "wmsTongiaoLayer": wmsTongiaoLayer,
        "wmsToanhaLayer": wmsToanhaLayer,
        "wmsThuyheLayer": wmsThuyheLayer,
        "wmsSanbayLayer": wmsSanbayLayer,
        "wmsRungLayer": wmsRungLayer,
        "wmsPolvhxhLayer": wmsPolvhxhLayer,
        "wmsPoivhxhLayer": wmsPoivhxhLayer,
        "wmsDentinhieuLayer": wmsDentinhieuLayer,
        "wmsGiaothongLayer": wmsGiaothongLayer,
        "wmsBenxeLayer": wmsBenxeLayer,
        "wmsDaoLayer": wmsDaoLayer,
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
        `&FEATURE_COUNT=1` +
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
    const isMobile = window.innerWidth <= 768;
    let tabShown = false;

    // Danh sách các lớp WMS với zIndex
    const wmsLayers = [
        { name: 'camau_px', layer: wmsPhuongxaLayer, zIndex: 450 },
        { name: 'camau_vungbien', layer: wmsVungbien, zIndex: 450 },
        { name: 'camau_truso_tinh', layer: wmsTrusotinhLayer, zIndex: 650 },
        { name: 'camau_truso_px', layer: wmsTrusophuongxaLayer, zIndex: 650 },
        { name: 'camau_debien', layer: wmsDebienLayer, zIndex: 450 },
        { name: 'camau_tongiao', layer: wmsTongiaoLayer, zIndex: 650 },
        { name: 'camau_toanha', layer: wmsToanhaLayer, zIndex: 550 },
        { name: 'camau_thuyhe', layer: wmsThuyheLayer, zIndex: 550 },
        { name: 'camau_sanbay', layer: wmsSanbayLayer, zIndex: 550 },
        { name: 'camau_rung', layer: wmsRungLayer, zIndex: 550 },
        { name: 'camau_poi_polygon', layer: wmsPolvhxhLayer, zIndex: 650 },
        { name: 'camau_poi_point', layer: wmsPoivhxhLayer, zIndex: 650 },
        { name: 'camau_dentinhieu', layer: wmsDentinhieuLayer, zIndex: 650 },
        { name: 'camau_gt', layer: wmsGiaothongLayer, zIndex: 650 },
        { name: 'camau_benxe', layer: wmsBenxeLayer, zIndex: 650 },
        { name: 'camau_dao', layer: wmsDaoLayer, zIndex: 650 }
    ];

    // Lọc các lớp WMS đang hiển thị và sắp xếp theo zIndex
    const visibleLayers = wmsLayers
        .filter(item => map.hasLayer(item.layer))
        .sort((a, b) => (b.zIndex || 0) - (a.zIndex || 0));

    if (visibleLayers.length === 0) {
        document.getElementById('feature-details').innerHTML = 'Chọn một đối tượng trên bản đồ để xem thông tin';
        highlightLayer.clearLayers();
        return;
    }

    // Hàm xử lý GetFeatureInfo tuần tự để ưu tiên lớp trên cùng
    async function fetchFeatureInfoSequentially(layers) {
        for (const item of layers) {
            const url = getFeatureInfoUrl(item.name, e.latlng, item.layer._url);
            try {
                const res = await fetch(url);
                const data = await res.json();
                if (data.features && data.features.length > 0) {
                    return { layerName: item.name, data };
                }
            } catch (error) {
                console.error(`Lỗi khi lấy thông tin từ lớp ${item.name}:`, error);
            }
        }
        return null; // Không tìm thấy dữ liệu
    }

    fetchFeatureInfoSequentially(visibleLayers).then(result => {
        if (!result) {
            document.getElementById('feature-details').innerHTML = 'Không tìm thấy thông tin tại vị trí này';
            highlightLayer.clearLayers();
            return;
        }

        const { layerName, data } = result;
        const properties = data.features[0].properties;
        let popupContent = "<div class='popup-content'><table>";

        // Xác định nội dung popup dựa trên lớp
        switch (layerName) {
            case 'camau_vungbien':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Vùng biển</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>Shape_Length:</strong></td><td>${properties.Shape_Length || 'Không có'}</td></tr>
                    <tr><td><strong>Shape_Area:</strong></td><td>${properties.Shape_Area || 'Không có'}</td></tr>`;
                break;
            case 'camau_truso_tinh':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Trụ sở tỉnh</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten:</strong></td><td>${properties.ten || 'Không có'}</td></tr>`;
                break;
            case 'camau_truso_px':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Trụ sở phường xã</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten:</strong></td><td>${properties.ten || 'Không có'}</td></tr>`;
                break;
            case 'camau_debien':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Đê biển</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten:</strong></td><td>${properties.ten || 'Không có'}</td></tr>`;
                break;
            case 'camau_tongiao':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Tôn giáp</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>fclass:</strong></td><td>${properties.fclass || 'Không có'}</td></tr>
                    <tr><td><strong>name:</strong></td><td>${properties.name || 'Không có'}</td></tr>`;
                break;
            case 'camau_sanbay':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Sân bay</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>fclass:</strong></td><td>${properties.fclass || 'Không có'}</td></tr>
                    <tr><td><strong>name:</strong></td><td>${properties.name || 'Không có'}</td></tr>`;
                break;
            case 'camau_poi_polygon':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Vùng KTVHXH</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>fclass:</strong></td><td>${properties.fclass || 'Không có'}</td></tr>
                    <tr><td><strong>name:</strong></td><td>${properties.name || 'Không có'}</td></tr>`;
                break;
            case 'camau_poi_point':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Điểm KTVHXH</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>fclass:</strong></td><td>${properties.fclass || 'Không có'}</td></tr>
                    <tr><td><strong>name:</strong></td><td>${properties.name || 'Không có'}</td></tr>`;
                break;
            case 'camau_dentinhieu':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Đèn tín hiệu</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>fclass:</strong></td><td>${properties.fclass || 'Không có'}</td></tr>
                    <tr><td><strong>name:</strong></td><td>${properties.name || 'Không có'}</td></tr>`;
                break;
            case 'camau_benxe':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Bến xe</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>fclass:</strong></td><td>${properties.fclass || 'Không có'}</td></tr>
                    <tr><td><strong>name:</strong></td><td>${properties.name || 'Không có'}</td></tr>`;
                break;
            case 'camau_toanha':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Tòa nhà</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>area_in_me:</strong></td><td>${properties.area_in_me || 'Không có'}</td></tr>
                    <tr><td><strong>confidence:</strong></td><td>${properties.confidence || 'Không có'}</td></tr>`;
                break;
            case 'camau_thuyhe':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Thủy hệ</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten_kenh_rach:</strong></td><td>${properties.ten_kenh_rach || 'Không có'}</td></tr>
                    <tr><td><strong>chieu_dai:</strong></td><td>${properties.chieu_dai || 'Không có'}</td></tr>
                    <tr><td><strong>chieu_rong:</strong></td><td>${properties.chieu_rong || 'Không có'}</td></tr>
                    <tr><td><strong>ti_le:</strong></td><td>${properties.ti_le || 'Không có'}</td></tr>
                    <tr><td><strong>Shape_Length:</strong></td><td>${properties.Shape_Length || 'Không có'}</td></tr>`;
                break;
            case 'camau_gt':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Giao thông</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten_duong:</strong></td><td>${properties.ten_duong || 'Không có'}</td></tr>`;
                break;
            case 'camau_rung':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Rừng</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>dien_tich:</strong></td><td>${properties.s || 'Không có'}</td></tr>`;
                break;
            case 'camau_px':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Phường xã</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten_dvhc:</strong></td><td>${properties.ten_dvhc || 'Không có'}</td></tr>`;
                break;
            case 'camau_dao':
                popupContent += `
                    <tr><td><strong>Tên lớp dữ liệu:</strong></td><td>Đảo</td></tr>
                    <tr><td><hr></</tr>
                    <tr><td><strong>ten_vung:</strong></td><td>${properties.ten_vung || 'Không có'}</td></tr>
                    <tr><td><strong>dien_tich:</strong></td><td>${properties.dien_tich || 'Không có'}</td></tr>
                    <tr><td><strong>tinh_thanh:</strong></td><td>${properties.tinh_thanh || 'Không có'}</td></tr>`;
                break;
            default:
                popupContent += `<tr><td colspan='2'>Không có thông tin chi tiết</td></tr>`;
        }

        popupContent += "</table></div>";
        document.getElementById('feature-details').innerHTML = popupContent;

        // Đánh dấu đối tượng được click với pane riêng và style nổi bật
        highlightLayer.clearLayers();
        const highlightedFeature = L.geoJSON(data.features[0], {
            pane: 'highlightPane', // Sử dụng pane có zIndex cao
            style: {
                color: '#ff0000',
                weight: 5, // Tăng độ dày viền để nổi bật
                opacity: 1,
                fillOpacity: 0.3, // Tăng độ mờ fill để dễ thấy hơn
                dashArray: '5, 5' // Thêm hiệu ứng gạch ngang
            }
        });
        highlightLayer.addLayer(highlightedFeature);

        // Hiển thị tab thông tin trên di động
        if (isMobile) {
            openTab('info');
            const tabs = document.getElementById('tabs');
            if (!tabs.classList.contains('active')) {
                toggleTabVisibility();
                tabShown = true;
            }
        }
    }).catch(error => {
        console.error('Lỗi khi lấy thông tin đối tượng:', error);
        document.getElementById('feature-details').innerHTML = 'Lỗi khi tải thông tin';
        highlightLayer.clearLayers();
    });
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

    // Prevent multiple toggles if already in progress
    if (tabs.classList.contains('toggling')) return;

    tabs.classList.add('toggling');
    if (isActive) {
        tabs.classList.remove('active');
        mapTong.style.width = '100%';
    } else {
        tabs.classList.add('active');
        mapTong.style.width = '80%';
    }

    // Ensure the transition completes before removing the toggling class
    setTimeout(() => {
        tabs.classList.remove('toggling');
    }, 300); // Match the CSS transition duration
}

// add log out
var logoutBtn = L.control({ position: 'topright' });
logoutBtn.onAdd = function(map) {
    var div = L.DomUtil.create('div');
    div.innerHTML = '<a style="padding: 3px 5px; background-color:#fff" href="<?= Yii::$app->urlManager->createUrl(["user/auth/logout"]) ?>">Đăng xuất</a>';
    return div;
};
logoutBtn.addTo(map);

// Add toggle button for tabs
var toggleTabBtn = L.control({ position: 'topleft' });
toggleTabBtn.onAdd = function(map) {
    var div = L.DomUtil.create('div', 'leaflet-bar');
    div.innerHTML = '<button id="toggle-tab-btn" style="background: #fff; border: 1px solid #ccc; padding: 5px 10px; cursor: pointer;">☰</button>';
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
    // div.innerHTML +=
    //     '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_px"> Phường xã<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_vungbien"> Vùng biển<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_truso_tinh"> Trụ sở tỉnh<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_truso_px"> Trụ sở phường xã<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_debien"> Đê biển<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_tongiao"> Tôn giáo<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_toanha"> Tòa nhà<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_thuyhe"> Thủy hệ<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_sanbay"> Sân bay<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_rung"> Rừng<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_poi_polygon"> KTVHXH<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_poi_point"> KTVHXH<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_gt"> Giao thông<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_benxe"> Bến xe<br>';
    div.innerHTML +=
        '<img src="https://nongdanviet.net/geoserver/gis_camau/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=gis_camau:camau_dao"> Đảo<br>';
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
