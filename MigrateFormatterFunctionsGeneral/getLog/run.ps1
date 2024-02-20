using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $Documents, $TriggerMetadata)

if($Documents){
    # only return the email, message and timestamp
    $Documents = $Documents | Select-Object email,message,timestamp
    # rebuild the output like this
    <# {
        "email": <unique email>,
        "message": <message>,
        "count": <number of times the email appears with this message>,
        "first-timestamp": <oldest timestamp>
        "newest-timestamp": <newest timestamp>
    }
    #>
    $body = @()
    $Documents | Group-Object email,message | ForEach-Object {
        # split the email to get the email address
        $email = ($_.Name -split " ")[0]
        
        $count = $_.Count
        $first = ($_.Group | Sort-Object timestamp)[0].timestamp
        $last = ($_.Group | Sort-Object timestamp -Descending)[0].timestamp
        $body += @{
            email = $email
            message = $_.Group[0].message
            count = $count
            "first-timestamp" = $first
            "newest-timestamp" = $last
        }
    }
    
    
}else{
    $body = ""
}

# Associate values to output bindings by calling 'Push-OutputBinding'.
Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [HttpStatusCode]::OK
    Body = $body
})
