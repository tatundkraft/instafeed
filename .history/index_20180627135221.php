<?php declare (strict_types = 1);

const CLIENT_ID = '2d80231b815944328b271c5ef4f2e693';
const CLIENT_SECRET = '';
const ACCESS_TOKEN = '178618494.2d80231.823130dd9f1044aca1fe0a16d8d84e90';
const USER_ID = '178618494';
const BASE_DIR = __DIR__;
const CACHE_DIR = "cache/";

$url = sprintf("https://api.instagram.com/v1/users/%s/media/recent?access_token=%s", USER_ID, ACCESS_TOKEN);

const IMAGE_ROOT = "/" . CACHE_DIR . "images/";

$cacheName = hash("md5", $url) . ".json";

if (file_exists(BASEDIR . "/" . CACHEDIR . $cacheName) && !array_key_exists("update", $_GET)) {
    $res = file_get_contents(BASEDIR . "/" . CACHEDIR . $cacheName);
    echo $res;
}

$json = file_get_contents($url);
$res = json_decode($json, true);
$data = $res["data"];

if (!is_dir(BASE_DIR . "/" . CACHE_DIR)) {
    mkdir(BASE_DIR . "/" . CACHE_DIR . "images/", 0777, true);
}

foreach ($data as &$image) {
    $imageDir = IMAGE_ROOT . $image["id"];
    if (!is_dir(BASE_DIR . $imageDir)) {
        mkdir(BASE_DIR . $imageDir);
    }
    foreach ($image["images"] as $key => &$imageSize) {
        $imageData = file_get_contents($imageSize["url"]);
        $imageSize["url"] = $imageDir . "/" . $key . "jpg";
        file_put_contents($imageDir . "/" . $key . ".jpg", $imageData);
    }
}

$res["data"] = $data;

$result = json_encode($res);
file_put_contents($cacheName, $result);

header("application/json");
echo $result;