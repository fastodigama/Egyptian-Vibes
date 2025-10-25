<?php

include('includes/database.php');

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

function generateSku($title, $size_name, $color_name) {
    // STEP 1: Product code (first 3 letters of title)
    $product_code = strtoupper(substr(preg_replace('/[^A-Z]/', '', $title), 0, 3));
    
    // STEP 2: Color code (first 3 letters of color)
    $color_code = strtoupper(substr(preg_replace('/[^A-Z]/', '', $color_name), 0, 3));
    
    // STEP 3: Size code (1-3 letters)
    $size_code = strtoupper(preg_replace('/[^A-Z0-9]/', '', $size_name));
    if (strlen($size_code) > 3) {
        $size_code = substr($size_code, 0, 3);
    }
    
    // STEP 4: Base SKU (without number)
    $base_sku = $product_code . '-' . $color_code . '-' . $size_code;
    
    // STEP 5: Add unique number (001, 002, 003...)
    $number = 1;
    $sku = $base_sku . '-001';
    
    // Check if SKU already exists in database
    global $connect;
    if (isset($connect) && $connect) {
        while (true) {
            $check_sku = mysqli_real_escape_string($connect, $sku);
            $result = mysqli_query($connect, "SELECT COUNT(*) as count FROM product_variants WHERE sku = '$check_sku'");
            $row = mysqli_fetch_assoc($result ?? []);
            
            if (!$row || $row['count'] == 0) {
                break;
            }
            
            $number++;
            $sku = $base_sku . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
    }
    
    return $sku;
}

function generateSkuWithIds($title, $size_id, $color_id) {
    // NO DATABASE QUERIES! Uses IDs directly
    
    $product_code = strtoupper(substr(preg_replace('/[^A-Z]/', '', $title), 0, 3));
    $color_code = 'C' . str_pad($color_id, 2, '0', STR_PAD_LEFT);  // C01, C02...
    $size_code = 'S' . str_pad($size_id, 2, '0', STR_PAD_LEFT);    // S01, S02...
    
    $base_sku = $product_code . '-' . $color_code . '-' . $size_code;
    
    // Make unique
    global $connect;
    $number = 1;
    $sku = $base_sku . '-001';
    
    if (isset($connect) && $connect) {
        while (true) {
            $check_sku = mysqli_real_escape_string($connect, $sku);
            $result = mysqli_query($connect, "SELECT COUNT(*) as count FROM product_variants WHERE sku = '$check_sku'");
            $row = mysqli_fetch_assoc($result ?? []);
            
            if (!$row || $row['count'] == 0) {
                break;
            }
            
            $number++;
            $sku = $base_sku . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
        }
    }
    
    return $sku;
}

//image resize function (unchanged)
function resizeImageToBase64($filePath, $maxWidth = 800, $maxHeight = 800, $jpegQuality = 80, $pngCompression = 7, $webpQuality = 80) {
    list($origWidth, $origHeight, $imageType) = getimagesize($filePath);

    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth  = (int)($origWidth * $ratio);
    $newHeight = (int)($origHeight * $ratio);

    switch ($imageType) {
        case IMAGETYPE_JPEG: $src = imagecreatefromjpeg($filePath); break;
        case IMAGETYPE_PNG:  $src = imagecreatefrompng($filePath); break;
        case IMAGETYPE_GIF:  $src = imagecreatefromgif($filePath); break;
        case IMAGETYPE_WEBP: $src = imagecreatefromwebp($filePath); break;
        default: return false;
    }

    $dst = imagecreatetruecolor($newWidth, $newHeight);

    if (in_array($imageType, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP])) {
        imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    ob_start();
    switch ($imageType) {
        case IMAGETYPE_JPEG: imagejpeg($dst, null, $jpegQuality); break;
        case IMAGETYPE_PNG:  imagepng($dst, null, $pngCompression); break;
        case IMAGETYPE_GIF:  imagegif($dst); break;
        case IMAGETYPE_WEBP: imagewebp($dst, null, $webpQuality); break;
    }
    $imageData = ob_get_clean();

    imagedestroy($src);
    imagedestroy($dst);

    $mime = image_type_to_mime_type($imageType);
    return 'data:' . $mime . ';base64,' . base64_encode($imageData);
}

?>