<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "apartment".
 *
 * @property int $id
 * @property int $number
 * @property int $floor
 * @property int $room
 * @property float $square
 * @property int $price
 * @property int $cost
 * @property int $status
 */
class Apartment extends \yii\db\ActiveRecord
{
    /**
     * Count of updated apartment
     *
     * @var int
     */
    public static $updatedApartmentsCount;

    /**
     * Count of inserted apartment
     *
     * @var int
     */
    public static $insertedApartmentsCount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apartment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'floor', 'room', 'square', 'price', 'cost', 'status'], 'required'],
            [['number', 'floor', 'room', 'price', 'cost'], 'integer'],
            [['square'], 'number'],
            [['status'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'floor' => 'Floor',
            'room' => 'Room',
            'square' => 'Square',
            'price' => 'Price',
            'cost' => 'Cost',
            'status' => 'Status',
        ];
    }

    /**
    * Insert new apartments and update changed appertments
    *
    * @param array $apartments Apartment data
    */
	public static function insertOrUpdate(array $apartments)
	{
		$transaction = Yii::$app->db->beginTransaction();

		try {
			$numbers = ArrayHelper::getColumn($apartments, 'number');
			$savedApartments = Apartment::find()->where(['in', 'number', $numbers])->indexBy('number')->all();
			self::$updatedApartmentsCount = 0;
			self::$insertedApartmentsCount = 0;

			foreach ($apartments as $apartmentData) {
				if (array_key_exists($apartmentData['number'], $savedApartments)) {
					$apartment = $savedApartments[$apartmentData['number']];

					if ($apartmentData['floor'] != $apartment['floor']
						|| $apartmentData['room'] != $apartment['room']
						|| $apartmentData['square'] != $apartment['square']
						|| $apartmentData['price'] != $apartment['price']
						|| $apartmentData['cost'] != $apartment['cost']
						|| $apartmentData['status'] != $apartment['status']
					) {
						$apartment->attributes = $apartmentData;
						self::$updatedApartmentsCount++;
					} else {
						continue;
					}
				} else {
					$apartment = new Apartment($apartmentData);
					self::$insertedApartmentsCount++;
				}
				$apartment->save();
			}
			$transaction->commit();
		} catch(\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}
