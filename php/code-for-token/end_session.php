<?php
header('Content-Type: application/json');
session_start();
 
$_SESSION = array();
session_destroy();
?>
