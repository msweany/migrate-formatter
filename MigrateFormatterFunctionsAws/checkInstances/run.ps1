using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $TriggerMetadata)

# get the instances from the $request.Query.instances and split up the comma separated list
$instances = $Request.Query.instances 
$instances = $instances -split ","
$instances = $instances | ForEach-Object { $_.Trim() }

# create arrays needed for the script
$confirmed = @()
$checkInto = @()

#loop through the instances and check if they exist in the Cosmos DB (call checkInstance function)
foreach($instance in $instances){
    $query = '?code='+$env:functionKey
    $query += '&id='+$instance
    $url = $env:functionUrlAws+'checkInstance'+$query
    $check = Invoke-RestMethod -Method GET -Uri $url 
    if($check.id){
        $confirmed += $check
    }else{
        $checkInto += $instance
    }
}

# if we need to check into any, call the getInstances function
if($checkInto){
    $query = '?code='+$env:functionKey
    # implode the $checkInto array into a comma separated list
    $implode = $checkInto -join ","
    $query += '&instances=' + $implode
    $url = $env:functionUrlAws+'getInstance'+$query
    Invoke-RestMethod -Method GET -Uri $url 
}

# if we didn't need to check into any instances, just return the $confirmed array
if(!$checkInto){
    $body = $confirmed
}else{
    #call this function to get data about the instances from the Cosmos DB
    $query = '?code='+$env:functionKey
    $implode = $instances -join ","
    $query += '&instances=' + $implode
    $url = $env:functionUrlAws+'returnInstances'+$query
    $body = Invoke-RestMethod -Method GET -Uri $url 
}

Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [System.Net.HttpStatusCode]::OK
    Body = $body
})

