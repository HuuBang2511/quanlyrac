<?php

use yii\helpers\Url;

// Đăng ký các asset cần thiết.
app\widgets\maps\LeafletMapAsset::register($this);
app\widgets\maps\plugins\leafletprint\PrintMapAsset::register($this);
app\widgets\maps\plugins\markercluster\MarkerClusterAsset::register($this);
app\widgets\maps\plugins\leaflet_measure\LeafletMeasureAsset::register($this);
app\widgets\maps\plugins\leafletlocate\LeafletLocateAsset::register($this);


$this->title = 'Bản đồ GIS';
$this->params['hideHero'] = true;
?>
<!-- 
    QUAN TRỌNG: Thẻ Meta Viewport để đảm bảo hiển thị đúng trên thiết bị di động.
    Nếu bạn có một file layout chính (vd: main.php), hãy đảm bảo thẻ này có trong <head>.
-->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

<!-- Import Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-color: #007bff;
        --light-gray: #f1f5f9;
        --border-color: #e2e8f0;
        --background-color: #fff;
        --text-color: #334155;
        --text-light-color: #64748b;
        --shadow-color: rgba(0, 0, 0, 0.1);
        --transition-speed: 0.3s;
        --font-family: 'Inter', sans-serif;
        /* Biến chiều cao động, sẽ được set bằng JS */
        --app-height: 100vh;
    }

    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        overflow: hidden;
        font-family: var(--font-family);
        color: var(--text-color);
    }

    #mapInfo {
        display: flex;
        /* Sử dụng biến --app-height thay vì 100vh */
        height: var(--app-height);
    }

    #mapTong {
        flex-grow: 1;
        height: 100%;
        transition: width var(--transition-speed);
        position: relative;
    }

    #map {
        height: 100%;
        width: 100%;
        background-color: var(--light-gray);
    }

    /* --- Side Panel (Tabs) --- */
    #tabs {
        width: 25%;
        max-width: 350px;
        min-width: 300px;
        background: var(--background-color);
        border-right: 1px solid var(--border-color);
        transition: transform var(--transition-speed) ease-in-out, min-width var(--transition-speed) ease-in-out, width var(--transition-speed) ease-in-out;
        display: flex;
        flex-direction: column;
        transform: translateX(0);
        box-shadow: 2px 0 10px var(--shadow-color);
    }

    #tabs.hidden {
        min-width: 0;
        width: 0;
        transform: translateX(0);
        /* Desktop uses width/min-width to hide */
        border-right: none;
    }

    .tabs-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
        /* Ngăn header bị co lại */
    }

    .tabs-header a {
        flex-grow: 1;
        margin-right: 15px;
    }

    .tabs-header img {
        width: 100%;
        height: auto;
        display: block;
    }

    .tab-buttons {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        flex-shrink: 0;
    }

    .tab-button {
        flex: 1;
        padding: 12px;
        text-align: center;
        cursor: pointer;
        background: var(--background-color);
        border: none;
        font-weight: 500;
        font-size: 14px; /* Thêm font-size để đồng bộ */
        color: var(--text-light-color);
        border-bottom: 3px solid transparent;
        transition: color 0.2s, border-color 0.2s;
    }

    .tab-button:hover {
        color: var(--primary-color);
    }

    .tab-button.active {
        color: var(--text-color);
        border-bottom: 3px solid var(--primary-color);
    }

    .tab-content {
        display: none;
        padding: 15px;
        overflow-y: auto;
        flex-grow: 1;
        /* Cải thiện trải nghiệm cuộn trên iOS */
        -webkit-overflow-scrolling: touch;
    }

    .tab-content.active {
        display: block;
    }

    #layer-control .layer-item {
        display: flex;
        align-items: center;
        margin: 12px 0;
        cursor: pointer;
    }

    #layer-control .layer-item input {
        margin-right: 10px;
        width: 16px;
        height: 16px;
    }

    #feature-details {
        word-wrap: break-word;
    }

    .popup-content table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .popup-content th,
    .popup-content td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .popup-content th {
        font-weight: 500;
        width: 40%;
        color: var(--text-light-color);
    }

    .popup-content h4 {
        margin-top: 0;
        color: var(--primary-color);
    }

    .legend {
        background-color: var(--background-color);
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px var(--shadow-color);
        display: none;
        max-height: 40vh;
        overflow-y: auto;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .legend img {
        width: 20px;
        height: 20px;
        margin-right: 10px;
    }
    
    /* --- Filter Form Styles --- */
    .filter-group {
        margin-bottom: 15px;
    }

    .filter-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .filter-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background-color: #fff;
        font-family: var(--font-family);
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .filter-actions button {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .filter-actions button[type="submit"] {
        background-color: var(--primary-color);
        color: white;
    }
    .filter-actions button[type="submit"]:hover {
        background-color: #0056b3;
    }

    .filter-actions button[type="button"] {
        background-color: var(--light-gray);
        color: var(--text-color);
        border: 1px solid var(--border-color);
    }
    .filter-actions button[type="button"]:hover {
        background-color: #e2e8f0;
    }


    #back-to-map-mobile-btn {
        display: none;
    }

    @media screen and (max-width: 768px) {
        #tabs {
            width: 100%;
            max-width: none;
            position: absolute;
            top: 0;
            left: 0;
            height: var(--app-height);
            z-index: 2000;
            transform: translateX(-100%);
            border-right: none;
        }

        #tabs.active {
            transform: translateX(0);
        }

        #mapTong {
            width: 100% !important;
        }

        #back-to-map-mobile-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            padding: 5px;
        }

        .leaflet-bottom.leaflet-right,
        .leaflet-bottom.leaflet-left {
            margin-bottom: 40px;
        }
    }

    #toggle-tab-btn {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 1000;
        background: var(--background-color);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        width: 40px;
        height: 40px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px var(--shadow-color);
    }

    .leaflet-bar {
        border-radius: 8px !important;
        box-shadow: 0 2px 5px var(--shadow-color) !important;
    }

    .leaflet-bar a,
    .leaflet-bar button {
        border-radius: 8px !important;
    }
