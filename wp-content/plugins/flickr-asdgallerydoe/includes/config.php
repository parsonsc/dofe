<?php
// factor for the real size of the uploaded image
$sizefactor = 3;

// size of the big, preview and thumb container
$bigWidthPrev = 530;
$bigHeightPrev = 530;

// canvas size for the uploaded image
$canvasWidth = $bigWidthPrev * $sizefactor;
$canvasHeight = $bigHeightPrev * $sizefactor;

// file type error
$fileError = 'Filetype not allowed. Please upload again. Only GIF, JPG and PNG files are allowed.';
$sizeError = 'File is too big. Please upload again. Maximum filesize is '.getMaxUploadSize();

// image upload folders
$imgthumb = 'uploads/ready/'; // folder for the uploads after cropping
$imgtemp = 'uploads/temp/'; // temp-folder before cropping
$imgbig = 'uploads/big/'; // folder with big uploaded images

// max file-size for upload in bytes, default: 3mb
$maxuploadfilesize = 5120000;

// background color of the canvas as rgb, default:white
$canvasbg = array(
	'r' => 255,
	'g' => 255,
	'b' => 255
);

function getMaxUploadSize(){
    $max_upload    = (ini_get('upload_max_filesize'));
    $max_post      = (ini_get('post_max_size'));
    $memory_limit  = (ini_get('memory_limit'));
    $size = min($max_upload, $max_post, $memory_limit); 
    if (preg_match('/^(\d+)(.)$/', $size, $matches)) {
        if ($matches[2] == 'M') {
            $size = $matches[1] .'MB'; // nnnM -> nnn MB
        } else if ($matches[2] == 'K') {
            $size = $matches[1] .'kB'; // nnnK -> nnn KB
        }
    }    
    return $size;   
}
?>