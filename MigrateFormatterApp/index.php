<?php 
include 'functions/header.php';
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Azure Migrate Formatter</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="container">
        <h2 class="text-center">Azure Migrate Formatter</h2>
        <h3 class="text-center">Brought to you by Mighty <img src="images/SDP_Logo_Color_wide_RGB.png" style="width: 200px;"/></h3>
        <div class="row initial-view">
            <div class="main-sections" style="width: 450px">
                <div id="upload-form">
                    <div class="form-group main-margin" id="fileUploadCSS">
                        <label for="fileToUpload">File to Upload</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" class="form-control-file">
                    </div>
                    <div id="progress" class="main-margin float-none"></div>
                    <div id="quickstart">
                        <br /><br />
                        <div>
                            <h3>Quick start</h3>
                            <p>Click one of the buttons below to get started</p>
                            <button class="btn btn-outline-secondary" id="aws-qs">AWS</button>
                            <button class="btn btn-outline-secondary" id="gcp-qs">GCP</button>
                            <button class="btn btn-outline-secondary" id="custom-qs">Custom CSV</button>
                            <button class="btn btn-outline-secondary" id="rvtools-qs">RVTools</button>
                        </div>
                        <div id="quickstart-info"></div>
                    </div>
                    
                </div>
                <div id="verify-file">
                    <div id="verify-confirm">
                        <span id="verify-type"></span> 
                        <button id="verify-btn-yes" class="btn btn-outline-primary">Yes</button> 
                        <button id="verify-btn-no" class="btn btn-outline-primary">No</button>
                    </div>
                    <br />
                    <span id="verify-explain"></span>
                    <table class="table" id="verify-table">
                        <thead>
                            <tr>
                                <th scope="col">Required Values</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="verify-body-required"></tbody>
                        <thead>
                            <tr>
                                <th scope="col">Optional Values</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="verify-body-optional"></tbody>
                        <tr>
                            <td colspan="2">
                                <button id="verify-btn-continue" class="btn btn-primary float-end" disabled>Continue</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col main-sections">
                <h3>What is this?</h3>
                <p>This is a tool that will take AWS, GCP exports, or work with a custom CSV file. After a couple questions, it will output a Azure Migrate Ready CSV that you can upload directly to Azure Migrate to get baseline pricing.</p>
                <h3>Why do I need it?</h3>
                <p>This will save you hours of time manipulating spreadsheets or looking up the other clouds VM information. This does it all for you.</p>
                <h3>How does it work?</h3>
                <p>With the AWS and GCP files, we've downloaded an export of all available VM's in each platform, when you upload your file, we check the Instance or Machine Types against a database and plug in the proper CPU/Memory specs.</p>
                <p>With the custom CSV, we ask you to map the columns to the proper Azure Migrate fields and then we output the CSV for you.</p>
                <h3>I've got this AzureMigrateReady CSV file, now what?</h3>
                <p>Great! Now you can go to Azure Migrate, create a new project, and upload the file.  It will give you a baseline cost for running those machines in Azure.</p>
                <p>Here's a link that shows how to upload a CSV to Azure Migrate (skip to 12 minutes to get right to the upload portion) <a href="https://microsoft.sharepoint.com/teams/AzureCoreSTU2/_layouts/15/stream.aspx?id=%2Fteams%2FAzureCoreSTU2%2FShared%20Documents%2FMigration%20Flight%20School%2FAWS%20Formatter%2Emp4&ga=1&referrer=StreamWebApp%2EWeb&referrerScenario=AddressBarCopied%2Eview" target="_blank">Azure Migrate CSV Upload</a></p>
                <h3>Requirements</h3>
                <p>We only can accept CSV uploads, so if you have an Excel file, please save it as a CSV first.</p>
                <p>For AWS and GCP, you need to have the proper exports from the cloud providers that include the Instance type or Machine type. For the custom CSV, at a minimum you need to have CPU and Memory in the file.</p>
                <p>All files can have optional columns, Operating System, Computer Name and Storage.  We let you know which are optional or required after you upload the file.</p>
                <h3>Assumptions</h3>
                <p>Because we're looking to get a starting point for a quote, the data is formatted in the following way</p>
                <ul>
                    <li>When you list the instance name, this app looks up the CPU and Memory for that machine</li>
                    <li>Storage entered is divided by the number of machines and rounded up to the next 10's (Ie: 153 is rounded up to 160)</li>
                    <li>Each machine is given 1 disk with the size of the storage per machine</li>
                    <li>OS is set to Windows Server 2016 (assuming AHUB) so cost is same with Win/Linux</li>
                    <li>Machines will be named VM1, VM2, etc. for the input to Azure Migrate (to avoid duplicates)</li>
                </ul>
                <h3>Is my data safe?</h3>
                <p>We log your username when you authenticate to the app and save generic usage data for reporting purposes.</p>
                <p>This tool processes the data, provides an Azure Ready Migrate file to you, then immediately purges any data we were working with to build the export.</p>
            </div>
        </div>
        
    </div>
    <div class="text-center">
        Written and supported by: <a href="mailto:misweany@microsoft.com">Mike Sweany</a>, Sr Technical Specialist, SDP<br />
        <?php echo 'version: <a href="changelog.php" target="_blank">'.$version.'</a>'; ?>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.21/jquery.csv.min.js" integrity="sha512-Y8iWYJDo6HiTo5xtml1g4QqHtl/PO1w+dmUpQfQSOTqKNsMhExfyPN2ncNAe9JuJUSKzwK/b6oaNPop4MXzkwg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="js/main.js?v=<?php echo $jsVer?>"></script>
        <script src="js/functions.js?v=<?php echo $jsVer?>"></script>
        <script src="js/custom.js?v=<?php echo $jsVer?>"></script>
        <script>

            $("#resetForm").hide();
            // disbale the upload button until the memory is set and the file is selected
            $("#uploadBtn").prop('disabled', true);
            $(document).ready(function () {
                // activate all tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            });
        </script>
    </body>
</html>



