<?php declare (strict_types = 1);

const CLIENT_ID = '2d80231b815944328b271c5ef4f2e693';
const CLIENT_SECRET = '';
const ACCESS_TOKEN = '178618494.2d80231.823130dd9f1044aca1fe0a16d8d84e90';
const USER_ID = '178618494';
const BASE_DIR = __DIR__;
const CACHE_DIR = "cache/";

$url = sprintf("https://api.instagram.com/v1/users/%s/media/recent?access_token=%s", USER_ID, ACCESS_TOKEN);

const IMAGE_ROOT = "/" . CACHE_DIR . "images/";

$res = file_get_contents($url);
$data = $res["data"];

if (!is_dir(BASE_DIR . "/" . CACHE_DIR)) {
    mkdir(BASE_DIR . "/" . CACHE_DIR . "images/", 0777, true);
}

foreach ($data as &$image) {
    $imageDir = BASE_DIR . "/" . CACHE_DIR . $image["id"];
    if (is_dir($imageDir)) {
        mkdir($imageDir);
    }
    foreach ($image as $key => &$imageSize) {
        $imageData = file_get_contents($imageSize["url"]);
        file_put_contents($imageDir . "/" . $key . ".jpg");
    }
}