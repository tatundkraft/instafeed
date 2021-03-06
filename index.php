<?php declare (strict_types = 1);

require_once 'config.php';

$url = sprintf("https://api.instagram.com/v1/users/self/media/recent?access_token=%s", ACCESS_TOKEN);

const IMAGE_DIR_PATH = "/" . CACHE_DIR_NAME . '/' . "images";
const BASE_DIR =  __DIR__;

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

//get json from instagram
$json = file_get_contents($url);
$res = json_decode($json, true);
$data = $res["data"];

//create folders if they don't exist
if (!is_dir(BASE_DIR . "/" . CACHE_DIR_NAME . '/')) {
    mkdir(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . "images/", 0777, true);
}

//Initialise directory glob to delete unused images
$imageFolders = glob(BASE_DIR . IMAGE_DIR_PATH . '/*', GLOB_ONLYDIR);
$validFolders = [];

//Go through all images
foreach ($data as &$image) {

    //create directory for each image, if it doesn't exist yet
    $imageDir = IMAGE_DIR_PATH . "/" . $image["id"] . "/";
    if (!is_dir(BASE_DIR . $imageDir)) {
        mkdir(BASE_DIR . $imageDir);
    }

    //check for instagram carousel, set boolean to prevent duplicate creating of image
    $isCarousel = false;
    if ($image["type"] == "carousel") {
        $isCarousel = true;
        $image = handleCarousel($image, $imageDir);
    }

    //fetch image data, save local and merge new url to response object
    foreach ($image["images"] as $key => &$imageSize) {
        if (!$isCarousel) {
            $imageData = file_get_contents($imageSize["url"]);
            file_put_contents(BASE_DIR . $imageDir . $key . ".jpg", $imageData);
        }
        $imageSize["url"] = WEB_PATH . $imageDir . $key . ".jpg";
    }

    //push all used image id to validFolders for deletion of unused ones
    $itemPath = BASE_DIR . IMAGE_DIR_PATH . '/' . $image["id"];
    if (in_array($itemPath, $imageFolders)) {
        array_push($validFolders, $itemPath);
    }
}

function handleCarousel($image, $imageDir)
{
    $count = 0;
    foreach ($image["carousel_media"] as $index => &$carouselImageData) {
        if ($carouselImageData["type"] != "image") continue;
        foreach ($carouselImageData["images"] as $key => &$imageSize) {
            $imageData = file_get_contents($imageSize["url"]);
            //if first image, skip count suffix, so image is easily reused
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
    return $image;
}

//delete unused image folders
$toDelete = array_diff($imageFolders, $validFolders);
foreach ($toDelete as $dir) {
    $imageDir = glob($dir . "/*");
    array_map('unlink', $imageDir);
    rmdir($dir);
}

$res["data"] = $data;

//return results, write cache
$result = json_encode($res);
file_put_contents(BASE_DIR . "/" . CACHE_DIR_NAME . '/' . $cacheName, $result);

file_put_contents(CACHE_TIME_PATH, time());

header("application/json");
echo $result;