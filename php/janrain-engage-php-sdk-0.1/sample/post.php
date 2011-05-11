<?php
ob_start();
$post_fields = array();
$post_fields['comment'] = '';
session_name('engage');
session_start();
$incoming_nonce = strip_tags(urldecode($_GET['nonce']));
if ($incoming_nonce == $_SESSION['nonce'] && !empty($_SESSION['nonce'])) {
  $_SESSION['nonce'] = '';
  foreach ($post_fields as $key=>$var){
    if ( !empty($_GET[$key]) ) {
      $post_fields[$key] = strip_tags(urldecode($_GET[$key]));
    }
  }
  $stat = 'ok';
  $_SESSION['post_data'] = $post_fields;
} else {
  $stat = 'fail';
  $_SESSION['post_data'] = '';
}
ob_end_clean();
echo json_encode(array('stat'=>$stat));
exit;
?>
