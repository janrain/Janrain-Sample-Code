<?php
session_name('engage');
session_start();
echo "Session Save Path: " . ini_get( 'session.save_path') . "<br>\nSession:<pre>";
var_dump($_SESSION);
?>
