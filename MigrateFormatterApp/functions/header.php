<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('config.php');
require_once('functions.php');

// show this next section if you are in prod
if($env == 'prod'){
    # grab the user email in $_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME'] and split it to get the domain name
    $domain = explode("@", $_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME'])[1];
    if(trim($domain)=="microsoft.com"){
        logThis("login","Authenticated");
    }else{
        logThis("login","Not Authenticated");
        echo $domain ." Access to this page requires a @Microsoft.com account, please log in with your Microsoft credentials.";
        exit;
    }
}

?>