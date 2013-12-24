<?php
namespace Jack;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Stringy\Stringy as S;

class Poster {
	
	public $id;
	public $slug;
	public $title;
	public $description;
	
	public $poster;
	public $thumb;

	public $left;
	public $right;
	public $left_flipped;
	public $right_flipped;

	public $issueId;

	public function hydrate(AssetManager $assets) {
		if (empty($this->poster)) {
			$this->poster = $assets->asset("posters/$this->id/original.jpg");
			$this->thumb = $assets->asset("posters/$this->id/thumb.jpg");
			$this->left = $assets->asset("posters/$this->id/left.jpg");
			$this->left_flipped = $assets->asset("posters/$this->id/left-flipped.jpg");
			$this->right = $assets->asset("posters/$this->id/right.jpg");
			$this->right_flipped = $assets->asset("posters/$this->id/right-flipped.jpg");
		}
	}

	protected function updateData($data, DbAccess $db) {
		if (!isset($data['id']) || !ctype_digit($data['id'])) {
			throw new \Exception("Invalid data.");
		}
		if (isset($data['title'])) {
			$data['slug'] = S::create($data['title'])->slugify();
		}
		$middleSql = "SET `title`=?, `slug`=?, `description`=? ";
		if ($data['id'] !== '0') {
			$sql = "UPDATE `".$db->table("posters")."` ".$middleSql."WHERE `id`=".intval($this->id);
			$db->query($sql, array($data['title'], $data['slug'], $data['description']));
		}
		else {
			$sql = "INSERT INTO `".$db->table("posters")."` ".$middleSql;
			$db->query($sql, array($data['title'], $data['slug'], $data['description']));
			$this->id = $db->lastInsertId();
			$sql = "INSERT INTO `".$db->table("issue_posters")."` SET `issue_id`=?, `poster_id`=?";
			$db->query($sql, array($this->issueId, $this->id));
		}
		foreach (array('title','slug','description') as $key) {
			$this->$key = $data[$key];
		}
	}

	protected function updatePoster($info, AssetManager $assets) {
		if (strpos($info['type'], 'image/jpeg') !== 0) {
			throw new \InvalidArgumentException("The file sent is not a valid JPEG image.");
		}
		$base = $assets->basePath()."/posters/$this->id";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$imageBox = new Box(Issue::PAGE_WIDTH * 2, Issue::PAGE_HEIGHT);
		$pageBox = new Box(Issue::PAGE_WIDTH, Issue::PAGE_HEIGHT);
		$image = $this->imagine->open($info['tmp_name']);
		$dims = $image->getSize();
		if ($dims->getWidth() !== $imageBox->getWidth()) {
			throw new \InvalidArgumentException("The width of the image is ".$dims->getWidth()." px, not the required ".$imageBox->getWidth()." px.");
		}
		if ($dims->getHeight() !== $imageBox->getHeight()) {
			throw new \InvalidArgumentException("The height of the image is ".$dims->getHeight()." px, not the required ".$imageBox->getHeight()." px.");
		}
		if (!move_uploaded_file($info['tmp_name'], $path("original"))) {
			throw new \RuntimeException("Error saving the image.");
		}
		clearstatcache();
		$image->copy()->crop(new Point(0, 0), $pageBox)->save($path("left"));
		$image->copy()->crop(new Point(0, 0), $pageBox)->flipHorizontally()->save($path("left-flipped"));
		$image->copy()->crop(new Point(Issue::PAGE_WIDTH, 0), $pageBox)->save($path("right"));
		$image->copy()->crop(new Point(Issue::PAGE_WIDTH, 0), $pageBox)->flipHorizontally()->save($path("right-flipped"));
		$image->resize($imageBox->heighten(Issue::THUMB_HEIGHT))->save($path("thumb"));
		$this->poster = $assets->asset($path("original"));
	}

	public function update($data, $files, AssetManager $assets, DbAccess $db) {
		if (!empty($data)) {
			$this->updateData($data, $db);
			if ($data['id'] === '0') {
				$path = $assets->basePath()."/posters/$this->id";
				!is_dir($path) && mkdir($path);
			}
		}
		if (!empty($files) && isset($files['poster'])) {
			$this->imagine = new \Imagine\Gd\Imagine();
			$this->updatePoster($files['poster'], $assets);
		}
	}

	public function delete(AssetManager $assets, DbAccess $db) {
			delTree($assets->basePath()."/posters/$this->id");
			$db->query("DELETE FROM `".$db->table("posters")."` WHERE `id`=?", array($this->id));
			$db->query("DELETE FROM `".$db->table("issue_posters")."` WHERE `poster_id`=?", array($this->id));
	}

}

