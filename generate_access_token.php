<?php declare (strict_types = 1);

require_once 'config.php';


if (!array_key_exists("code", $_GET)) {
    $requestUrl = sprintf("https://api.instagram.com/oauth/authorize/?client_id=%s&redirect_uri=%s&response_type=code", CLIENT_ID, REDIRECT_URI);
    header("Location: ".$requestUrl);
    exit;
}


$code = $_GET["code"];

$ch = curl_init();

$access_token_parameters = array(
    "client_id" => CLIENT_ID,
    "client_secret" => CLIENT_SECRET,
    "code" => $code,
    "redirect_uri" => REDIRECT_URI,
    'grant_type' => 'authorization_code',
);

curl_setopt($ch, CURLOPT_URL, "https://api.instagram.com/oauth/access_token");
curl_setopt($ch, CURLOPT_POST,true);   // to send a POST request
curl_setopt($ch, CURLOPT_POSTFIELDS,$access_token_parameters);   // indicate the data to send
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   // to stop cURL from verifying the peer's certificate.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   // to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.

$json = curl_exec($ch);
curl_close($ch);

$res = json_decode($json, true);

if(is_null($res)) {
    echo "<p>There was an error in the authorization.</p>";
} else {
    echo "<p>You're access token is </p>";
    echo "<h2>" . $res["access_token"] . "<h2>";
}
