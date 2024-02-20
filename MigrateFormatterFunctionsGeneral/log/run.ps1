using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $TriggerMetadata)

$body = "Hi";
if($Request.Query.email){
    $timestamp = Get-Date -Format "MM-dd-yyyy HH:mm:ss"
    $dbRecord = @{
        email = $Request.Query.email
        message = $Request.Query.info
        timestamp = $timestamp
    }
    $body = $dbRecord
}

# Associate values to output bindings by calling 'Push-OutputBinding'.
Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [HttpStatusCode]::OK
    Body = $body
})

# Check if $records exists and push it to Cosmos DB using the output binding
if ($dbRecord) {
    # Configure the output binding for Cosmos DB
    Push-OutputBinding -Name outputDocument -Value $dbRecord
}

