<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\quanly\models\Diemthugom */
?>
<div class="diemthugom-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'customer_id',
            'address:ntext',
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
            'status',
        ],
    ]) ?>

</div>
