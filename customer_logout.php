<?php

include ('frontend_includes\config.php');
session_destroy();
header('Location: index.php');
die;
?>