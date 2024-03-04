// global variables
var fileInfo = [];
var tableExplain = 'Match up the appropriate headers with the required headers.';

// handle the file upload
$("#fileToUpload").on("change", function() {
    // hide the quickstart
    $("#quickstart").hide();

    // disable the upload button
    $("#uploadBtn").prop('disabled', true);

    // show loading
    $("#progress").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i> Uploading...');

    // Get the selected file from the input
    var file = $("#fileToUpload")[0].files[0];

    if (file) {
        // Create a new FormData object
        var formData = new FormData();

        // Append the selected file to the FormData object with a specified key (e.g., 'csvFile')
        formData.append('csvFile', file);

        // add the memory to the form data
        formData.append('totalMemory', $("#totalMemory").val());

        // Send an AJAX POST request to your PHP server
        $.ajax({
            url: 'functions/upload.php', // Replace with the URL of your PHP server script
            type: 'POST',
            data: formData,
            contentType: false, // Set to false to let jQuery handle the content type
            processData: false, // Set to false to prevent jQuery from processing the data
            success: function(response) {
                // Handle the server response here
                fileInfo = response;
                // hide the loading 
                $("#progress").html('');
                // hide the .initial-view 
                $("#fileUploadCSS").hide();
                // show the selection-view
                $("#verify-selection").show();
                // show the verify-confirm
                $("#verify-file").show();
                $("#verify-confirm").show();

                if(fileInfo.type == 'rvtools'){
                    $("#verify-type").show().html('This looks like an RVTools file, is that correct?');   
                } 

                if(fileInfo.type == 'aws'){
                    $("#verify-type").show().html('This looks like an AWS file, is that correct?');   
                }   
                
                if(fileInfo.type == 'gcp'){
                    $("#verify-type").show().html('This looks like an GCP file, is that correct?');   
                } 

                if(fileInfo.type == 'custom'){
                    // call this on the custom.js file
                    formatCustom(fileInfo);
                } 
            }
        });
    }
});

// verify yes was clicked and show the appropriate table
$(document).on('click', '#verify-btn-yes', function(){
    // show the explanation
    $("#verify-explain").show().html(tableExplain);
    // show the table
    $("#verify-table").show();

    var requiredHtml = '';
    var optionalHtml = '';

    /* ##########################################################
    Handle the different file types 
    #############################################################*/

    // rvtools is different, we'll just share the privare preview link
    if(fileInfo.type == 'rvtools'){
        // set up the response
        html = '<h3>Processing as a RVTools file.</h3>';
        html += '<p>We have a private preview that allows you to upload the RVTools Export right into Azure Migrate now.</p>';
        html += 'Access the preview here <a href="https://aka.ms/migrate/rvtools" target="_blank">https://aka.ms/migrate/rvtools</a><br />';
        html += '<a href="https://microsoftapc.sharepoint.com/teams/AzureCoreIDC/_layouts/15/stream.aspx?id=%2Fteams%2FAzureCoreIDC%2FShared%20Documents%2FGeneral%2FExecution%2FDocumentation%2FAzure%20migrate%2FRVTools%20XLSX%20import%2FRVTools%20XLSX%20import%20private%20preview%20demo%2Emp4&nav=eyJwbGF5YmFja09wdGlvbnMiOnsic3RhcnRUaW1lSW5TZWNvbmRzIjoxOC4wOTQ4MTd9fQ%3D%3D&referrer=StreamWebApp%2EWeb&referrerScenario=AddressBarCopied%2Eview" target="_blank">See the demo video here.</a><br /><br />';
        html += 'Refresh this page to process another file.';
        $("#verify-confirm").html(html);

        // we dont want to show this table.
        $("#verify-explain").hide();
        $("#verify-table").hide();
       
    }
    
    // for AWS, get it set up for the next step
    if(fileInfo.type == 'aws'){
        // hide the buttons
        $("#verify-confirm").html('<h3>Processing as a AWS file.</h3>');
        
        // set up the required table info
        requiredHtml += columnMatch(true, 'Instance Type', 'The column with the AWS instance type', fileInfo);
        requiredHtml += columnMatch(true, 'Storage', 'The column with individual machine storage listed', fileInfo, true);
        requiredHtml += confirmOptions(true, 'Storage Type', 'Is the storage in the column GB or MB?', ['GB', 'MB']);
        
        // set up the optional table info
        optionalHtml += columnMatch(false, 'OS', 'Add this so we can match up if it\'s Windows or Linux. We\'ll assume Windows with AHUB if this is not set.', fileInfo);
        optionalHtml += columnMatch(false, 'VM Name', 'The original VM name column. Add this if you want to see it matched up on the output file.', fileInfo);
    }

    // GCP is hadeled the same as AWS
    if(fileInfo.type == 'gcp'){
        // hide the buttons
        $("#verify-confirm").html('<h3>Processing as a GCP file.</h3>');
        
        // set up the required table info
        requiredHtml += columnMatch(true, 'Machine Type', 'The column with the GCP machine type', fileInfo);
        requiredHtml += columnMatch(true, 'Storage', 'The column with individual machine storage listed', fileInfo, true);
        requiredHtml += confirmOptions(true, 'Storage Type', 'Is the storage in the column GB or MB?', ['GB', 'MB']);
        
        // set up the optional table info
        optionalHtml += columnMatch(false, 'OS', 'Add this so we can match up if it\'s Windows or Linux. We\'ll assume Windows with AHUB if this is not set.', fileInfo);
        optionalHtml += columnMatch(false, 'VM Name', 'The original VM name column. Add this if you want to see it matched up on the output file.', fileInfo);
    }

    /* ##########################################################
    End of unique file type info 
    #############################################################*/
    
    // append to the tbody in the table
    $("#verify-body-required").append(requiredHtml);

    // show this if the optionalHtml is not empty
    if(optionalHtml != ''){
        //append to the tbody in the table
        $("#verify-body-optional").append(optionalHtml);
    }
    
    // show dynamic tooltips
    showTooltip();
});

