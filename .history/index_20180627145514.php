<?php declare (strict_types = 1);

const CLIENT_ID = '2d80231b815944328b271c5ef4f2e693';
const CLIENT_SECRET = '';
const ACCESS_TOKEN = '178618494.2d80231.823130dd9f1044aca1fe0a16d8d84e90';
const USER_ID = '178618494';
const BASE_DIR = __DIR__;
const CACHE_DIR_NAME = "cache";
const CACHE_LIFETIME = 60 * 60 * 24 * 1; //value in seconds; default: 1 day;

$url = sprintf("https://api.instagram.com/v1/users/%s/media/recent?access_token=%s", USER_ID, ACCESS_TOKEN);


const IMAGE_DIR_PATH = "/" . CACHE_DIR_NAME . '/' . "images";

$cacheName = hash("md5", $url) . ".json";
const CACHE_TIME_PATH = BASE_DIR . "/" . CACHE_DIR_NAME . '/' . ".cache_time";

if (file_exists(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName) && !array_key_exists("update", $_GET) && cacheValid()) {
    $res = file_get_contents(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName);
    echo $res;
    exit;
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
    foreach ($image["images"] as $key => &$imageSize) {
        $imageData = file_get_contents($imageSize["url"]);
        $imageSize["url"] = "/" . basename(BASE_DIR) . "/" . $imageDir . $key . ".jpg";
        file_put_contents(BASE_DIR . $imageDir . $key . ".jpg", $imageData);
    }
}

$imageFolders = glob(BASE_DIR . IMAGE_DIR_PATH . '/*', GLOB_ONLYDIR);
$validFolders = [];

foreach ($data as $image) {
    if (in_array($image["id"], $imageFolders)) {
        array_push($validFolders, BASE_DIR . IMAGE_DIR_PATH . '/' . $image["id"]);
    }
}

$toDelete = array_diff($imageFolders, $validFolders);
print_r($validFolders);
exit;
$toDelete = array_diff($toDelete, [".", ".."]);
foreach ($toDelete as $dir) {
    //$path = BASE_DIR . IMAGE_DIR_PATH . $dir;
    recursiveRemoveDirectory($path);
}

function recursiveRemoveDirectory($directory)
{
    foreach (glob("{$directory}/*") as $file) {
        if (is_dir($file)) {
            recursiveRemoveDirectory($file);
        } else {
            echo "Delete file" . $file . "\n";
            //unlink($file);
        }
    }
    //rmdir($directory);
    echo "Delete dir" . $directory . "\n";
}

$res["data"] = $data;

$result = json_encode($res);
file_put_contents(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName, $result);

file_put_contents(CACHE_TIME_PATH, time());

header("application/json");
//echo $result;