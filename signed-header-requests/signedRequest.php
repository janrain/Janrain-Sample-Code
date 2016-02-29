<?php

function makeSignedAuthHeader($endpoint, $params, $datetime, $client_id, $secret) {
    $param_keys = array_keys($params);
    sort($param_keys);
    $kv_params = [];
    foreach($param_keys as $key) {
        array_push($kv_params, "{$key}={$params[$key]}");
    }
    $param_string = implode($kv_params, "\n");
    $string_to_sign = "${endpoint}\n${datetime}\n${param_string}\n";
    $signature = base64_encode(hash_hmac("sha1", $string_to_sign, $secret, True));
    return array("Authorization" => "Signature ${client_id}:${signature}");
}

print_r(makeSignedAuthHeader(
    '/entity.find',
    array('entity_type' => 'user', 'filter' => "lastUpdated >= '2016-01-01'"),
    'Fri, 26 Feb 2016 19:08:44 GMT',
    'apkrahlfumwse2e9nvrrotv6vchuptzw',
    'rylicq8ydkz0vmki3gqaoxbk4gyrr05t'
));

?>
