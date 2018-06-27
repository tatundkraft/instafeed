<?php declare (strict_types = 1);

const CLIENT_ID = '2d80231b815944328b271c5ef4f2e693';
const CLIENT_SECRET = '';
const ACCESS_TOKEN = '178618494.2d80231.823130dd9f1044aca1fe0a16d8d84e90';
const USER_ID = '178618494';
const BASE_DIR = __DIR__;
const CACHE_DIR_NAME = "cache";
const CACHE_LIFETIME = 60 * 60 * 24 * 1; //value in seconds; default: 1 day;
const WEB_PATH = '';

$url = sprintf("https://api.instagram.com/v1/users/%s/media/recent?access_token=%s", USER_ID, ACCESS_TOKEN);


const IMAGE_DIR_PATH = "/" . CACHE_DIR_NAME . '/' . "images";

$cacheName = hash("md5", $url) . ".json";
const CACHE_TIME_PATH = BASE_DIR . "/" . CACHE_DIR_NAME . '/' . ".cache_time";

if (useCache($cacheName)) {
    $res = file_get_contents(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName);
    echo $res;
    exit;
}

function useCache($cacheName)
{
    return file_exists(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName) && !array_key_exists("update", $_GET) && cacheValid();
}

function cacheValid()
{
    if (!file_exists(CACHE_TIME_PATH)) return false;
    if (CACHE_LIFETIME === 0) return true;
    $cacheCreationDate = file_get_contents(CACHE_TIME_PATH);
    return ((time() - $cacheCreationDate) < CACHE_LIFETIME);
}
$json = file_get_contents($url);
$res = json_decode($json, true);
$data = $res["data"];

if (!is_dir(BASE_DIR . "/" . CACHE_DIR_NAME . '/')) {
    mkdir(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . "images/", 0777, true);
}

foreach ($data as &$image) {
    $imageDir = IMAGE_DIR_PATH . "/" . $image["id"] . "/";
    if (!is_dir(BASE_DIR . $imageDir)) {
        mkdir(BASE_DIR . $imageDir);
    }

    $isCarousel = false;
    if ($image["type"] == "carousel") {
        $isCarousel = true;
        handleCarousel($image);
    }

    foreach ($image["images"] as $key => &$imageSize) {
        if (!$isCarousel) {
            $imageData = file_get_contents($imageSize["url"]);
            file_put_contents(BASE_DIR . $imageDir . $key . ".jpg", $imageData);
        }
        $imageSize["url"] = WEB_PATH . $imageDir . $key . ".jpg";
    }
}

function handleCarousel($image)
{
    $count = 0;
    foreach ($image["carousel_media"] as $index => &$carouselImageData) {
        if ($carouselImageData["type"] != "image") continue;
        foreach ($carouselImageData["images"] as $key => &$imageSize) {
            $imageData = file_get_contents($imageSize["url"]);
            if ($count === 0) {
                file_put_contents(BASE_DIR . $imageDir . $key . ".jpg", $imageData);
                $imageSize["url"] = WEB_PATH . $imageDir . $key . ".jpg";
            } else {
                file_put_contents(BASE_DIR . $imageDir . $key . "_" . $count . ".jpg", $imageData);
                $imageSize["url"] = WEB_PATH . $imageDir . $key . "_" . $count . ".jpg";
            }
        }
        $count++;
    }
}

$imageFolders = glob(BASE_DIR . IMAGE_DIR_PATH . '/*', GLOB_ONLYDIR);
$validFolders = [];

foreach ($data as $image) {
    $itemPath = BASE_DIR . IMAGE_DIR_PATH . '/' . $image["id"];
    if (in_array($itemPath, $imageFolders)) {
        array_push($validFolders, $itemPath);
    }
}

$toDelete = array_diff($imageFolders, $validFolders);
foreach ($toDelete as $dir) {
    $imageDir = glob($dir . "/*");
    array_map('unlink', $imageDir);
    rmdir($dir);
}

$res["data"] = $data;

$result = json_encode($res);
file_put_contents(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName, $result);

file_put_contents(CACHE_TIME_PATH, time());

header("application/json");
echo $result;