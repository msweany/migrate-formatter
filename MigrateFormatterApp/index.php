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
                <p>Coming Soon</p>
            </div>
        </div>
        
    </div>
    <div class="text-center">
        Written and supported by: <a href="mailto:misweany@microsoft.com">Mike Sweany</a>, Sr Technical Specialist, SDP
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.21/jquery.csv.min.js" integrity="sha512-Y8iWYJDo6HiTo5xtml1g4QqHtl/PO1w+dmUpQfQSOTqKNsMhExfyPN2ncNAe9JuJUSKzwK/b6oaNPop4MXzkwg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="js/main.js"></script>
        <script src="js/functions.js"></script>
        <script src="js/custom.js"></script>
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



