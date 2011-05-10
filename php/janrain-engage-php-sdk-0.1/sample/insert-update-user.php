<?php
ob_start();
$debug = array();
$stat = 'ok';
session_name('engage');
@session_start();
$session = $_SESSION;
if ( !empty($session['authinfo']['profile']['identifier']) && function_exists('SQLite3') ) {
  $user = array();
  $user['user_name'] = '';
  $user['first_name'] = '';
  $user['last_name'] = '';
  $user['email'] = '';
  $user['profile_url'] = '';
  $user['phone'] = '';
  $user['company'] = '';
  foreach ($user as $key=>$val) {
    if ( !empty($session['user_data'][$key]) ) {
      $user[$key] = addslashes($session['user_data'][$key]);
    }
  }
  $user['identifier'] = addslashes($session['authinfo']['profile']['identifier']);

  $sqdb = new SQLite3('demo.sqlite');
  $sqdb->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, user_name STRING, first_name STRING, last_name STRING, email STRING, profile_url STRING, phone STRING, company STRING)');
  $sqdb->exec('CREATE TABLE IF NOT EXISTS user_map (id INTEGER NOT NULL, identifier STRING)');

  $identifier = $sqdb->querySingle('SELECT id FROM user_map WHERE identifier = \''.$user['identifier'].'\'');
  if ( empty($identifier) ) {
    $query = 'INSERT INTO users (user_name) VALUES (\'\')';
    $insert = $sqdb->exec($query);
    if (!$insert) {
      $stat = 'fail';
      $debug[] = $query;
      $debug[] = $sqdb->lastErrorMsg();
    }
    $user['id'] = $sqdb->lastInsertRowID();
    $query = 'INSERT INTO user_map (id, identifier) VALUES (\''.$user['id'].'\',\''.$user['identifier'].'\')';
    $insert = $sqdb->exec($query);
    if (!$insert) {
      $stat = 'fail';
      $debug[] = $query;
      $debug[] = $sqdb->lastErrorMsg();
    }
  } else {
    $user['id'] = $identifier;
  }
  if ( !empty($user['id']) && $stat == 'ok' ) {
    $query = 'SELECT * FROM users WHERE id = \''.$user['id'].'\'';
    $db_user = $sqdb->querySingle($query, true);
    foreach ($db_user as $key=>$val) {
      if ( empty($user[$key]) ) {
        $user[$key] = addslashes($val);
      }
    }
    $user_updates = array();
    foreach ($user as $key=>$val) {
      if ( !empty($val) && $key != 'id' && $key != 'identifier' ) {
        $user_updates[] = $key.' = \''.$val.'\'';
      }
    }
    $user_update = implode(', ', $user_updates);
    if ( !empty($user_update) ) {
      $query = 'UPDATE users SET '.$user_update.' WHERE id = \''.$user['id'].'\'';
      $update = $sqdb->exec($query);
      if (!$update) {
        $stat = 'fail';
        $debug[] = $query;
        $debug[] = $sqdb->lastErrorMsg();
      }
    }
    $query = 'SELECT * FROM users WHERE id = \''.$user['id'].'\'';
    $user_data = $sqdb->querySingle($query, true);
    $query = 'SELECT identifier FROM user_map WHERE id = \''.$user['id'].'\'';
    $map_result = $sqdb->query($query);
    $map_result->reset();
    $map_data = array();
    while ($row = $map_result->fetchArray()) {
      unset($row[0]);
      $map_data[] = $row;
    }
    $map_result->finalize();
  }
}
$out_array = array('data'=>$user,'stat'=>$stat);
if ( !empty($debug) ) {
  $out_array['debug'] = $debug;
}
$json = json_encode($out_array);
ob_end_clean();
echo $json;
?>
