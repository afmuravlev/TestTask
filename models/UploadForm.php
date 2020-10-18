<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * Section for which looking for aprtments
     *
     * @var int
     */
	public const SECTION = 3;

    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * Current file spreadsheet
     *
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
	private $sheet;

    /**
     * Max count of sheet rows
     *
     * @var int
     */
	private $maxRow;

    /**
     * Max count of sheet columns
     *
     * @var int
     */
	private $maxCol;

    /**
     * Begin row of section
     *
     * @var int
     */
	private $beginRow;

    /**
     * Begin column of section
     *
     * @var int
     */
	private $beginCol;

    /**
     * Count of section floors
     *
     * @var int
     */
	private $floors;

    /**
     * Count of apartments on section floor
     *
     * @var int
     */
	private $apartmentsOnFloor;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false]
        ];
    }

    /**
    * Find begin row, begin column, rooms count, floor count for section
    *
    * @param array $sheetData All data from sheet as array
    *
    * @return boolean
    */
	private function findSectionData($sheetData)
	{
		for ($row = 1; $row < $this->maxRow; $row++) {
			for ($col = 0; $col < $this->maxCol; $col++) {
				if (preg_match("/эт\./", $sheetData[$row][$col])) {
					$this->apartmentsOnFloor = 0;
					for ($sectionCol = $col + 1; $sectionCol < $this->maxCol; $sectionCol += 2) {
						if($sheetData[$row][$sectionCol] == "" || preg_match("/эт\./", $sheetData[$row][$sectionCol])) {
							break;
						}
						$this->apartmentsOnFloor++;
					}

					for ($sectionCol = $col; $sectionCol < $col + ($this->apartmentsOnFloor * 2); $sectionCol++) {
						if (preg_match("/Подъезд (?<section>\d+)/", $sheetData[$row - 1][$sectionCol], $matches) && ($matches['section'] == self::SECTION)) {
							$this->beginRow = $row + 1;
							$this->beginCol = $col + 1;
							$this->floors = 0;
							for ($sectionRow = $row + 2; $sectionRow < $this->maxRow; $sectionRow += 3) {
								if ($sheetData[$sectionRow][$col] == "" || preg_match("/эт\./", $sheetData[$sectionRow][$col])) {
									break;
								}
								$this->floors++;
							}

							return true;
						}
					}
				}
			}
		}

		return false;
	}

    /**
    * Get apartment status from cell
    *
    * @param int $apartmentCol Column of cell
    * @param int $apartmentRow Row of cell
    *
    * @return boolean
    */
	private function getStatus($apartmentCol, $apartmentRow)
	{
		if ($apartmentCol < 26) {
			$apartmentColValue = chr(ord('A') + $apartmentCol);
		} else {
			$firstLetter = chr(ord('A') + $apartmentCol / 26);
			$secondLetter = chr(ord('A') + $apartmentCol % 26);
			$apartmentColValue = "$firstLetter$secondLetter";
		}

		$color = $this->sheet->getStyle($apartmentColValue . ($apartmentRow + 2))->getFill()->getStartColor()->getRGB();

		return $color == "FFFFFF";
	}

    /**
    * Extract apartments data from uploaded file
    *
    * @return array
    */
	public function parse()
	{
		$apartments = [];
		$this->sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file->tempName)->getSheet(0);
		$sheetData = $this->sheet->toArray(null, true, true, false);
		$this->maxRow = count($sheetData);
		$this->maxCol = count($sheetData[1]);

		if (!$this->findSectionData($sheetData)) {
			return $apartments;
		}

		for ($sectionFloor = 0; $sectionFloor < $this->floors; $sectionFloor++) {
			for ($sectionApartment = 0; $sectionApartment < $this->apartmentsOnFloor; $sectionApartment++) {
				$apartmentRow = $sectionFloor * 3 + $this->beginRow;
				$apartmentCol = $sectionApartment * 2 + $this->beginCol;

				if (empty($sheetData[$apartmentRow + 1][$apartmentCol])) {
					continue;
				}

				$apartments[] = [
					'floor' => (int)$sheetData[$apartmentRow + 1][$this->beginCol - 1],
					'number' => (int)$sheetData[$apartmentRow + 1][$apartmentCol],
					'room' => (int)$sheetData[$this->beginRow - 1][$apartmentCol],
					'square' => (float)$sheetData[$apartmentRow][$apartmentCol + 1],
					'price' => (int)str_replace(",", "", $sheetData[$apartmentRow + 1][$apartmentCol + 1]),
					'cost' => (int)str_replace(",", "", $sheetData[$apartmentRow + 2][$apartmentCol + 1]),
					'status' => $this->getStatus($apartmentCol, $apartmentRow),
				];
			}
		}

		return $apartments;
	}
}

