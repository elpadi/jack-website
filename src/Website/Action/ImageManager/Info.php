<?php
namespace Website\Action\ImageManager;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Jack\Images\Manager;
use Jack\Images\Image;

class Info extends Index {

	protected function templatePath() {
		throw new \BadMethodCallException("This method must not be called.");
	}

	protected function fetchData($args) {
		$img = new Image('/'.$_GET['path']);
		$image = (new Imagine())->open(PUBLIC_ROOT_DIR.$img->path);
		$dims = $image->getSize();
		$img->setMeta(['width' => $dims->getWidth(), 'height' => $dims->getHeight()]);
		$meta = json_decode(file_get_contents(JACK_DIR.'/cache/image-meta.json'));
		$hash = Manager::generateHash($img->path, $dims->getWidth(), $dims->getHeight());
		var_dump($img, $hash, isset($meta->$hash));
		foreach (Image::$_sizes as $size => $length) {
			$resized = $img->resizedDims($size);
			$hash = Manager::generateHash($img->path, $resized->getWidth(), $resized->getHeight());
			if (file_exists(Manager::hashToPath($hash, 'jpg'))) var_dump($size, $resized, $hash);
		}
		exit(0);
	}

}
