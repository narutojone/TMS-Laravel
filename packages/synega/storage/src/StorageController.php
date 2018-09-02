<?php

namespace Synega\Storage;

use Illuminate\Routing\Controller as BaseController;
use Synega\Storage\Facades\FileVault;

class StorageController extends BaseController {

	public function download($id) {
		return FileVault::download($id);
	}
}