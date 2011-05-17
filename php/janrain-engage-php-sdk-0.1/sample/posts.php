<?php
ob_start();
$provider_map = array();
$provider_map['Facebook'] = 'facebook';
$provider_map['Google'] = 'google';
$provider_map['GoogleApps'] = 'google';
$provider_map['LinkedIn'] = 'linkedin';
$provider_map['MySpace'] = 'myspace';
$provider_map['Twitter'] = 'twitter';
$provider_map['Windows Live'] = 'live_id';
$provider_map['Yahoo!'] = 'yahoo';
$provider_map['AOL'] = 'aol';
$provider_map['Blogger'] = 'blogger';
$provider_map['Flickr'] = 'flickr';
$provider_map['Hyves'] = 'hyves';
$provider_map['LiveJournal'] = 'livejournal';
$provider_map['MyOpenID'] = 'myopenid';
$provider_map['Netlog'] = 'netlog';
$provider_map['OpenID'] = 'openid';
$provider_map['Verisign'] = 'verisign';
$provider_map['Wordpress'] = 'wordpress';
$provider_map['PayPal'] = 'paypal';
$provider_map['Orkut'] = 'orkut';
$provider_map['VZN'] = 'vzn';
$provider_map['Salesforce'] = 'salesforce';
$provider_map['Foursquare'] = 'foursquare';
$debug = array();
$stat = 'ok';
session_name('engage');
session_start();
$session = $_SESSION;
if ( class_exists('SQLite3') ) {
  $sqdb = new SQLite3('demo.sqlite');
  if ( $sqdb != false ){
    $query = 'SELECT users.user_name, users.profile_url, user_posts.comment, user_posts.id, user_map.provider '
    .'FROM user_posts JOIN users ON users.id = user_posts.user_id JOIN user_map ON users.id = user_map.id ORDER BY user_posts.id DESC';
    $db_posts = $sqdb->query($query);
    if (!$db_posts) {
      $stat = 'fail';
    } else {
      $posts = array();
      while ($row = $db_posts->fetchArray(SQLITE3_ASSOC)) {
        if ( !empty($row['provider']) ) {
          $row['provider'] = $provider_map[$row['provider']];
        }
        if ( !empty($row['comment']) && preg_replace('/\s+/', '', $row['comment']) != '' ) {
          $posts[] = $row;
        }
      }
    }
  }
}

if ( empty($posts) && !empty($_SESSION['post_data']['comment']) ) {
  $posts = array();
  $posts[] = array(
  'comment' => $_SESSION['post_data']['comment'], 
  'profile_url' => $_SESSION['user_data']['profile_url'], 
  'user_name' => $_SESSION['user_data']['user_name'], 
  'provider' => $provider_map[$_SESSION['user_data']['provider']]);
}
$out_array = array('posts'=>$posts,'stat'=>$stat);
if ( !empty($debug) ) {
  $out_array['debug'] = $debug;
}
$json = json_encode($out_array);
ob_end_clean();
echo $json;
?>
