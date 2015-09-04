<?php

function set_common_curl_opts($curl, $url) {
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl, CURLOPT_FAILONERROR, true);
}

function curl_helper_post($curl, $url, $post_data) {
  set_common_curl_opts($curl, $url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
  return curl_exec($curl);
}

function curl_helper_get($curl, $url) {
  set_common_curl_opts($curl, $url);
  return curl_exec($curl);
}

function curl_helper_error($curl, $url, $post_data = false) {
  echo "\nURL: $url";
  echo "\nCurl error: " . curl_error($curl);
  echo "\nHTTP code: " . curl_errno($curl);
  if ($post_data) {
    echo "\n";
    var_dump($post_data);
  }
}

function facebook_helper_get($endpoint, $access_token) {
  $url = $endpoint . '?access_token=' . urlencode($access_token);
  $curl = curl_init();
  $result = curl_helper_get($curl, $url);
  if ($result == false){
      curl_helper_error($curl, $url);
  }
  $json = json_decode($result);
  curl_close($curl);
  return $json;
}

function output($message, $var) {
  echo "\n$message:\n";
  var_dump($var);
}
