<?php
namespace Website;

use Jack\Propel\PosterQuery;
use Jack\Propel\Poster as PropelModel;

class Poster {

	public static function handleSubmission($data) {
		$poster = Model::create('poster');
		$poster->setPage($data['page']);
		$poster->setFace($data['face']);
		$poster->setOrientation($data['orientation']);
		$poster->setRow($data['row']);
		$poster->setCol($data['column']);
		$poster->setLayout(Model::byId('layout', $data['layout_id']));
		$poster->save();
	}

}
