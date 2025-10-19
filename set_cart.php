<?php

include ("frontend_includes/config.php");

include('admin/includes/database.php');


//Get product info from GET

$id = $_GET['id'];
$title = $_GET['title'];
$price = $_GET['price'];

$qty = isset($_GET['qty'])? (int) $_GET['qty'] : 1;

//get the product photo
$photoQuery = "SELECT photo from product_photos WHERE product_id = $id ORDER BY photo_id LIMIT 1";
$result = mysqli_query($connect, $photoQuery);
$photo = mysqli_fetch_assoc($result)['photo'] ?? ''; // empty string if no photo

//initialize the cart if not set

if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

//if prodyct already in cart, increase quantity
if(isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity'] += $qty;

}else{
    $_SESSION['cart'][$id] = [
        'title' => $title,
        'price' => $price,
        'quantity' => $qty,
        'photo' => $photo
    ];
}

//redirect to product page

header('Location: product_details.php?id=' .$id);
die;
