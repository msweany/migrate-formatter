using namespace System.Net

# Input bindings are passed in via param block.
param($Request, $TriggerMetadata)

# get the GET variables (should be a comma delimited list of instances)
$instances = $Request.Query.instances
# split the instances into an array
$instances = $instances -split ","
$instances = $instances | ForEach-Object { $_.Trim() }
# create an array to track which values we've saved
$added = @()
# Create an array to hold multiple DBrecords
$records = @()

#loop through the instances and get the info for them from https://instances.vantage.sh/aws/ec2/
foreach($instance in $instances){
    # trim instance
    $instance = $instance.Trim()

    # make sure this value wasn't already checked - see if it's in the $added array
    $check = $added | Where-Object { $_ -eq $instance }

    # if if wasn't in the #added array, get the data
    if(!$check){

        # Define the URL of the page containing the table
        $url = 'https://instances.vantage.sh/aws/ec2/'+$instance
        
        # Create a User-Agent header to mimic a web browser request
        $userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36"

        # Create a headers hashtable with the User-Agent header
        $headers = @{
            "User-Agent" = $userAgent
        }

        # Fetch the HTML content of the page
        $response = Invoke-WebRequest -Uri $url -Headers $headers
        $htmlContent = $response.RawContent

        # Define a regular expression pattern to match table rows
        $rowPattern = '<tr.*?>\s*<td.*?>(.*?)</td>\s*<td.*?>(.*?)</td>\s*<td.*?>(.*?)</td>\s*</tr>'

        # Find all matches using the regular expression
        $matchTable = [regex]::Matches($htmlContent, $rowPattern)

        # Loop through the matches
        foreach ($match in $matchTable) {
            $size = [System.Text.RegularExpressions.Regex]::Replace($match.Groups[1].Value, "<.*?>", "")
            #add $the instance name to the $added array
            $added += $size
            # Convert $vCPUs to an integer
            $vCPUs = [int]([System.Text.RegularExpressions.Regex]::Replace($match.Groups[2].Value, "<.*?>", ""))
            # Convert $memory to an integer and multiply it by 1024
            $memory = [int]([System.Text.RegularExpressions.Regex]::Replace($match.Groups[3].Value, "<.*?>", "")) * 1024
            
            # Create an object for the current row and add it to the data array
            $rowData = @{
                id = $size
                cpu = $vCPUs
                memory = $memory
            }
            # add to the DB records
            $records += $rowData

        }
    } 
}

$body = @{
    "status" = "100"
    "message" = "success"
    "data" = $records
}

# Associate values to output bindings by calling 'Push-OutputBinding'.
Push-OutputBinding -Name Response -Value ([HttpResponseContext]@{
    StatusCode = [HttpStatusCode]::OK
    Body = $body
})

# Check if $records exists and push it to Cosmos DB using the output binding
if ($records) {
    # Configure the output binding for Cosmos DB
    Push-OutputBinding -Name outputDocument -Value $records
}
