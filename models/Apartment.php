<?php

namespace app\models;

use Yii;

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
            [['number', 'floor', 'room', 'price', 'cost', 'status'], 'integer'],
            [['square'], 'number'],
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
}