</style>

<div id="mapInfo">
    <div id="tabs">
        <div class="tabs-header">
            <a href="<?= Yii::$app->homeUrl ?>">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo_map.jpg" alt="Logo">
            </a>
            <button id="back-to-map-mobile-btn"></button>
        </div>

        <div class="tab-buttons">
            <button class="tab-button active" data-tab="layer">Lớp dữ liệu</button>
            <!-- THÊM TAB BỘ LỌC -->
            <button class="tab-button" data-tab="filter">Bộ lọc</button>
            <button class="tab-button" data-tab="info">Thông tin</button>
        </div>

        <div id="layer-content" class="tab-content active">
            <h5>Hiển thị lớp dữ liệu</h5>
            <div id="layer-control"></div>
        </div>
        
        <!-- THÊM NỘI DUNG CHO TAB BỘ LỌC -->
        <div id="filter-content" class="tab-content">
            <h5>Lọc dữ liệu điểm thu gom</h5>
            <form id="filter-form">
                <div class="filter-group">
                    <label for="filter-ward">Phường xã</label>
                    <select id="filter-ward" name="ward">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-area">Khu vực thu gom</label>
                    <select id="filter-area" name="area">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-loai_thu">Loại thu gom</label>
                    <select id="filter-loai_thu" name="loai_thu">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-loai_khach_hang">Phân loại khách hàng</label>
                    <select id="filter-loai_khach_hang" name="loai_khach_hang">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-loai_rac_thai">Phân loại rác thải</label>
                    <select id="filter-loai_rac_thai" name="loai_rac_thai">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-doi_tuong">Đối tượng thu gom</label>
                    <select id="filter-doi_tuong" name="doi_tuong">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter-nhan_vien">Nhân viên thu gom</label>
                    <select id="filter-nhan_vien" name="nhan_vien">
                        <option value="">Tất cả</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button type="submit">Áp dụng</button>
                    <button type="button" id="reset-filter-btn">Xóa lọc</button>
                </div>
            </form>
        </div>

        <div id="info-content" class="tab-content">
            <h5>Thông tin chi tiết</h5>
            <div id="feature-details">
                <p>Nhấn vào một đối tượng trên bản đồ để xem thông tin.</p>
            </div>
        </div>
    </div>

    <div id="mapTong">
        <div id="map"></div>
        <button id="toggle-tab-btn"></button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- KHẮC PHỤC LỖI 100VH TRÊN MOBILE ---
        const setAppHeight = () => {
            const doc = document.documentElement;
            doc.style.setProperty('--app-height', `${window.innerHeight}px`);
        };
        window.addEventListener('resize', setAppHeight);
        window.addEventListener('orientationchange', setAppHeight);
        setAppHeight(); // Set initial height

        // --- ICONS ---
        const ICONS = {
            menu: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>',
            close: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>'
        };

        // --- CONFIGURATION ---
        const WMS_URL = 'https://nongdanviet.net/geoserver/qlrac/wms';
        // THÊM URL ĐỂ LẤY DỮ LIỆU BỘ LỌC
        const FILTER_OPTIONS_URL = '<?= Url::to(['/quanly/map/get-filter-options']) ?>';
        const MAP_CENTER = [10.761978, 106.777679];
        const MAP_ZOOM = 11;
        const layerConfig = [{
                id: 'wmsDiemthugom1Layer',
                wmsName: 'qlrac:contracts_1',
                displayName: 'Điểm thu gom đã xác thực không cập nhật',
                isFilterable: true, // Đánh dấu lớp này có thể lọc
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'ma_kh': 'Mã khách hàng',
                    'khach_hang': 'Tên khách hàng',
                    'cua_hang_cong_ty': 'Cửa hàng/Công ty',
                    'address': 'Địa chỉ đầy đủ',
                    'street': 'Đường',
                    'ward': 'Phường',
                    'area': 'Khu vực',
                    'phone_number': 'Số điện thoại',
                    'debt_status': 'Tình trạng nợ',
                    'total_amount': 'Tổng tiền'
                }
            },
            {
                id: 'wmsDiemthugom2Layer',
                wmsName: 'qlrac:contracts_2',
                displayName: 'Điểm thu gom đã xác thực có cập nhật',
                isFilterable: true, // Đánh dấu lớp này có thể lọc
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'ma_kh': 'Mã khách hàng',
                    'khach_hang': 'Tên khách hàng',
                    'cua_hang_cong_ty': 'Cửa hàng/Công ty',
                    'address': 'Địa chỉ đầy đủ',
                    'street': 'Đường',
                    'ward': 'Phường',
                    'area': 'Khu vực',
                    'phone_number': 'Số điện thoại',
                    'debt_status': 'Tình trạng nợ',
                    'total_amount': 'Tổng tiền'
                }
            },
            {
                id: 'wmsDiemthugom3Layer',
                wmsName: 'qlrac:contracts_3',
                displayName: 'Điểm thu gom chưa xác thực',
                isFilterable: true, // Đánh dấu lớp này có thể lọc
                defaultVisible: true,
                zIndex: 750,
                popupFields: {
                    'customer_id': 'Mã khách hàng',
                    'khach_hang': 'Tên khách hàng',
                    'cua_hang_cong_ty': 'Cửa hàng/Công ty',
                    'phone_number': 'Số điện thoại',
                    'address': 'Địa chỉ đầy đủ',
                    'street': 'Đường',
                    'ward': 'Phường',
                    'area': 'Khu vực',
                    'debt_status': 'Tình trạng nợ',
                    'total_amount': 'Tổng tiền',
                    'loai_thu': 'Loại thu',
                    'loai_khach_hang': 'Loại khách hàng',
                    'loai_rac_thai': 'Loại rác thải',
                    'doi_tuong': 'Đối tượng',
                    'nhan_vien': 'Nhân viên',
                    // 'tinh_trang': 'Tình trạng hợp đồng',
                    // 'trang_thai': 'Trạng thái',
                }
            },
            {
                id: 'wmsDiemthugom4Layer',
                wmsName: 'qlrac:contracts_trangthai0',
                displayName: 'Điểm thu gom không nợ',
                isFilterable: true, // Đánh dấu lớp này có thể lọc
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'customer_id': 'Mã khách hàng',
                    'khach_hang': 'Tên khách hàng',
                    'cua_hang_cong_ty': 'Cửa hàng/Công ty',
                    'phone_number': 'Số điện thoại',
                    'address': 'Địa chỉ đầy đủ',
                    'street': 'Đường',
                    'ward': 'Phường',
                    'area': 'Khu vực',
                    'debt_status': 'Tình trạng nợ',
                    'total_amount': 'Tổng tiền',
                    'loai_thu': 'Loại thu',
                    'loai_khach_hang': 'Loại khách hàng',
                    'loai_rac_thai': 'Loại rác thải',
                    'doi_tuong': 'Đối tượng',
                    'nhan_vien': 'Nhân viên',
                    // 'tinh_trang': 'Tình trạng hợp đồng',
                    // 'trang_thai': 'Trạng thái',
                }
            },
            {
                id: 'wmsDiemthugom5Layer',
                wmsName: 'qlrac:contracts_trangthai1',
                displayName: 'Điểm thu gom còn nợ',
                isFilterable: true, // Đánh dấu lớp này có thể lọc
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'customer_id': 'Mã khách hàng',
                    'khach_hang': 'Tên khách hàng',
                    'cua_hang_cong_ty': 'Cửa hàng/Công ty',
                    'phone_number': 'Số điện thoại',
                    'address': 'Địa chỉ đầy đủ',
                    'street': 'Đường',
                    'ward': 'Phường',
                    'area': 'Khu vực',
                    'debt_status': 'Tình trạng nợ',
                    'total_amount': 'Tổng tiền',
                    'loai_thu': 'Loại thu',
                    'loai_khach_hang': 'Loại khách hàng',
                    'loai_rac_thai': 'Loại rác thải',
                    'doi_tuong': 'Đối tượng',
                    'nhan_vien': 'Nhân viên',
                    // 'tinh_trang': 'Tình trạng hợp đồng',
                    // 'trang_thai': 'Trạng thái',
                }
            },
            {
                id: 'wmsDiemthugom6Layer',
                wmsName: 'qlrac:contracts_all',
                displayName: 'Điểm thu gom chung',
                isFilterable: true, // Đánh dấu lớp này có thể lọc
                defaultVisible: true,
                zIndex: 750,
                popupFields: {
                    'customer_id': 'Mã khách hàng',
                    'khach_hang': 'Tên khách hàng',
                    'cua_hang_cong_ty': 'Cửa hàng/Công ty',
                    'phone_number': 'Số điện thoại',
                    'address': 'Địa chỉ đầy đủ',
                    'street': 'Đường',
                    'ward': 'Phường',
                    'area': 'Khu vực',
                    'debt_status': 'Tình trạng nợ',
                    'total_amount': 'Tổng tiền',
                    'loai_thu': 'Loại thu',
                    'loai_khach_hang': 'Loại khách hàng',
                    'loai_rac_thai': 'Loại rác thải',
                    'doi_tuong': 'Đối tượng',
                    'nhan_vien': 'Nhân viên',
                    // 'tinh_trang': 'Tình trạng hợp đồng',
                    // 'trang_thai': 'Trạng thái',
                }
            },
            {
                id: 'wmsKhuphoLayer',
                wmsName: 'qlrac:kp',
                displayName: 'Khu phố',
                defaultVisible: false,
                zIndex: 450,
                popupFields: {
                    'ten_px_moi': 'Tên phường xã (mới)',
                    'TenKhuPho': 'Tên khu phố'
                }
            },
            {
                id: 'wmsPhuongxaLayer',
                wmsName: 'qlrac:phuongxa',
                displayName: 'Phường xã',
                defaultVisible: false,
                zIndex: 450,
                popupFields: {
                    'ten_dvhc': 'Tên đơn vị hành chính',
                    'quanhuyen_cu': 'Quận huyện cũ',
                    'sapxeptu': 'Sắp xếp từ'
                }
            },
            {
                id: 'wmsCHTLLayer',
                wmsName: 'qlrac:qlrac_chtienloi',
                displayName: 'Cửa hàng tiện lợi',
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'Ten': 'Tên',
                    'DiaChi': 'Địa chỉ',
                    'PhuongXa': 'Phường xã',
                    'QuanHuyenTP': 'Quận huyện'
                }
            },
            {
                id: 'wmsNhathuocLayer',
                wmsName: 'qlrac:qlrac_nhathuoc',
                displayName: 'Nhà thuốc',
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'Ten': 'Tên',
                    'SoNha': 'Số nhà',
                    'TenDuong': 'Tên đường',
                    'PhuongXa': 'Phường xã',
                    'QuanHuyenT': 'Quận huyện'
                }
            },
            {
                id: 'wmsDuongLayer',
                wmsName: 'qlrac:qlrac_road',
                displayName: 'Đường',
                defaultVisible: false,
                zIndex: 450,
                popupFields: {
                    'name': 'Tên'
                }
            },
            {
                id: 'wmsSieuthiLayer',
                wmsName: 'qlrac:qlrac_sieuthi',
                displayName: 'Siêu thị',
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'Ten': 'Tên',
                    'DiaChi': 'Địa chỉ',
                    'PhuongXa': 'Phường xã',
                    'QuanHuyenTP': 'Quận huyện'
                }
            },
            {
                id: 'wmsThuyheLayer',
                wmsName: 'qlrac:qlrac_thuyhe',
                displayName: 'Thủy hệ',
                defaultVisible: false,
                zIndex: 450,
                popupFields: {
                    'name': 'Tên'
                }
            },
            {
                id: 'wmsTongiaoLayer',
                wmsName: 'qlrac:qlrac_tongiao',
                displayName: 'Tôn giáo',
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'name': 'Tên'
                }
            },
            {
                id: 'wmsTTTMLayer',
                wmsName: 'qlrac:qlrac_tttm',
                displayName: 'Trung tâm thương mại',
                defaultVisible: false,
                zIndex: 750,
                popupFields: {
                    'Ten': 'Tên',
                    'DiaChi': 'Địa chỉ',
                    'PhuongXa': 'Phường xã',
                    'QuanHuyenTP': 'Quận huyện'
                }
            },
        ];

        // --- MAP INITIALIZATION ---
        const map = L.map('map', {
            zoomControl: false
        }).setView(MAP_CENTER, MAP_ZOOM);
        const leafletLayers = {};
        L.control.zoom({
            position: 'topright'
        }).addTo(map);

        const baseMaps = {
            "Bản đồ Google": L.tileLayer('https://{s}.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            }).addTo(map),
            "Ảnh vệ tinh": L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
                maxZoom: 22,
                subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
            })
        };

        map.createPane('highlightPane').style.zIndex = 700;
        const highlightLayer = L.geoJSON(null, {
            pane: 'highlightPane',
            style: {
                color: '#ff0000',
                weight: 5,
                opacity: 1,
                fillOpacity: 0.3,
                dashArray: '5, 5'
            }
        }).addTo(map);

        // --- DYNAMIC UI GENERATION ---
        function initializeDynamicUI() {
            const layerControlContainer = document.getElementById('layer-control');
            let legendHtml = '<h4>Chú giải</h4>';

            layerConfig.forEach(config => {
                const label = document.createElement('label');
                label.className = 'layer-item';
                label.innerHTML = `<input type="checkbox" data-layer-id="${config.id}" ${config.defaultVisible ? 'checked' : ''}> <span>${config.displayName}</span>`;
                layerControlContainer.appendChild(label);

                const legendUrl = `${WMS_URL}?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=20&HEIGHT=20&LAYER=${config.wmsName}`;
                legendHtml += `<div class="legend-item"><img src="${legendUrl}" alt="${config.displayName}"><span>${config.displayName}</span></div>`;
            });

            legendControl.getContainer().innerHTML = legendHtml;
            document.getElementById('toggle-tab-btn').innerHTML = ICONS.menu;
            document.getElementById('back-to-map-mobile-btn').innerHTML = ICONS.close;
        }

        // --- LAYER MANAGEMENT ---
        function createWmsLayer(config) {
            map.createPane(config.id).style.zIndex = config.zIndex;
            return L.tileLayer.wms(WMS_URL, {
                layers: config.wmsName,
                format: 'image/png',
                transparent: true,
                maxZoom: 22,
                pane: config.id
            });
        }

        layerConfig.forEach(config => {
            leafletLayers[config.id] = createWmsLayer(config);
            if (config.defaultVisible) {
                leafletLayers[config.id].addTo(map);
            }
        });

        // --- MAP CONTROLS ---
        L.control.layers(baseMaps, null, {
            position: 'topright'
        }).addTo(map);
        L.control.scale({
            imperial: false
        }).addTo(map);
        new L.Control.Measure({
            position: 'topright',
            primaryLengthUnit: 'meters',
            primaryAreaUnit: 'sqmeters'
        }).addTo(map);
        new L.Control.Locate({
            position: 'topright',
            strings: {
                title: "Hiện vị trí"
            }
        }).addTo(map);

        const legendControl = L.control({
            position: 'bottomright'
        });
        legendControl.onAdd = () => L.DomUtil.create('div', 'legend');
        legendControl.addTo(map);

        const legendToggle = L.control({
            position: 'bottomright'
        });
        legendToggle.onAdd = () => {
            const button = L.DomUtil.create('button', 'leaflet-bar');
            button.innerHTML = 'Chú giải';
            button.style.cursor = 'pointer';
            button.onclick = () => {
                const legendDiv = legendControl.getContainer();
                legendDiv.style.display = (legendDiv.style.display === 'none' || legendDiv.style.display === '') ? 'block' : 'none';
            };
            return button;
        };
        legendToggle.addTo(map);

        // --- EVENT HANDLERS ---
        map.on('click', async function(e) {
            const latlng = e.latlng;
            const point = map.latLngToContainerPoint(latlng, map.getZoom());
            const size = map.getSize();
            const bbox = map.getBounds().toBBoxString();

            const visibleLayers = layerConfig
                .filter(cfg => map.hasLayer(leafletLayers[cfg.id]))
                .sort((a, b) => b.zIndex - a.zIndex);

            if (visibleLayers.length === 0) return;

            document.getElementById('feature-details').innerHTML = '<p>Đang tải...</p>';
            highlightLayer.clearLayers();

            for (const config of visibleLayers) {
                // Lấy CQL_FILTER hiện tại của layer nếu có
                const cqlFilter = leafletLayers[config.id].wmsParams.CQL_FILTER;
                let url = `${WMS_URL}?SERVICE=WMS&VERSION=1.1.1&REQUEST=GetFeatureInfo&LAYERS=${config.wmsName}&QUERY_LAYERS=${config.wmsName}&BBOX=${bbox}&FEATURE_COUNT=1&HEIGHT=${size.y}&WIDTH=${size.x}&INFO_FORMAT=application/json&SRS=EPSG:4326&X=${Math.floor(point.x)}&Y=${Math.floor(point.y)}`;
                
                // Thêm CQL_FILTER vào URL nếu nó tồn tại
                if (cqlFilter) {
                    url += `&CQL_FILTER=${encodeURIComponent(cqlFilter)}`;
                }

                try {
                    const response = await fetch(url);
                    const data = await response.json();

                    if (data.features && data.features.length > 0) {
                        displayFeatureInfo(data.features[0], config);
                        if (window.innerWidth <= 768) {
                            openTab('info');
                            toggleTabPanel(true);
                        }
                        return;
                    }
                } catch (error) {
                    console.error(`Lỗi khi lấy thông tin từ lớp ${config.displayName}:`, error);
                }
            }

            document.getElementById('feature-details').innerHTML = '<p>Không tìm thấy thông tin tại vị trí này.</p>';
        });

        function displayFeatureInfo(feature, config) {
            const properties = feature.properties;
            let content = `<div class='popup-content'><h4>${config.displayName}</h4><table>`;

            for (const key in config.popupFields) {
                if (properties.hasOwnProperty(key)) {
                    content += `<tr><th>${config.popupFields[key]}</th><td>${properties[key] || 'Không có'}</td></tr>`;
                }
            }
            content += "</table></div>";
            document.getElementById('feature-details').innerHTML = content;

            highlightLayer.clearLayers();
            highlightLayer.addData(feature);
        }

        document.getElementById('layer-control').addEventListener('change', function(e) {
            if (e.target.matches('input[type="checkbox"]')) {
                const layerId = e.target.dataset.layerId;
                const layer = leafletLayers[layerId];
                if (e.target.checked) {
                    map.addLayer(layer);
                } else {
                    map.removeLayer(layer);
                }
            }
        });

        // --- UI FUNCTIONS ---
        document.querySelector('.tab-buttons').addEventListener('click', function(e) {
            if (e.target.classList.contains('tab-button')) {
                openTab(e.target.dataset.tab);
            }
        });

        function openTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabName + '-content').classList.add('active');

            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`.tab-button[data-tab='${tabName}']`).classList.add('active');
        }

        window.toggleTabPanel = function(forceShow) {
            const tabs = document.getElementById('tabs');
            const isMobile = window.innerWidth <= 768;
            let show;

            if (typeof forceShow === 'boolean') {
                show = forceShow;
            } else {
                show = isMobile ? !tabs.classList.contains('active') : tabs.classList.contains('hidden');
            }

            if (isMobile) {
                tabs.classList.remove('hidden');
                tabs.classList.toggle('active', show);
            } else {
                tabs.classList.remove('active');
                tabs.classList.toggle('hidden', !show);
            }

            setTimeout(() => map.invalidateSize(), 300);
        }

        document.getElementById('toggle-tab-btn').addEventListener('click', () => toggleTabPanel());
        document.getElementById('back-to-map-mobile-btn').addEventListener('click', () => toggleTabPanel(false));


        // --- FILTER LOGIC ---

        /**
         * Hàm điền các lựa chọn vào một thẻ <select>
         * @param {string} selectId - ID của thẻ select
         * @param {Array<string>} options - Mảng các giá trị
         */
        function populateSelectOptions(selectId, options) {
            const select = document.getElementById(selectId);
            if (!select) return;

            // Xóa các option cũ (trừ option "Tất cả")
            select.innerHTML = '<option value="">Tất cả</option>';

            options.forEach(optionValue => {
                const option = document.createElement('option');
                option.value = optionValue;
                option.textContent = optionValue;
                select.appendChild(option);
            });
        }

        /**
         * Hàm tải dữ liệu cho các bộ lọc từ server
         */
        async function loadFilterOptions() {
            try {
                const response = await fetch(FILTER_OPTIONS_URL);
                const data = await response.json();
                
                populateSelectOptions('filter-ward', data.ward || []);
                populateSelectOptions('filter-area', data.area || []);
                populateSelectOptions('filter-loai_thu', data.loai_thu || []);
                populateSelectOptions('filter-loai_khach_hang', data.loai_khach_hang || []);
                populateSelectOptions('filter-loai_rac_thai', data.loai_rac_thai || []);
                populateSelectOptions('filter-doi_tuong', data.doi_tuong || []);
                populateSelectOptions('filter-nhan_vien', data.nhan_vien || []);

            } catch (error) {
                console.error("Lỗi khi tải dữ liệu bộ lọc:", error);
            }
        }
        
        /**
         * Hàm áp dụng bộ lọc vào các lớp WMS
         */
        function applyWmsFilter() {
            const form = document.getElementById('filter-form');
            const formData = new FormData(form);
            const cqlParts = [];

            for (const [name, value] of formData.entries()) {
                if (value) { // Chỉ thêm vào filter nếu có giá trị được chọn
                    // Thêm dấu nháy đơn cho giá trị chuỗi trong CQL
                    cqlParts.push(`${name} = '${value.replace(/'/g, "''")}'`);
                }
            }

            const cqlFilter = cqlParts.length > 0 ? cqlParts.join(' AND ') : null;

            // Lặp qua các layer đã cấu hình và áp dụng filter nếu nó được đánh dấu là 'isFilterable'
            layerConfig.forEach(config => {
                if (config.isFilterable) {
                    const layer = leafletLayers[config.id];
                    if (layer) {
                        // SỬA LỖI: Để xóa bộ lọc một cách đáng tin cậy, chúng ta sẽ sửa đổi trực tiếp
                        // đối tượng wmsParams của layer. Việc đặt CQL_FILTER thành `undefined` hoặc `null`
                        // có thể khiến Leaflet gửi chuỗi "undefined" hoặc "null" trong URL,
                        // gây ra lỗi trên GeoServer.
                        if (cqlFilter) {
                            // Nếu có bộ lọc, gán nó vào tham số.
                            layer.wmsParams.CQL_FILTER = cqlFilter;
                        } else {
                            // Nếu không có bộ lọc, xóa hoàn toàn tham số khỏi đối tượng.
                            delete layer.wmsParams.CQL_FILTER;
                        }
                        // Gọi redraw() để buộc layer làm mới và sử dụng wmsParams đã được cập nhật.
                        layer.redraw();
                    }
                }
            });
        }

        // Gắn sự kiện cho form
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn form gửi đi theo cách truyền thống
            applyWmsFilter();
        });

        // Gắn sự kiện cho nút xóa lọc
        document.getElementById('reset-filter-btn').addEventListener('click', function() {
            const form = document.getElementById('filter-form');
            form.reset(); // Reset tất cả các select về giá trị đầu tiên
            applyWmsFilter(); // Áp dụng lại bộ lọc (lúc này sẽ là rỗng)
        });


        // --- INITIALIZATION ---
        initializeDynamicUI();
        loadFilterOptions(); // Tải dữ liệu bộ lọc khi trang được tải

        // Set initial state based on screen size
        if (window.innerWidth > 768) {
            toggleTabPanel(true); // Luôn hiện panel trên desktop khi tải
        } else {
            toggleTabPanel(false); // Luôn ẩn panel trên mobile khi tải
        }
    });
</script>