// verify no was clicked and show the custom form
$(document).on('click', '#verify-btn-no', function(){
    // call this on the custom.js file
    formatCustom();
});

// if one of the select lists change, capture that
$(document).on('change', '.verify-select', function(){
    // change the modified attribute to true
    $(this).attr('modified', 'true');

    if($(this).attr('id') == 'storage'){
         // if the value is storage, show the storage input
        if($(this).val() == 'storage_column'){
            // check to see if the storage_type radio buttons are already there
            if(!$('#storage_total_input').length){
                // clear the modified attribute
                storageType = false;
                // remove the storage_type radio buttons
                $('#storage_type').remove();
                // append the storage column to the table
                $("#verify-body-required").append('<tr id="storage_total_input"><td>Total Storage (GB) <i class="fa fa-info-circle show-tooltip" aria-hidden="true" data-bs-toggle="tooltip" title="Enter the total storage for all VM\'s in GB.  We\'ll spread that across all servers to get an idea of price."></i></td><td><input type="text" id="total_storage" modified="false" class="item-required" /></td></tr>');
            } 
        }else{
            if($('#storage_total_input').length){
                // clear the value for the storage input
                $("#storage_total_input").attr('modified', 'false');
                // remove the storage_total_input
                $("#storage_total_input").remove();
                // readd the storage_type radio buttons right after the storage select
                var newRow = confirmOptions(true, 'Storage Type', 'Is the storage in the column GB or MB?', ['GB', 'MB']);;
                $(newRow).insertAfter('#storage_row');
            }else{

            }
        }
    }
   
    // show dynamic tooltips
    showTooltip();
    // verify the form
    verifyForm();
});

// if the storage input changes, capture that
$(document).on('keyup', '#total_storage', function(){
    // change the modified attribute to true
    $(this).attr('modified', 'true');
    // verify the form
    verifyForm();
});

// set a global variable to track the clicks for memory_type
var memoryType = false;
// if one of the memory radio buttons change, capture that
$(document).on('change', 'input[name=memory_type]:checked', function(){
    // change the global variable to true
    memoryType = true
    // verify the form
    verifyForm();
});

// set a global variable to track the clicks for storage_type
var storageType = false;
// if one of the memory radio buttons change, capture that
$(document).on('change', 'input[name=storage_type]:checked', function(){
    // change the global variable to true
    storageType = true
    // verify the form
    verifyForm();
});

// once the button is ready, lets submit it and process the data
$(document).on('click', '#verify-btn-continue', function(){
    // disable the button
    $("#verify-btn-continue").prop('disabled', true);
    // disable all the selects and inputs
    $(".item-required").prop('disabled', true);
    $(".item-optional").prop('disabled', true);
    $(".btn-check").prop('disabled', true);
    // show loading
    $("#progress").html('<i class="fa fa-spinner fa-spin" style="font-size:24px"></i> Processing...');
    // set up the data object
    var data = {
        required: {},
        optional: {}
    };

    // loop through the required and optional items and add them to the data object
    $(".item-required").each(function(){
        data.required[$(this).attr('id')] = $(this).val();
    });

    // loop through the optional items and add them to the data object
    $(".item-optional").each(function(){
        data.optional[$(this).attr('id')] = $(this).val();
    });

    if(fileInfo.type == 'aws'){ 
        var functionFile = 'functions/aws.php';
    }

    if(fileInfo.type == 'gcp'){ 
        var functionFile = 'functions/gcp.php';
    }

    if(fileInfo.type == 'custom'){ 
        // add the track clicks to the data object
        data.trackClicks = trackClicks;
        var functionFile = 'functions/custom.php';
    }

    // send the data to the appropriate function and return the Migrate ready file
    $.ajax({
        url: functionFile,
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            // hide the loading
            $("#progress").html('');
            // clear everything and show the download link
            $("#verify-file").html('<h3>Processing complete.</h3>');
            $("#verify-file").append('Refresh this page to process another file.');
            
            // prompt the user to download the file
            window.location.href = "files/"+response.filename;

            // wait for 5 seconds and then delete the file
            setTimeout(function(){
                // delete the file on the server, send a POST to functions/clean.php
                $.ajax({
                    url: 'functions/clean.php',
                    type: 'POST',
                    data: {filename: response.filename},
                    success: function(response) {
                        //console.log(response);
                    }
                });
            }, 5000);
        }
    });
});


// handle the quick start button clicks for any ID that ends with -qs
$(document).on('click', '[id$="-qs"]', function(){
    // get the button ID
    var btnID = $(this).attr('id');

    // split to the before the last dash
    var workload = $(this).attr('id').split('-').slice(0, -1).join('-');
    
    // remove active from the other buttons
    $('[id$="-qs"]').removeClass('active');

    // make this button active
    $('#'+btnID).addClass('active');

    // call the quickstart function
    quickStart(workload);
});