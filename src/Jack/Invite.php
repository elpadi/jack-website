<?php
namespace Jack;

class Invite {

	public $id = 0;
	public $email = "";
	public $additional = "";
	public $hash = "";

	public $uses = array();
	public $sends = array();

	const EMAIL_SUBJECT = 'For your eyes only. Invitation to thejackmag.com';
	const EMAIL_ADDRESS = 'dah@thejackmag.com';
	const EMAIL_NAME = 'Dahlen';

	public function __construct() {
	}

	public function hydrate(DbAccess $db) {
		foreach ($db->query('SELECT * FROM '.$db->table('invites_uses')." WHERE `invite_id`=$this->id") as $row) {
			$this->uses[] = $row['time'];
		}
		foreach ($db->query('SELECT * FROM '.$db->table('invites_sent')." WHERE `invite_id`=$this->id") as $row) {
			$this->sends[] = $row['time'];
		}
	}

	public function recordUse(DbAccess $db) {
		$db->query('INSERT INTO '.$db->table('invites_uses')." SET `invite_id`=$this->id, `time`=NOW()");
	}

	protected function addToDb(DbAccess $db) {
		$this->hash = md5($this->email);
		$sql = "INSERT INTO `".$db->table("invites")."` SET `email`=?, `hash`=?, `additional`=?";
		$db->query($sql, array($this->email, $this->hash, $this->additional));
		$this->id = $db->lastInsertId();
	}

	protected function recordSend(DbAccess $db) {
		$sql = "INSERT INTO `".$db->table("invites_sent")."` SET `invite_id`=?, `time`=NOW()";
		$db->query($sql, array($this->id));
	}

	public function send(DbAccess $db, EmailSender $email, TemplateHandler $tpl) {
		if ($this->id === 0) {
			$this->addToDb($db);
		}
		$this->recordSend($db);
		$tplVars = array(
			'additional' => $this->additional,
			'href' => "/invite/$this->hash",
			'itExpires' => false,
			'expirationDate' => null
		);
		$email->sendHtml($this->email, self::EMAIL_SUBJECT, array(self::EMAIL_ADDRESS => self::EMAIL_NAME), array(
			'plain' => $tpl->parseTemplate('email/invite-plain', $tplVars),
			'html' => $tpl->parseTemplate('email/invite-html', $tplVars),
		));
	}

	public function setData($data) {
		$this->id = isset($data['id']) ? (int)($data['id']) : 0;
		if (isset($data['email'])) {
			$this->email = $data['email'];
		}
		if (isset($data['additional'])) {
			$this->additional = $data['additional'];
		}
	}

	public function getHtml() {
		return "<p>$this->additional</p><p>This is an invite to jack.</p>";
	}

}

