<?php
ob_start();
$nonce = uniqid('engage_',true);
session_name('engage');
session_start();
$_SESSION['nonce'] = $nonce;
$the_nonce = array('nonce' => $nonce, 'stat'=>'ok');
ob_end_clean();
echo json_encode($the_nonce);
exit;
?>
