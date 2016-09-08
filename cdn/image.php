<?php
# put the header values
header('Cache-Control: public,max-age=2592000');
header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');

# this script assumes that the images are located in a folder same as the image.php
# otherwise youy can change the path variable

#argumants: w=width h=height i=image_location_in_image_folder
$path = "image/";
$w    = 0;
if (isset($_GET["w"])) {
    $w = intval($_GET["w"]);
}
$h = 0;
if (isset($_GET["h"])) {
    $h = intval($_GET["h"]);
}

$i = $path . $_GET["i"];

# below code takes care of location of image with name containg '&; (eg: "b & c.jpg")
$idArray = explode('&', $_SERVER["QUERY_STRING"]);
foreach ($idArray as $index => $avPair) {
    $pos = strpos($avPair, "=");
    if ($pos === false) {
        $i = $i . "&" . $avPair;
    }
}

resizeImage($i, $w, $h);

function resizeImage($filename, $max_width, $max_height)
{
    list($orig_width, $orig_height) = getimagesize($filename);
    $orig_ratio = ($orig_width/$orig_height);
    $new_ratio = ($max_width/$max_height);
    
    $width  = $orig_width;
    $height = $orig_height;
    
    if (($max_width != 0) && ($max_height != 0)) {
    	if($orig_ratio > $new_ratio) {
	       $height = $max_height;
	       $width = $orig_ratio*$max_height;	
	   } else if ($orig_ratio < $new_ratio) {
	       $width=$max_width;
	       $height = $max_width/$orig_ratio;
	   }
    }
    
    $image_p = imagecreatetruecolor($width, $height);
    $etag    = md5($filename);
    header("Etag:\"$etag\"");
    $image = imagecreatefromstring(file_get_contents($filename));
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
    header('Content-type: image/jpeg');

    print imagejpeg($image_p);

    # here you can either save the resized image before destroying it, and search for it, before resizing the image
    imagedestroy($image_p);
    
}

?>

