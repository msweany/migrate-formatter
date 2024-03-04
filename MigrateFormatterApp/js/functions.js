function columnMatch(required, column, info, response, storage = false){
    // Create a select ID based on the column name, all lowercase and no spaces
    var selectID = column.toLowerCase().replace(' ', '_');
    // add a class to the row if it's required
    var className = required ? 'item-required' : 'item-optional';        
    var html = '<tr id="'+selectID+'_row"><td>'+column+' <i class="fa fa-info-circle show-tooltip" aria-hidden="true" data-bs-toggle="tooltip" title="'+info+'"></i></td>';
    
    html += '<td><select id="'+selectID+'" class="verify-select '+className+'" modified="false">';
        html += '<option>Select</option>';
        $.each(response.headers, function(key, value) {
            html += '<option value="' + key + '">' + value + '</option>';
        });
        if(storage){
            html += '<option value="storage_column">I don\'t have a storage column</option>';
        }
        html += '</select>';
    html += '</select></td></tr>';
    return html;
}

function confirmOptions(required, title, info, options, defaultOption = null){
    var checked = "";
    // create an id that can be used
    var newId = title.toLowerCase().replace(' ', '_');
    // add a class to the row if it's required
    var className = required ? 'item-required' : 'item-optional';        
    var html = '<tr id="'+newId+'"><td>'+title+' <i class="fa fa-info-circle show-tooltip" aria-hidden="true" data-bs-toggle="tooltip" title="'+info+'"></i></td>';
    html += '<td>';
        // button group
        html += '<div class="btn-group '+className+'" role="group" modified="false">';
        $.each(options, function(key, value) {
            if(value == defaultOption){
                checked = 'checked';
            }else{
                checked = '';
            }
            html += '<input type="radio" class="btn-check" name="'+newId+'" id="'+newId+'_'+value+'" autocomplete="off" '+checked+' >';
            html += '<label class="btn btn-outline-dark btn-sm" for="'+newId+'_'+value+'">'+value+'</label>';
        });
        html += '</div>';
    html += '</td></tr>';
    return html;
}

function showTooltip(){
    $('.show-tooltip').tooltip({
        trigger: 'hover'
    }).on('click', function () {
        $(this).tooltip('hide');
    });
}

// verify form
function verifyForm(){
    // count all item-required classes
    var requiredItems = $('.item-required').length;
    // count all item-optional classes
    var optionalItems = $('.item-optional').length;
    // count all item-required classes that have a value
    var requiredItemsWithValue = $('.item-required[modified="true"]').length;

    // check both memory and storage radio buttons
    if(memoryType){
        requiredItemsWithValue++;
    }
    if(storageType){
        requiredItemsWithValue++;
    }

    // if all required items have a value, enable the button
    if(requiredItems == requiredItemsWithValue){
        $("#verify-btn-continue").prop('disabled', false);
    } else {
        $("#verify-btn-continue").prop('disabled', true);
    }
}

// show the quick start information
function quickStart(selected){
    var html = '';
    if(selected == 'aws'){
        html = '<h3>AWS Files</h3>';
        html += '<p>You can get the appropriate file by accessing the <a href="https://aws.amazon.com/cli/" target="_blank">AWS CLI</a> and running the following command:</p>';
        html += '<p>This command will create a file called instances.csv with the following columns: ID, Name, OS, and Instance_Type</p>';
        html += `<pre>echo "ID,Name,OS,Instance_Type" > instances.csv
aws ec2 describe-instances \\
    --filters Name=instance-state-name,Values=running \\
    --query 'Reservations[*].Instances[*].{Name:Tags[?Key==\`Name\`]|[0].Value,Instance:InstanceId,Type:InstanceType,Platform:PlatformDetails}' \\
    --output text | sed -E 's/\s+/,/g'>> instances.csv'
        </pre>`;
        html += `<h3>Resources</h3>
            <p>You can use this download to test the AWS conversion functionality.</p>
            <p><a href="/files/aws-migrate-formatter-example.csv">AWS Example</a></p>`; 
        
    }else if(selected == 'gcp'){
        html = '<h3>GCP File</h3>';
        html += '<p>You can get the appropriate file by accessing the Google Cloud Shell in a GCP project that has VM\'s and running the following command:</p>';
        html += '<p>This command will create a file called gcp_machines.csv with the following columns: Name, Machine Type, Zone</p>';
        html += `<pre>gcloud compute instances list --format="csv (NAME,MACHINE_TYPE,ZONE)">gcp_machines.csv</pre>`;
        html += `<h3>Resources</h3>
            <p>You can use this download to test the GCP conversion functionality.</p>
            <p><a href="/files/gcp-migrate-formatter-example.csv">GCP Example</a></p>`;
    }else if(selected == 'rvtools'){
        html = '<h3>RVTools</h3>';
        html += '<p>We have a private preview that allows you to upload the RVTools Export right into Azure Migrate now.</p>';
        html += 'Access the preview here <a href="https://aka.ms/migrate/rvtools" target="_blank">https://aka.ms/migrate/rvtools</a><br />';
        html += '<a href="https://microsoftapc.sharepoint.com/teams/AzureCoreIDC/_layouts/15/stream.aspx?id=%2Fteams%2FAzureCoreIDC%2FShared%20Documents%2FGeneral%2FExecution%2FDocumentation%2FAzure%20migrate%2FRVTools%20XLSX%20import%2FRVTools%20XLSX%20import%20private%20preview%20demo%2Emp4&nav=eyJwbGF5YmFja09wdGlvbnMiOnsic3RhcnRUaW1lSW5TZWNvbmRzIjoxOC4wOTQ4MTd9fQ%3D%3D&referrer=StreamWebApp%2EWeb&referrerScenario=AddressBarCopied%2Eview" target="_blank">See the demo video here.</a><br /><br />';
    }else{
        html = '<h3>Custom CSV</h3>';
        html += '<p>For custom files, we can handle the following.</p>';
        html += '<ul>';
        html += '<li><strong>Single Line</strong> - one computer per line.</li>';
        html += '<li><strong>Multiple per line</strong> - multiple computers per line with a count column.</li>';
        html += '<li><strong>Group or Business Unit</strong> - if you have multiple business units in a single file, we can name those computers so they can be easily searched in Migrate.</li>';
        html += '<li><strong>Memory</strong> - we can handle the source as GB or MB, just let us know.</li>';
        html += '<li><strong>Storage</strong> - we can handle the source as GB or MB, just let us know.</li>';
        html += '<li>We can also accept storage on the sheet, or you can enter it for the entire fleet.</li>';
        html += '<li><strong>OS</strong> - if you have the OS listed, we can match it up to the VM in Migrate.</li>';
        html += '<li><strong>VM Name</strong> - if you have the original VM name, we can match it up to the VM in Migrate.</li>';
        html += '</ul>';
        html += `<h3>Resources</h3>
            <p>You can use these downloads to test the custom csv functionality.</p>
            <a href="/files/custom-single-migrate-formatter-example.csv">Single Computer per line example</a><br />
            <a href="/files/custom-multi-migrate-formatter-example.csv">Multi Computer per line example</a><br />
            <a href="/files/custom-multi-groups-migrate-formatter-example.csv">Multi Computer per line with Business Units example</a>`;
    }
    $('#quickstart-info').html(html);
}