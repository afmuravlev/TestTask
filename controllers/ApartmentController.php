<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\UploadForm;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;

class ApartmentController extends Controller
{
    public function actionIndex()
    {
		$model = new UploadForm();
		$appartments = [];

		if (Yii::$app->request->isPost) {
			$model->file = UploadedFile::getInstance($model, 'file');

			if (!$model->validate() && array_key_exists('file', $model->errors)) {
				Yii::$app->session->setFlash('error', implode("<br>", $model->errors['file']));
			} else {
				Yii::$app->session->setFlash('success', "Файле загружен.");
			}
		}

		$provider = new ArrayDataProvider([
			'allModels' => $appartments,
			'pagination' => [
				'pageSize' => 15,
			],
			'sort' => [
				'attributes' => ['number'],
				'defaultOrder' => ['number' => SORT_ASC],
			],
		]);

        return $this->render('index', [
			'model' => $model,
			'provider' => $provider
		]);
    }

}