<?php
namespace Website\Issues;

trait IssueObjectTrait {

	protected $issueId;

	protected function setIssueId(int $issueId) {
		$this->issueId = $issueId;
	}

	public static function getAllIssueObjects(int $issueId) {
		$collection = new static();
		$collection->setIssueId($issueId);
		$collection->fetchAll();
		return $collection;
	}

	public static function getOneIssueObject(int $issueId, array $where) {
		$collection = new static();
		$collection->setIssueId($issueId);
		$collection->fetchOne($where);
		return $collection->current();
	}

}
