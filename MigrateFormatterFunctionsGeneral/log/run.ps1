using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $TriggerMetadata)
# &email=".$_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME']."&info=".$message."&type=".$type."&storage=".$storage."&multi=".$multi."&groups=".$groups."&os=".$os."&rows=".$rows;
$body = "Hi";
if($Request.Query.email){
    $timestamp = Get-Date -Format "MM-dd-yyyy HH:mm:ss"

    if($Request.Query.type -eq "login") {
        $dbRecord = @{
            id = $Request.Query.email
            message = $Request.Query.info
            type = $Request.Query.type
            timestamp = $timestamp
        }
    }else{
        $dbRecord = @{
            email = $Request.Query.email
            message = $Request.Query.info
            type = $Request.Query.type
            storage = $Request.Query.storage
            multi = $Request.Query.multi
            groups = $Request.Query.groups
            os = $Request.Query.os
            rows = $Request.Query.rows
            timestamp = $timestamp    
        }
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

