<?php
// Extract the necessary variables from the response
$profile = $auth_info['profile'];
$identifier = $profile['identifier'];
$provider = $profile['providerName'];

$providers_supporting_get_contacts = array(
    'Google',
    'Twitter',
    'Facebook',
    'Yahoo!'
);

// Example of the get_contacts API
if (in_array($provider, $providers_supporting_get_contacts)) {
    $post_data = array(
        'apiKey' => $janrain_api_key,
        'identifier' => $identifier
    );
    $url = 'https://rpxnow.com/api/v2/get_contacts';
    $curl = curl_init();
    $result = curl_helper_post($curl, $url, $post_data);
    if ($result == false) {
        curl_helper_error($curl, $url, $post_data);
    }
    $get_contacts = json_decode($result);
    curl_close($curl);
    output("get_contacts", $get_contacts);
}

// Examples of using the accessToken to perform Facebook Graph API calls.
// Some other providers also offer API access via the provided accessToken.
if ($provider == 'Facebook') {
    $access_token = $auth_info['accessCredentials']['accessToken'];
    $user_id = $auth_info['accessCredentials']['uid'];

    //Make a "feed" post.
    $post_data = array(
        'access_token' => $access_token,
        'message' => 'MESSAGE',
        'picture' => 'http://www.janrain.com/favicon.png',
        'link' => 'http://www.janrain.com',
        'name' => 'NAME',
        'caption' => 'CAPTION',
        'description' => 'DESCRIPTION'
    );
    $url = "https://graph.facebook.com/$user_id/feed";
    $curl = curl_init();
    $result = curl_helper_post($curl, $url, $post_data);
    if ($result == false) {
        curl_helper_error($curl, $url, $post_data);
    }
    $graph_feed = json_decode($result);
    curl_close($curl);
    output("GRAPH feed post result", $graph_feed);

    // Pull the "me" profile
    $me = facebook_helper_get('https://graph.facebook.com/me', $access_token);
    output("GRAPH 'me' profile", $me);

    // Pull the "likes"
    $likes = facebook_helper_get(
        "https://graph.facebook.com/$user_id",
        $access_token
    );
    output("GRAPH 'likes'", $likes);
}
