<?php

namespace app\modules\quanly\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\quanly\models\Diemthugom;

/**
 * DiemthugomSearch represents the model behind the search form about `app\modules\quanly\models\Diemthugom`.
 */
class DiemthugomSearch extends Diemthugom
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer_id', 'address', 'province', 'ward', 'street', 'phone_number', 'area', 'debt_status', 'created_at', 'updated_at', 'geom'], 'safe'],
            [['total_amount', 'latitude', 'longitude'], 'number'],
            [['status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Diemthugom::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'total_amount' => $this->total_amount,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'upper(customer_id)', mb_strtoupper($this->customer_id)])
            ->andFilterWhere(['like', 'upper(address)', mb_strtoupper($this->address)])
            ->andFilterWhere(['like', 'upper(province)', mb_strtoupper($this->province)])
            ->andFilterWhere(['like', 'upper(ward)', mb_strtoupper($this->ward)])
            ->andFilterWhere(['like', 'upper(street)', mb_strtoupper($this->street)])
            ->andFilterWhere(['like', 'upper(phone_number)', mb_strtoupper($this->phone_number)])
            ->andFilterWhere(['like', 'upper(area)', mb_strtoupper($this->area)])
            ->andFilterWhere(['like', 'upper(debt_status)', mb_strtoupper($this->debt_status)])
            ->andFilterWhere(['like', 'upper(geom)', mb_strtoupper($this->geom)]);

        return $dataProvider;
    }

    public function getExportColumns()
    {
        return [
            [
                'class' => 'kartik\grid\SerialColumn',
            ],
            'customer_id',
        'address',
        'province',
        'ward',
        'street',
        'phone_number',
        'area',
        'debt_status',
        'total_amount',
        'latitude',
        'longitude',
        'created_at',
        'updated_at',
        'geom',
        'status',        ];
    }
}
