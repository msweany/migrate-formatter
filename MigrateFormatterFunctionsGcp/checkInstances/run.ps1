using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $TriggerMetadata)

# get the instances from the $request.Query.instances and split up the comma separated list
$instances = $Request.Query.instances 
$instances = $instances -split ","
$instances = $instances | ForEach-Object { $_.Trim() }

# create arrays needed for the script
$output = @()

#loop through the instances and check if they exist in the Cosmos DB (call checkInstance function)
foreach($instance in $instances){
    $query = '?code='+$env:functionKey
    $query += '&id='+$instance
    $url = $env:functionUrlGcp+'checkInstance'+$query
    $check = Invoke-RestMethod -Method GET -Uri $url 
    if($check.id){
        $output += $check
    }else{
        ## if the content is blank, ignore it
        if($check -ne ""){
            # creat an array to state the instance isn't found in our Cosmos DB
            $notFound = @{}
            $notFound.id = $instance
            $notFound.cpu = 999
            $notFound.memory = 999999
            $output += $notFound
        }
        
    }
}

# return the output
$body = $output

Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [System.Net.HttpStatusCode]::OK
    Body = $body
})

