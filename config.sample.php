<?php

define("CLIENT_ID", ""); //Your client ID
define("CLIENT_SECRET", ""); //Your client secret
define("ACCESS_TOKEN", ""); //Your generated access token
define("CACHE_DIR_NAME", "cache"); // name of the cache folder. Change this to your prefered name.
define("CACHE_LIFETIME", 60 * 60 * 24 * 1); //value in seconds; default: 1 day;
define("WEB_PATH", ""); // If you place your script into a subfolder on your domain, add the path here eg. yourdomain.com/<path>/ -> WEB_PATH = "/<path>. See below.
define("REDIRECT_URI", "http://localhost:8000" . WEB_PATH . "/generate_access_token.php"); //Redirect URI for your instagram client. If you deploy this script, you probably need to change this.