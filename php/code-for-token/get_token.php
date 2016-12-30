<?php
header('Content-Type: application/json');
require_once("start_session.php");
 
if (!empty($_SESSION['access_token'])) {
    echo json_encode(array(
        "access_token" => $_SESSION['access_token'],
        "expires" => $_SESSION['expires']
    ));
} else {
    echo json_encode(array("access_token" => null));
}
?>
