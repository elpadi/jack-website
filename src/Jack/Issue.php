<?php
namespace Jack;

class Issue {

	public $id;
	public $slug;
	public $title;
	public $posters;

	public $covers = array();
	public $centerfold = array();

	protected $imagine;

	const PAGE_WIDTH = 450;
	const PAGE_HEIGHT = 600;
	
	const THUMB_HEIGHT = 100;

	public function hydrate(AssetManager $assets) {
		if (empty($this->covers)) {
			$this->covers = array(
				'front' => $assets->asset("issues/$this->slug/covers/front.jpg"),
				'back' => $assets->asset("issues/$this->slug/covers/back.jpg"),
				'index' => $assets->asset("issues/$this->slug/covers/index.jpg"),
				'thumb' => $assets->asset("issues/$this->slug/covers/thumb.jpg"),
				'poster' => $assets->asset("issues/$this->slug/cover-poster/original.jpg"),
				'poster_left' => $assets->asset("issues/$this->slug/cover-poster/left.jpg"),
				'poster_middle' => $assets->asset("issues/$this->slug/cover-poster/middle.jpg"),
				'poster_right' => $assets->asset("issues/$this->slug/cover-poster/right.jpg"),
				'poster_thumb' => $assets->asset("issues/$this->slug/cover-poster/thumb.jpg"),
			);
		}
		if (empty($this->centerfold)) {
			$this->centerfold = array(
				'front' => $assets->asset("issues/$this->slug/centerfold/front/original.jpg"),
				'front_top_left' => $assets->asset("issues/$this->slug/centerfold/front/top-left.jpg"),
				'front_top_right' => $assets->asset("issues/$this->slug/centerfold/front/top-right.jpg"),
				'front_bottom_left' => $assets->asset("issues/$this->slug/centerfold/front/bottom-left.jpg"),
				'front_bottom_right' => $assets->asset("issues/$this->slug/centerfold/front/bottom-right.jpg"),
				'front_thumb' => $assets->asset("issues/$this->slug/centerfold/front/thumb.jpg"),
				'back' => $assets->asset("issues/$this->slug/centerfold/back/original.jpg"),
				'back_top_left' => $assets->asset("issues/$this->slug/centerfold/back/top-left.jpg"),
				'back_top_right' => $assets->asset("issues/$this->slug/centerfold/back/top-right.jpg"),
				'back_bottom_left' => $assets->asset("issues/$this->slug/centerfold/back/bottom-left.jpg"),
				'back_bottom_right' => $assets->asset("issues/$this->slug/centerfold/back/bottom-right.jpg"),
				'back_thumb' => $assets->asset("issues/$this->slug/centerfold/back/thumb.jpg"),
			);
		}
	}

	public function updatePosterOrder($data, DbAccess $db) {
		$sth = $db->prepare("UPDATE `".$db->table("issue_posters")."` SET `sort_order`=? WHERE `issue_id`=$this->id AND `poster_id`=?");
		$order = 1;
		for ($i = 0; $i < count($data) / 2; $i++) {
			$sth->execute(array($order, $data['row'.$i.'_front']));
			$order++;
			$sth->execute(array($order, $data['row'.$i.'_back']));
			$order++;
		}
	}
	
	public function update($data, $files, AssetManager $assets) {
		$this->imagine = new \Imagine\Gd\Imagine();
		foreach ($files as $key => $info) {
			if (strpos($info['type'], 'image/jpeg') !== 0) {
				throw new \InvalidArgumentException("The file sent is not a valid JPEG image.");
			}
			call_user_func(array($this, "update$key"), $info['tmp_name'], $assets);
		}
	}
	
	public function updateFrontCover($imagePath, AssetManager $assets) {
		$this->updateCoverImage('front', $imagePath, $assets);
	}

	public function updateBackCover($imagePath, AssetManager $assets) {
		$this->updateCoverImage('back', $imagePath, $assets);
	}

	public function updateIndex($imagePath, AssetManager $assets) {
		$this->updateCoverImage('index', $imagePath, $assets);
	}

