using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $Documents, $TriggerMetadata)

# get the GET variables (should be a comma delimited list of instances)
$instances = $Request.Query.instances
# split the instances into an array
$instances = $instances -split ","

# only return the documents that are in the $instances array
$Documents = $Documents | Where-Object { $instances -contains $_.id }

# only return id, cpu and memory 
$Documents = $Documents | Select-Object id, cpu, memory 
$body = $Documents 

# Associate values to output bindings by calling 'Push-OutputBinding'.
Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [HttpStatusCode]::OK
    Body = $body
})
