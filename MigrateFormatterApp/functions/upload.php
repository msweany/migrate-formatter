<?php
include 'header.php';
session_start();
#include 'functions.php';
header("Content-Type: application/json; charset=UTF-8");

// create arrays of what to look for for each type of file
$aws = array('.nano','.micro','.small','.large','.xlarge','.2xlarge','.4xlarge','.8xlarge','.9xlarge','.10xlarge','.12xlarge','.16xlarge','.18xlarge','.24xlarge','.32xlarge','.metal');
$gcp = array('-standard-', 'tau-t2d-', '-highgpu-', '-ultragpu-', '-highcpu-', '-highmem-', '-ultramem-', '-megamem-', '-hypermem-', '-micro', '-small');
$rvtools = array("VM", "Powerstate", "Template", "SRM Placeholder", "Config status", "DNS Name", "Connection state", "Guest state", "Heartbeat", "Consolidation Needed", "PowerOn", "Suspend time", "Creation date", "Change Version", "CPUs", "Memory", "NICs", "Disks", "Total disk capacity MiB", "min Required EVC Mode Key", "Latency Sensitivity", "EnableUUID", "CBT", "Primary IP Address", "Network #1", "Network #2", "Network #3", "Network #4", "Network #5", "Network #6", "Network #7", "Network #8", "Num Monitors", "Video Ram KiB", "Resource pool", "Folder ID", "Folder", "vApp", "DAS protection", "FT State", "FT Role", "FT Latency", "FT Bandwidth", "FT Sec. Latency", "Provisioned MiB", "In Use MiB", "Unshared MiB", "HA Restart Priority", "HA Isolation Response", "HA VM Monitoring", "Cluster rule(s)", "Cluster rule name(s)", "Boot Required", "Boot delay", "Boot retry delay", "Boot retry enabled", "Boot BIOS setup", "Reboot PowerOff", "EFI Secure boot", "Firmware", "HW version", "HW upgrade status", "HW upgrade policy", "HW target", "Path", "Log directory", "Snapshot directory", "Suspend directory", "Annotation", "NB_LAST_BACKUP", "Patching", "DR", "Linux-Patch", "IT Owners", "Application Type", "Unix-Team-Linux-VMs", "Datacenter", "Cluster", "Host", "OS according to the configuration file", "OS according to the VMware Tools", "VM ID", "SMBIOS UUID", "VM UUID", "VI SDK Server type", "VI SDK API Version", "VI SDK Server", "VI SDK UUID");

#$apiKey = getEnv('functionKey');
// check if a file was uploaded
if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0) {
    $tmp_name = $_FILES['csvFile']['tmp_name'];
    $output['filename'] = $tmp_name;
    // verify this is a CSV or xlsx
    $fileType = $_FILES['csvFile']['type'];
    if ($fileType != 'text/csv' && $fileType != 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        $output['status'] = 200;
        $output['message'] = 'Invalid file type.  Please upload a CSV or xlsx file.';
        echo json_encode($output);
        exit;
    }

    // set the counts to 0
    $awsCount = 0;
    $gcpCount = 0;
    $rvtoolsCount = 0;

    if($fileType == 'text/csv'){
        // Check if the file is a valid CSV file
        if (($handle = fopen($tmp_name, "r")) !== FALSE) {
            $output['status'] = 100;
            // set up an $output array to return to the user
            $output = [];
            // Read and display the contents of the uploaded CSV file
            $csvArray = [];
            while (($data = fgetcsv($handle)) !== FALSE) {
                // remove any empty rows
                if (!empty($data[0])) {
                    $csvArray[] = $data;
                }
            }
            fclose($handle);
            $_SESSION['csvData'] = $csvArray;
            
            // grab the headers in an array
            $headers = $csvArray[0];

            $rowCount = 0;
            // check the headers first to see if it's an rvtools file
            foreach ($headers as $header) {
                if (in_array($header, $rvtools)) {
                    $rvtoolsCount++;
                }
            }
            // if more than 70% of the headers match, it's an rvtools file
            if ($rvtoolsCount / count($headers) > .7) {
                $output['type'] = 'rvtools';
                $output['headers'] = $headers;
            }else{
                // Search for values in each row to see if it's a AWS or GPC file
                foreach ($csvArray as $row) {
                    // if the row doesn't have text, skip it
                    if (empty($row[0])) {
                        continue;
                    }
                    foreach ($row as $index => $cell) {
                        // Check if any part of the cell value contains any of the values in the $aws array
                        foreach ($aws as $awsValue) {
                            if (strpos($cell, $awsValue) !== false) {
                                // we matched, increment the aws count
                                $awsCount++;
                            }
                        }
                        // check to see if it's a GCP file
                        foreach ($gcp as $gcpValue) {
                            if (strpos($cell, $gcpValue) !== false) {
                                // we matched, increment the gcp count
                                $gcpCount++;
                            }
                        }
                    }
                    $rowCount++;
                }
                // conculsion
                $awsPercent = $awsCount / $rowCount;
                $gcpPercent = $gcpCount / $rowCount;
                // if more than 70% of the rows contain AWS values, it's an AWS file
                if ($awsPercent > .7) {
                    $output['type'] = 'aws';
                // if more than 70% of the rows contain GCP values, it's a GCP file
                }elseif ($gcpPercent > .7) {
                    $output['type'] = 'gcp';
                // othewise, it's a custom file
                }else{
                    $output['type'] = 'custom';
                }
            }
            

            // now we need to match up headers 
            $output['headers'] = $headers;
            
        }else{
            $output['status'] = 200;
            $output['message'] = 'Issue with CSV file';
        }
        
    }else{
        $output['status'] = 200;
        $output['message'] = 'Invalid file type.  Please upload a CSV or xlsx file.';
    }

}

echo json_encode($output);
?>
