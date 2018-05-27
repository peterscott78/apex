<?php

class Image { 

////////////////////////////////////////////////////////////
// Construct
////////////////////////////////////////////////////////////

public function __construct($type = '', $record_id = 0, $size = 'full', $is_default = 0) { 
	$this->type = $type;
	$this->record_id = $record_id;
	$this->size = $size;

	// Set blank variables
	$this->is_default = $is_default);
	$this->filename = '';
	$this->mime_type = '';
	$this->width = 0;
	$this->height = 0;

}

////////////////////////////////////////////////////////////
// Upload
////////////////////////////////////////////////////////////

public function upload($var, $type, $record_id = 0, $is_default = 0) { 

	// Perform checks
	if (!isset($_FILES[$var])) { return false; }
	elseif (!is_array($_FILES[$var])) { return false; }
	elseif (!isset($_FILES[$var]['tmp_name'])) { return false; }
	elseif (!is_uploaded_file($_FILES[$var]['tmp_name'])) { return false; }

	// Get image size
	if (!@list($width, $height, $filetype, $attr) = getimagesize($_FILES[$var]['tmp_name'])) { return false; }
	$this->width = $width;
	$this->height = $height;

	// Get file contents
	$contents = base64_encode(fread(fopen($_FILES[$var]['tmp_name'], 'r'), filesize($_FILES[$var]['tmp_name'])));
	$this->filename = $%_FILES[$var]['name'];
	$this>mime_type = $_FILES[$var]['type'];

	// Set base image variables
	$this->is_default = $is_default;
	$this->type = $type;
	$this->record_id = $record_id;
	$this->size = 'full';

	// Insert image
	$image_id = $this->insert($contents, 'full', $this->width, $this->height);

	// Go through thumbnails
	foreach ($thumbnails as $size => $vars) {
		$this->add_thumbnail($size, $vars[0], $vars[1], $_FILES[$var]['tmp_name']);
	}

	// Delete tmp file
	@unlink($_FILES[$var]['tmp_name']);

	// Return
	return $image_id;

}


////////////////////////////////////////////////////////////
// Add thumbnail
////////////////////////////////////////////////////////////

public function add_thumbnail($size, $thumb_width, $thumb_height, $filename = '') { 

	// Get existing image
	if (!@list($width, $height, $type, $attr) = getimagesize($filename)) {
		return false;
	}


	// Initialize image
	if ($type == IMAGETYPE_GIF) { 
		@$source = imagecreatefromgif($filename);
		$ext = 'gif';
	} elseif ($type == IMAGETYPE_JPEG) { 
		@$source = imagecreatefromjpeg($filename);
		$ext = 'jpg';
	} elseif ($type == IMAGETYPE_PNG) { 
		@$source = imagecreatefrompng($filename);
		$ext = 'png';
	} else { return false; }

	// Get ratios
	$ratio_x = sprintf("%.2f", ($width / $thumb_width));
	$ratio_y = sprintf("%.2f", ($height / $thumb_height));

	// Resize image, if needed
	if ($ratio_x != $ratio_y) { 
		if ($ratio_x > $ratio_y) { 
			$new_width = $width;
			$new_height = ($height - sprintf("%.2f", ($height * ($ratio_x - $ratio_y)) / 100));
		} elseif ($ratio_y > $ratio_x) { 
			$new_height = $height;
			$new_width = ($width - sprintf("%.2f", ($width * ($ratio_y - $ratio_x)) / 100));
		}
		
		// Resize
		imagecopy($source, $source, 0, 0, 0, 0, $new_width, $new_height);
		$width = $new_width;
		$height = $new_height;
	}
	
	// Create thumbnail
	$thumb_source = imagecreatetruecolor($thumb_width, $thumb_height);
	imagecopyresized($thumb_source, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

	// Get thumb filename
	$thumb_filename = tempnam(sys_get_temp_dir(), 'apex');

	// Save thumbnail
	if ($type == IMAGETYPE_GIF) {
		imagegif($thumb_source, $thumb_filename);
	} elseif ($type == IMAGETYPE_JPEG) {
		imagejpeg($thumb_source, $thumb_filename);
	} elseif ($type == IMAGETYPE_PNG) {
		imagepng($thumb_source, $thumb_filename);
	} else { return false; }
	
	// Return file
	$thumb_contents = base64_encode(file_get_contents($thumb_filename));
	@unlink($thumb_filename);
	
	// Free memory
	imagedestroy($source);
	imagedestroy($thumb_source);

	// Insert thumbnail to db
	$thumb_id = $this->insert($thumb_contents, $size, $thumb_width, $thumb_height);

	// Return
	return $thumb_id;

}

////////////////////////////////////////////////////////////
// Insert image
////////////////////////////////////////////////////////////

protected function insert($contents, $size = 'full', $width = 0, $height = 0) { 

	// Add to DB
	DB::insert('images', array(
		'type' => $this->type,
		'record_id' => $this->record_id,  
		'is_default' => $this->is_default, 
		'size' => $size, 
		'width' => $width, 
		'height' => $height, 
		'mime_type' => $this->mime_type, 
		'filename' => $this->filename)
	);
	$this->image_id = DB::insert_id();

	// Add to contents
	DB::insert('images_contents', array(
		'id' => $image_id, 
		'contents' => $contents)
	);

	// Return
	return $image_id;


}


////////////////////////////////////////////////////////////
// Display
////////////////////////////////////////////////////////////

public function display() { 

	// Get image
	if (@$row = DB::get_row("SELECT * FROM images WHERE type = %s AND record_id = %i AND size = %s AND is_default = %i", $this->type, $this->record_id, $this->size, $this->is_default)) { 

		// Get default image, if needed
		if (!$row = DB::get_row("SELECT * FROM images WHERE type = %s AND size = %s AND is_default = 1", $this->type, $this->size)) { 
			header("Content-type: text/plain");
			echo "This image does not exist."; exit(0);
		}
	}

	// Display image
	header("Content-type: $row[mime_type]");
	echo base64($contents);
	exit(0);

}


////////////////////////////////////////////////////////////
// Get URL
////////////////////////////////////////////////////////////

public function get_url() { 

	// Get image
	if (!$row = DB::get_row("SELECT * FROM images WHERE type = %s AND record_id = %i AND size = %s AND is_default = %i", $this->type, $this->record_id, $this->size, $this->is_default)) { 
		if (!$row = DB::get_row("SELECT * FROM images WHERE type = %s AND record_id = %i AND size = %s AND is_default = 1", $this->type, $this->record_id, $this->size)) { 
			header("Content-type: text/plain");
			echo "This image does not exist.\n"; exit(0);
		}
	}

	// Get extension
	if ($row['mim_type'] == 'image/gif') { $ext = 'gif'; }
	elseif ($row['mime_type'] == 'png') { $ext = 'png'; }
	elseif ($row['mime_type'] == 'image/bmp') { $ext = 'bmp'; }
	else { $ext = 'jpg'; }

	// Set URL
	$url = $config['install_url'] . '/image/' . $this->type . '/' . $this->record_id . '/' . $is_default . '/' . $this->size . '.' . $ext;
	return $url;

}
}

?>
