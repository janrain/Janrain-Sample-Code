<?php
define('JANRAIN_LOGIN_CLIENT_ID', "g7tkr2ue5t3c8fspef9wkrm2r2zk6erh");
define('JANRAIN_LOGIN_CLIENT_SECRET', "REDACTED");
define('JANRAIN_CAPTURE_SERVER', "https://maple-demo.janraincapture.com");
define('JANRAIN_REDIRECT_URI', "http://localhost/");
 
session_start();
 
if (!empty($_SESSION['expires']) && time() >= $_SESSION['expires']) {
    $params = array(
        'client_id' => JANRAIN_LOGIN_CLIENT_ID,
        'client_secret' => JANRAIN_LOGIN_CLIENT_SECRET,
        'grant_type' => 'refresh_token',
        'refresh_token' => $_SESSION['refresh_token'],
        'redirect_uri' => JANRAIN_REDIRECT_URI
    );
 
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, JANRAIN_CAPTURE_SERVER."/oauth/token");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
 
    $response = curl_exec($curl);
    curl_close($curl);
 
    $decoded = json_decode($response, true);
    $expires = strtotime("+{$decoded['expires_in']} seconds");
 
    $_SESSION['access_token'] = $decoded['access_token'];
    $_SESSION['refresh_token'] = $decoded['refresh_token'];
    $_SESSION['expires'] = $expires;
}
?>