	public function updateCoverPoster($imagePath, AssetManager $assets) {
		$imageBox = new Box(self::PAGE_WIDTH * 3, self::PAGE_HEIGHT);
		$image = $this->updateImage("cover-poster/original", $imageBox, $imagePath, $assets);
		$pageBox = new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT);
		$base = $assets->basePath()."/issues/$this->slug/cover-poster";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$image->copy()->crop(new Point(0, 0), $pageBox)->flipHorizontally()->save($path("left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, 0), $pageBox)->save($path("middle"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH * 2, 0), $pageBox)->flipHorizontally()->save($path("right"));
		$image->resize($imageBox->heighten(self::THUMB_HEIGHT))->save($path("thumb"));
		$this->covers["poster"] = $assets->asset($path("original"));
	}

	public function updateFrontCenterfold($imagePath, AssetManager $assets) {
		$imageBox = new Box(self::PAGE_WIDTH * 2, self::PAGE_HEIGHT * 2);
		$image = $this->updateImage("centerfold/front/original", $imageBox, $imagePath, $assets);
		$pageBox = new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT);
		$base = $assets->basePath()."/issues/$this->slug/centerfold/front";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$image->copy()->crop(new Point(0, 0), $pageBox)->save($path("top-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, 0), $pageBox)->save($path("top-right"));
		$image->copy()->crop(new Point(0, self::PAGE_HEIGHT), $pageBox)->save($path("bottom-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, self::PAGE_HEIGHT), $pageBox)->save($path("bottom-right"));
		$image->resize($imageBox->heighten(self::THUMB_HEIGHT * 2))->save($path("thumb"));
		$this->centerfold["front"] = $assets->asset($path("original"));
	}

	public function updateBackCenterfold($imagePath, AssetManager $assets) {
		$imageBox = new Box(self::PAGE_WIDTH * 2, self::PAGE_HEIGHT * 2);
		$image = $this->updateImage("centerfold/back/original", $imageBox, $imagePath, $assets);
		$pageBox = new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT);
		$base = $assets->basePath()."/issues/$this->slug/centerfold/back";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		$image->copy()->crop(new Point(0, 0), $pageBox)->flipHorizontally()->save($path("top-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, 0), $pageBox)->flipHorizontally()->save($path("top-right"));
		$image->copy()->crop(new Point(0, self::PAGE_HEIGHT), $pageBox)->flipHorizontally()->save($path("bottom-left"));
		$image->copy()->crop(new Point(self::PAGE_WIDTH, self::PAGE_HEIGHT), $pageBox)->flipHorizontally()->save($path("bottom-right"));
		$image->resize($imageBox->heighten(self::THUMB_HEIGHT * 2))->save($path("thumb"));
		$this->centerfold["back"] = $assets->asset($path("original"));
	}

	protected function updateImage($partialPath, Box $imageBox, $imagePath, AssetManager $assets) {
		$path = "issues/$this->slug/$partialPath.jpg";
		$image = $this->imagine->open($imagePath);
		$dims = $image->getSize();
		if ($dims->getWidth() !== $imageBox->getWidth()) {
			throw new \InvalidArgumentException("The width of the image is ".$dims->getWidth()." px, not the required ".$imageBox->getWidth()." px.");
		}
		if ($dims->getHeight() !== $imageBox->getHeight()) {
			throw new \InvalidArgumentException("The height of the image is ".$dims->getHeight()." px, not the required ".$imageBox->getHeight()." px.");
		}
		if (!move_uploaded_file($imagePath, $assets->basePath().'/'.$path)) {
			throw new \RuntimeException("Error saving the image.");
		}
		clearstatcache();
		return $image;
	}

	protected function updateCoverImage($section, $imagePath, AssetManager $assets) {
		if (!isset($this->covers[$section])) {
			throw new \InvalidArgumentException("Bad cover section '$section'.");
		}
		$this->updateImage("covers/$section", new Box(self::PAGE_WIDTH, self::PAGE_HEIGHT), $imagePath, $assets);
		$base = $assets->basePath()."/issues/$this->slug/covers";
		$path = function($part) use ($base) { return "$base/$part.jpg"; };
		
		$thumbBox = new Box(self::PAGE_WIDTH * 3, self::PAGE_HEIGHT);
		$thumb = $this->imagine->create($thumbBox);
		$thumb->paste($this->imagine->open($path("back")), new Point(0, 0));
		$thumb->paste($this->imagine->open($path("front")), new Point(self::PAGE_WIDTH, 0));
		$thumb->paste($this->imagine->open($path("index")), new Point(self::PAGE_WIDTH * 2, 0));
		$thumb->resize($thumbBox->heighten(self::THUMB_HEIGHT))->save($path("thumb"));
	
		$this->covers[$section] = $assets->asset($path($section));
	}

	public static function log($msg) {
		global $site;
		$site->getService('logger')->addDebug($msg);
	}

}

