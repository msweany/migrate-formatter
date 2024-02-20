<?php
require_once('config.php');
function logThis($message){
    global $apiKey;
    global $generalUrl;
    #use CURL to make the function call
    #$url = "https://aws-migrate-function.azurewebsites.net/api/log?code=".getEnv('functionKey')."&email=".$_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME']."&info=".$message;
    #$ch = curl_init();
    #curl_setopt($ch, CURLOPT_URL, $url);
    # get
    #curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    #curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    #curl_exec($ch);
    #curl_close($ch);
}
// determine if this is a dev or prod environment
if (getenv('migrateFormatterEnv') == 'dev') {
    $env = 'dev';
} else {
    $env = 'prod';
}

// show this next section if you are in prod
if($env == 'prod'){
    # grab the user email in $_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME'] and split it to get the domain name
    $domain = explode("@", $_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME'])[1];
    if(trim($domain)=="microsoft.com"){
        #logThis("Authenticated");
    }else{
        #logThis("Not Authenticated");
        echo $domain ." Access to this page requires a @Microsoft.com account, please log in with your Microsoft credentials.";
        exit;
    }
}

?>