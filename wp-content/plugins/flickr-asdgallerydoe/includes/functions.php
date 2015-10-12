<?php
/**
 * image resizing starts here
 * 
 * @param type $arr
 */
function resizeImg($arr) {
	// name of the file here
	$date = md5(time());
	// upload image and resize
	$uploaddir = $arr['uploaddir'];
	$tempdir = $arr['tempdir'];
	$temp_name = $_FILES['afgyourfile']['tmp_name'];
	$img_parts = pathinfo($_FILES['afgyourfile']['name']);
	$new_name = strtolower($date . '.' . $img_parts['extension']);
	$ext = strtolower($img_parts['extension']);
	$allowed_ext = array('gif', 'jpg', 'jpeg', 'png');
    $expire = gmdate('D, d M Y H:i:s \G\M\T', time() - 84600);
    header("Expires: ".$expire);
    echo '<html><head><meta http-Equiv="Cache-Control" Content="no-cache" /><meta http-Equiv="Pragma" Content="no-cache" /><meta http-Equiv="Expires" Content="'. $expire .'" /><body onload="window.parent.uploadDone();">';
	if (!in_array($ext, $allowed_ext)) {
		echo '<p class="uperror">' . $arr['fileError'] . '</p>';
		exit;
	}
	$temp_uploadfile = $tempdir . $new_name;
	$new_uploadfile = $uploaddir . $new_name;
	// less than 3MB default
	if ($_FILES['afgyourfile']['size'] < $arr['maxfilesize']) {
		if (move_uploaded_file($temp_name, $temp_uploadfile)) {
			// Check EXIF if jpeg

			if ($ext === 'jpg' || $ext === 'jpeg') {
				$arr['orientation'] = checkExifOrientation($temp_uploadfile);
			} else {
				$arr['orientation'] = 1;
			}
			// add key value to arr
			$arr['temp_uploadfile'] = $temp_uploadfile;
			$arr['new_uploadfile'] = $new_uploadfile;
			wideimageImg($arr);
			unlink($temp_uploadfile);
		}
	} else {
		echo '<p class="uperror">' . $arr['sizeError'] . '</p>';
	}
    echo '</body></html>';
    exit;    
}

/**
 * resizing the thumb image here
 * 
 * @param type $arr
 */
function resizeThumb($arr) {
	$filename = 'img_thumb_' . uniqid() . '_' . time() . '.png';
	$arr['temp_uploadfile'] = $arr['img_src'];
	$arr['new_uploadfile'] = $arr['uploaddir'] . $filename;
	wideimageImg($arr);
    
	exit;
}

/**
 * Check the EXIF orientation tag
 * 
 * @param type $target
 * @return int
 */
function checkExifOrientation($target) {
	$exif = exif_read_data($target);
	if (isset($exif['Orientation']) && $exif['Orientation'] != '') {
		return $exif['Orientation'];
	} else {
		return 1;
	}
}

/**
 * convert image with wideimage library
 * 
 * @param type $arr
 */
function wideimageImg($arr) {

    //file_put_contents(dirname(dirname(__FILE__)).'/file.log', '');
	include('lib/wideimage-11.02.19/WideImage.php');
	$wideImage = new WideImage();

	$height = $arr['height'];
	$width = $arr['width'];
	$x = $arr['x'];
	$y = $arr['y'];
	$bigWidth = $arr['bigWidthPrev'];
	$bigHeight = $arr['bigHeightPrev'];
	//$tempfileRotate = 'uploads/temp/' . 'img_' . uniqid() . '_temp_' . time() . '.png';
    
    
    //$content = print_r($arr, true);
    //file_put_contents(dirname(dirname(__FILE__)).'/file.log', $content, FILE_APPEND); 
	// load the image
	//$workingImg = $wideImage->load($arr['temp_uploadfile']);

	// background color for canvas
	//$bg_color = $workingImg->allocateColor($arr['canvasbg']['r'], $arr['canvasbg']['g'], $arr['canvasbg']['b']);

	// fit and add white frame										
    /*
	if ($arr['thumb'] === true) {
		//$workingImg = $workingImg->crop($x, $y, $width, $height)->resize($bigWidth, $bigHeight, 'inside')->resizeCanvas($bigWidth, $bigHeight, 'center', 'center', $bg_color);
		$workingImg = $workingImg->crop($x, $y, $width, $height)->resize($bigWidth, $bigHeight, 'inside');        
	} else {
    */
		// rotate the image if it is portrait

    switch ($arr['orientation']) {
        case 1: // nothing
            $workingImg = $wideImage->load($arr['temp_uploadfile'])->resize($width, $height, 'inside')->saveToFile($arr['new_uploadfile']);
        case 2: // horizontal flip
            $workingImg = $wideImage->load($arr['temp_uploadfile'])->resize($width, $height, 'inside')->mirror()->saveToFile($arr['new_uploadfile']);
        case 3: // 180 rotate left
            $tempi1 = $workingImg->rotate(180);
            // fix for rotated images
            $tempi1->saveToFile($tempfileRotate);
            $workingImg = \WideImage::load($tempfileRotate);
            break;
        case 4: // vertical flip
            break;
        case 5: // vertical flip + 90 rotate right
            break;
        case 6: // 90 rotate right
            $tempi1 = $workingImg->rotate(90);
            // fix for rotated images
            $tempi1->saveToFile($tempfileRotate);
            $workingImg = \WideImage::load($tempfileRotate);
            break;
        case 7: // horizontal flip + 90 rotate right
            break;
        case 8:	// 90 rotate left
            $tempi1 = $workingImg->rotate(-90);
            // fix for rotated images
            $tempi1->saveToFile($tempfileRotate);
            $workingImg = \WideImage::load($tempfileRotate);
            break;
    }

    //$workingImg = $workingImg->resize($width, $height, 'inside')->resizeCanvas($width, $height, 'center', 'center', $bg_color);
    //$workingImg = $workingImg->resize($width, $height, 'inside');
    //$workingImg->resize($width, $height, 'inside')->saveToFile($arr['new_uploadfile']);
	//}
	

	// always convert to jpg	
	//$workingImg->saveToFile($arr['new_uploadfile']);
	
	$data = array(
		'photo' => $arr['new_uploadfile']
	);
    
    //$content = print_r($data, true);
    //file_put_contents(dirname(dirname(__FILE__)).'/file.log', $content, FILE_APPEND);     
	// echo $user_id;
	// delete old file
	echo $data['photo'];    
}
