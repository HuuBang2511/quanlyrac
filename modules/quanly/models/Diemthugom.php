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
            [['customer_id'], 'string', 'max' => 50],
            [['province', 'ward', 'phone_number'], 'string', 'max' => 100],
            [['street', 'area', 'debt_status'], 'string', 'max' => 255],
            [['customer_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'customer_id' => 'Customer ID',
            'address' => 'Địa chỉ',
            'province' => 'Tỉnh thành',
            'ward' => 'Phường',
            'street' => 'Đường',
            'phone_number' => 'Số điện thoại',
            'area' => 'Khu vực',
            'debt_status' => 'Debt Status',
            'total_amount' => 'Total Amount',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'geom' => 'Geom',
            'status' => 'Loại',
        ];
    }
}
