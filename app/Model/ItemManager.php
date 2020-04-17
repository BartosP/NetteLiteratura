<?php

declare(strict_types=1);

namespace App\Model;

use Nette;

/**
 * Users management.
 */
final class ItemManager {
	use Nette\SmartObject;

	private const
		TABLE_NAME = 'item',
		COLUMN_ID = 'id',
		COLUMN_TITLE = 'title',
		COLUMN_AUTHOR = 'author',
		COLUMN_ANOTATION = 'anotation',
		COLUMN_YEAR = 'year',
		COLUMN_CATEGORY = 'category',
		COLUMN_STARS = 'stars';


	/** @var Nette\Database\Context */
	private $database;

	public function __construct(Nette\Database\Context $database) {
		$this->database = $database;
	}

	public function getAll($order = self::COLUMN_TITLE) {
		return $this->database->table(self::TABLE_NAME)->order($order)->fetchAll();
	}
		
	public function getById($id) {
		return $this->database->table(self::TABLE_NAME)->get($id);
	}
	   
	public function insert($values) {
		try {
			$this->database->table(self::TABLE_NAME)->insert($values);
			return true;
		}
		catch (Nette\Database\DriverException $e) {
			return false;
		}
	}

	public function update($id, $values) {
		if ($zaznam = $this->getById($id)) {
			return $zaznam->update($values);
		}
		return false;
	}

	public function delete($id) {
		if ($zaznam = $this->getById($id)) {
			return $zaznam->delete();
		}
		return false;
	} 
}