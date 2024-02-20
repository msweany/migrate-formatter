using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $Documents, $TriggerMetadata)

if($Documents){
    $body=$Documents
}else{
    $body = ""
}


# Associate values to output bindings by calling 'Push-OutputBinding'.
Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [HttpStatusCode]::OK
    Body = $body
})
