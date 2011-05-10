<?php
ob_start();
session_name('engage');
session_start();
$_SESSION = array();
$stat = 'ok';
ob_end_clean();
echo json_encode(array('stat'=>$stat));
exit;
?>
