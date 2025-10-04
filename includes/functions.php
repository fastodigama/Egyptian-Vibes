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


?>
