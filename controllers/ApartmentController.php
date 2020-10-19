<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\UploadForm;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;
use app\models\Apartment;
use yii\helpers\ArrayHelper;

class ApartmentController extends Controller
{
    public function actionIndex()
    {
		$model = new UploadForm();
		$apartments = [];

		if (Yii::$app->request->isPost) {
			$model->file = UploadedFile::getInstance($model, 'file');
			Yii::$app->session->remove('apartments');

			if (!$model->validate() && array_key_exists('file', $model->errors)) {
				Yii::$app->session->setFlash('error', implode("<br>", $model->errors['file']));
			} else {
				$parseError = false;

				try {
					$apartments = $model->parse();
				} catch(\Exception $e) {
					$parseError = true;
				}

				if ($parseError) {
					Yii::$app->session->setFlash('error', "Возникла ошибка при обработке загруженного файла.");
				} else if (count($apartments)) {
					Apartment::insertOrUpdate($apartments);

					Yii::$app->session->set('apartments', $apartments);

					$message = "Данные по квартирам в " . UploadForm::SECTION;
					if (Apartment::$insertedApartmentsCount > 0 || Apartment::$updatedApartmentsCount > 0) {
						$message .= " подъезде были сохранены: " . Apartment::$insertedApartmentsCount . " добавлено, " . Apartment::$updatedApartmentsCount . " обновлено.";
						Yii::$app->session->setFlash('success', $message);
					} else {
						$message .= " подъезде не изменены, так как они уже в актуальном состоянии.";
						Yii::$app->session->setFlash('success', $message);
					}

				} else {
					Yii::$app->session->setFlash('error', "В загруженном файле отсутсвуют данные по квартирам в " . UploadForm::SECTION ." подъезде.");
				}
			}
		}

		if (Yii::$app->session->has('apartments')) {
			$apartments = Yii::$app->session->get('apartments');
		} else {
			$apartments = [];
		}

		$provider = new ArrayDataProvider([
			'allModels' => $apartments,
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
