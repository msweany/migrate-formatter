# this is meant to be copy/pasted into the Azure Cloud Shell using the PowerShell option
# set the subscription 
$sub = "misweany-aia-migrate-formatter"

# resource group
$rg = "migrate-formatter-rg"

# secret key
$secret = ""

# set the cosmos connection string : example: AccountEndpoint=https://migrate-formatter-cosmosdb.documents.azure.com:443/;AccountKey=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
$cosmosdbconnectionstring = ""

#functions we're updating
$functions = @("migrate-formatter-function-aws", "migrate-formatter-function-general", "migrate-formatter-function-gcp")
$functionUrlName = @("functionUrlAws", "functionUrlGeneral", "functionUrlGcp")

# set the web app name
$webapp = "migrate-formatter"

##### no need to gedit below this line #####

az account set --subscription $sub
az configure --defaults group=$rg

# set the keys in the web app
#az webapp config appsettings set -n $webapp --settings functionKey=$secret
$i=0
foreach($url in $functionUrlName) {
    $urlSetting = "$url=https://$($functions[$i]).azurewebsites.net/api/"
    az webapp config appsettings set -n $webapp --settings $urlSetting
    $i++
}

# set up the functions with the correct keys and cosmos connection string
foreach($function in $functions) {
    # set the master key for each function app
    #az functionapp keys set -n $function --key-type functionKeys --key-name default --key-value $secret
    # set the cosmos connection string for each function app
    #az webapp config appsettings set -n $function --settings CosmosDBConnectionString=$cosmosdbconnectionstring
    # set the function key connection string for each function app
    az webapp config appsettings set -n $function --settings functionKey=$secret
    # set all the function urls in the function apps config
    $i=0
    foreach($url in $functionUrlName) {
        $urlSetting = "$url=https://$($functions[$i]).azurewebsites.net/api/"
        #az webapp config appsettings set -n $function --settings $urlSetting
        $i++
    } 
}





