<?php

namespace app\modules\quanly\models;

use app\modules\quanly\base\QuanlyBaseModel;
use Yii;

/**
 * This is the model class for table "contracts".
 *
 * @property string $customer_id
 * @property string|null $address
 * @property string|null $province
 * @property string|null $ward
 * @property string|null $street
 * @property string|null $phone_number
 * @property string|null $area
 * @property string|null $debt_status
 * @property float|null $total_amount
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $geom
 * @property int|null $status
 * @property string|null $tinh_trang
 * @property string|null $khach_hang
 * @property string|null $cua_hang_cong_ty
 * @property string|null $loai_khach_hang
 * @property string|null $loai_rac_thai
 * @property string|null $doi_tuong
 * @property string|null $loai_thu
 * @property string|null $nhan_vien
 * @property string|null $trang_thai
 */
class Diemthugom extends QuanlyBaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contracts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id'], 'required'],
            [['address', 'geom'], 'string'],
            [['total_amount', 'latitude', 'longitude'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'default', 'value' => null],
            [['status'], 'integer'],
            [['customer_id', 'tinh_trang', 'trang_thai'], 'string', 'max' => 50],
            [['province', 'ward', 'phone_number', 'loai_khach_hang', 'loai_rac_thai', 'loai_thu'], 'string', 'max' => 100],
            [['street', 'area', 'debt_status', 'khach_hang', 'cua_hang_cong_ty', 'doi_tuong', 'nhan_vien'], 'string', 'max' => 255],
            [['customer_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => 'Mã khách hàng',
            'address' => 'Địa chỉ',
            'province' => 'Tỉnh thành',
            'ward' => 'Phường',
            'street' => 'Đường',
            'phone_number' => 'Số điện thoại',
            'area' => 'Khu vực',
            'debt_status' => 'Tình trạng nợ',
            'total_amount' => 'Tổng tiền',
            'latitude' => 'Vĩ độ',
            'longitude' => 'Kinh độ',
            'created_at' => 'Ngày tạo',
            'updated_at' => 'Ngày cập nhật',
            'geom' => 'Geom',
            'status' => 'Loại',
            'tinh_trang' => 'Tình trạng',
            'khach_hang' => 'Khách hàng',
            'cua_hang_cong_ty' => 'Cửa hàng/Công ty',
            
            // Các nhãn cho cột mới
            'loai_khach_hang' => 'Loại khách hàng',
            'loai_rac_thai' => 'Loại rác thải',
            'doi_tuong' => 'Đối tượng',
            'loai_thu' => 'Loại thu',
            'nhan_vien' => 'Nhân viên',
            'trang_thai' => 'Trạng thái',
        ];
    }
    
    /**
     * Lấy danh sách nhãn của các trạng thái
     * @return array
     */
    public static function getStatusLabels()
    {
        return [
            1 => 'Điểm thu gom đã xác thực không cập nhật',
            2 => 'Điểm thu gom đã xác thực có cập nhật',
            3 => 'Điểm thu gom chưa xác thực',
        ];
    }

    /**
     * Lấy nhãn của một trạng thái cụ thể
     * @param int|null $status
     * @return string
     */
    public static function getStatusLabel($status)
    {
        $labels = self::getStatusLabels();
        return $labels[$status] ?? 'Không xác định';
    }
    
}