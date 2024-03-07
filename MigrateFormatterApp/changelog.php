<?php 
include 'functions/header.php';
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Azure Migrate Formatter Changelog</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
    <div class="container">
        <h2 class="text-center">Azure Migrate Formatter</h2>
        <h3 class="text-center">Brought to you by Mighty <img src="images/SDP_Logo_Color_wide_RGB.png" style="width: 200px;"/></h3>
        <div class="row initial-view">
            <div class="main-sections">
                <h3>Migrate Formatter Changelog</h3>
                <table class="table" id="change_table">
                    <thead>
                        <tr>
                            <th scope="col">Version</th>
                            <th scope="col">Date</th>
                            <th scope="col">Changes</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        
    </div>
    <div class="text-center">
        Written and supported by: <a href="mailto:misweany@microsoft.com">Mike Sweany</a>, Sr Technical Specialist, SDP<br />
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/1.0.21/jquery.csv.min.js" integrity="sha512-Y8iWYJDo6HiTo5xtml1g4QqHtl/PO1w+dmUpQfQSOTqKNsMhExfyPN2ncNAe9JuJUSKzwK/b6oaNPop4MXzkwg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="js/main.js"></script>
        <script src="js/functions.js"></script>
        <script src="js/custom.js"></script>
        <script>
            $(document).ready(function () {
                // activate all tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
                })
                // app version saved in config file
                var changes = [
                    ["1.0.1", "3/6/24", "AWS - added option to select \"Workloads\" column for multiple workloads on the same spreadsheet so we can name the computers by project name and sort them easier in Azure Migrate reports."],
                    ["1.0.1", "3/6/24", "GCP - added option to select \"Projects\" column for multiple projects on the same spreadsheet so we can name the computers by project name and sort them easier in Azure Migrate reports."],
                    ["1.0.1", "3/6/24", "GCP - added assumptions for custom machines to take CPU and memory from the instance type name.  EX: e2-custom-4-2048 will be 4 CPU and 2048 memory"],
                    ["1.0.0", "3/5/24", "Initial release"]
                ];

                // append the changes to the table
                for (var i = 0; i < changes.length; i++) {
                    var row = "<tr><td>" + changes[i][0] + "</td><td>" + changes[i][1] + "</td><td>" + changes[i][2] + "</td></tr>";
                    console.log(row);
                    $("#change_table tbody").append(row);
                }

            });
        </script>
    </body>
</html>



