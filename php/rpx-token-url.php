<?php
// Below is a very simple and verbose PHP script that implements the Engage
// token URL processing and some popular Pro/Enterprise examples. The code below
// assumes you have the CURL HTTP fetching library with SSL.
require('helpers.php');

ob_start();

// PATH_TO_API_KEY_FILE should contain a path to a plain text file containing
// only your API key. This file should exist in a path that can be read by your
// web server, but not publicly accessible to the Internet.
$janrain_api_key = trim(file_get_contents('PATH_TO_API_KEY_FILE'));

// Set this to true if your application is Pro or Enterprise.
$social_login_pro = false;

// Step 1: Extract token POST parameter
$token = $_POST['token'];

if ($token) {
    // Step 2: Use the token to make the auth_info API call.
    $post_data = array(
        'token' => $token,
        'apiKey' => $janrain_api_key,
        'format' => 'json'
    );

    if ($social_login_pro) {
        $post_data['extended'] = 'true';
    }

    $curl = curl_init();
    $url = 'https://rpxnow.com/api/v2/auth_info';
    $result = curl_helper_post($curl, $url, $post_data);
    if ($result == false) {
        curl_helper_error($curl, $url, $post_data);
        die();
    }
    curl_close($curl);

    // Step 3: Parse the JSON auth_info response
    $auth_info = json_decode($result, true);

    if ($auth_info['stat'] == 'ok') {
        echo "\n auth_info:";
        echo "\n"; var_dump($auth_info);

        // Pro and Enterprise API examples
        if ($social_login_pro) {
            include('social_login_pro_examples.php');
        }

        // Step 4: Your code goes here! Use the identifier in
        // $auth_info['profile']['identifier'] as the unique key to sign the
        // user into your system.

    } else {
        // Handle the auth_info error.
        output('An error occurred', $auth_info);
        output('result', $result);
    }
} else {
    echo 'No authentication token.';
}
$debug_out = ob_get_contents();
ob_end_clean();
?>
<html>
    <head>
        <title>Janrain Token URL Example</title>
    </head>
    <body>
        <pre><?php echo $debug_out; ?></pre>
    </body>
</html>
