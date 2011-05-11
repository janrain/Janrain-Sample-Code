<?php
ob_start();
$debug = array();
$stat = 'ok';
session_name('engage');
session_start();
$session = $_SESSION;
if ( class_exists('SQLite3') ) {
  $sqdb = new SQLite3('demo.sqlite');
  $query = 'SELECT users.user_name, users.profile_url, user_posts.comment FROM user_posts JOIN users ON users.id = user_posts.user_id';
  $db_posts = $sqdb->query($query);
  if (!$db_posts) {
    $stat = 'fail';
  } else {
    $posts = array();
    while ($row = $db_posts->fetchArray(SQLITE3_ASSOC)) {
      $posts[] = $row;
    }
  }
}
$out_array = array('posts'=>$posts,'stat'=>$stat);
if ( !empty($debug) ) {
  $out_array['debug'] = $debug;
}
$json = json_encode($out_array);
ob_end_clean();
echo $json;
?>
