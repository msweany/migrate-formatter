<?php
# this file has the variables for environment variables on the app and in the functionApps
$apiKey = getEnv('functionKey');
$awsUrl = getEnv('functionUrlAws');
$gcpUrl = getEnv('functionUrlGcp');
$generalUrl = getEnv('functionUrlGeneral');

# general shared variables
$version = "1.0.1";
$jsVer = 1;

// determine if this is a dev or prod environment
if (getenv('migrateFormatterEnv') == 'dev') {
    $env = 'dev';
} else {
    $env = 'prod';
}
?>