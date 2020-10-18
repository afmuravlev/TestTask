<?php
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use app\assets\LoadFileFormAsset;

/* @var $this yii\web\View */

LoadFileFormAsset::register($this);
?>

<div>
	<?php $form = ActiveForm::begin([
		'id' => 'load-file-form',
		'action' => ['apartment/index'],
		'enableClientValidation' => false,
		'options' => ['enctype' => 'multipart/form-data']
	]) ?>

	<div style="display: none">
		<?= $form->field($model, 'file')->fileInput(['id' => 'select-file',])->label(false) ?>
	</div>

	<button id="select-and-commit-btn">Выбрать и загрузить файл</button>

	<?php ActiveForm::end() ?>
</div>

<br>

<div>
	<?= GridView::widget([
		'dataProvider' => $provider,
		'emptyText' => "Данные по квартирам ещё не получены",
		'columns' => [
			[
				'attribute' => 'number',
				'label' => 'Номер',
			],
			[
				'attribute' => 'floor',
				'label' => 'Этаж',
			],
			[
				'attribute' => 'room',
				'label' => 'Количество комнат',
			],
			[
				'attribute' => 'square',
				'label' => 'Площадь',
			],
			[
				'attribute' => 'price',
				'label' => 'Цена',
			],
			[
				'attribute' => 'cost',
				'label' => 'Стоимость',
			],
			[
				'attribute' => 'status',
				'label' => 'Статус',
				'value' => function (array $apartment) {
					return $apartment['status'] ? "Продана" : "Продается";
				}
			],
		],
	]); ?>
</div>
