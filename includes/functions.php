<?php

function secure(){

    if(!isset($_SESSION['id'])){

        set_message("You must first login to view this page ");

        header('Location: /egyptian-vibes');
        die();
    }

}

function set_message($message){
    $_SESSION['message'] = $message;
}


function get_message(){
    if(isset($_SESSION['message'])){
        echo '<p>' . $_SESSION['message'] . '</p> 
        <hr>';
        unset($_SESSION['message']);

    }
}

function generateSKU($title) {
    // Remove spaces and special characters, take first 5 letters
    $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $title), 0, 5));

    // Add current date in YYYYMMDD format
    $date = date('Ymd');

    // Combine for SKU
    return $prefix . '-' . $date;
}
//image resize
function resizeImageToBase64($filePath, $maxWidth = 800, $maxHeight = 800, $jpegQuality = 80, $pngCompression = 7) {
    list($origWidth, $origHeight, $imageType) = getimagesize($filePath);

    // Calculate new dimensions while keeping aspect ratio
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth  = (int)($origWidth * $ratio);
    $newHeight = (int)($origHeight * $ratio);

    // Create image resource from file
    switch ($imageType) {
        case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($filePath); break;
        case IMAGETYPE_PNG:  $src = imagecreatefrompng($filePath); break;
        case IMAGETYPE_GIF:  $src = imagecreatefromgif($filePath); break;
        default: return false;
    }

    // Create a new blank image
    $dst = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG/GIF
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    // Copy and resize
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // Capture output buffer
    ob_start();
    switch ($imageType) {
        case IMAGETYPE_JPEG: imagejpeg($dst, null, $jpegQuality); break; // quality 0–100
        case IMAGETYPE_PNG:  imagepng($dst, null, $pngCompression); break; // compression 0–9
        case IMAGETYPE_GIF:  imagegif($dst); break;
    }
    $imageData = ob_get_clean();

    // Free memory
    imagedestroy($src);
    imagedestroy($dst);

    // Return base64 string
    $mime = image_type_to_mime_type($imageType);
    return 'data:' . $mime . ';base64,' . base64_encode($imageData);
}


?>
