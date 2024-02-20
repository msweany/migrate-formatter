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
        
        
    }elseif($fileType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'){      
        // it's a xlsx file, let them know to save as a CSV and try again
        $output = array();
        $output['status'] = 109;
        $output['type'] = 'xlsx';
    }else{
        $output['status'] = 200;
        $output['message'] = 'Invalid file type.  Please upload a CSV or xlsx file.';
    }

}


    // now we need to check info about the file.
    // check if it's an AWS file
    // expect CSV
    /*
        .nano,.micro,.small,.large,.xlarge,.2xlarge,.4xlarge,.8xlarge,.9xlarge,.10xlarge,.12xlarge,.16xlarge,.18xlarge,.24xlarge,.32xlarge,.metal
    */
    

    // check if it's a GCP file  #https://cloud.google.com/compute/docs/general-purpose-machines#c3_series
    // expect xlsx, but could be csv
    /*
        -standard-,tau-t2d-,-highgpu-,-ultragpu-,-highcpu-,-highmem-,-ultramem-,-megamem-,-hypermem-,-micro,-small
    */

    // check if it's a rvtools file
    // expect xlsx
    /* expect first tab to be vInfo with first column to be VM
        grab VM, CPUs, Memory, Provisioned MiB (could also grab In Use MiB),OS according to the configuration file, OS according to the VMware Tools 
    */

    // it's custom, let's work on that
    // expect xlsx or csv
    /*
        We need to figure out what's in there and match it up with what we want.
        Required columns CPU, Memory
        Optional columns: VM, OS, Storage, Machine Count
    */
    /*
    // Check if the file is a valid CSV file
    if (($handle = fopen($tmp_name, "r")) !== FALSE) {
        // set up an $output array to return to the user
        $output = [];
        // Read and display the contents of the uploaded CSV file
        $csvArray = [];
        while (($data = fgetcsv($handle)) !== FALSE) {
            $csvArray[] = $data;
        }
        fclose($handle);
    }

    // get the total memory from the POST
    $totalMemory = $_POST['totalMemory'];

    // look to see if Instance_Type is in the first row
    $instanceTypeIndex = array_search('Instance_Type', $csvArray[0]);
    
    // if Instance_Type is not in the first row, return an error
    if ($instanceTypeIndex === false) {
        $output['error'] = 'Instance_Type not found in CSV file';
        echo json_encode($output);
        exit;
    }

    // Convert the CSV data to JSON while keeping the first row as the key names, if there are any blank rows, skip them.
    $json = [];
    foreach($csvArray as $i => $row) {
        if ($i === 0) {
            continue;
        }
        if (empty($row[0])) {
            continue;
        }
        $trimmedRow = array_map('trim', $row);  // Trim all values in the row
        $json[] = array_combine($csvArray[0], $trimmedRow);
    }

    // let's get all unique Instance_Types
    $instanceTypes = array_unique(array_column($json, 'Instance_Type'));
    
    //print_r($instanceTypes);
  
    # implode and trim the array to append it as a GET variable
    $instances = implode(',', $instanceTypes); 
    # trim each element in the array

    # count how many unique instance types there are
    $totalInstances = count($instanceTypes);
    # log this number 
    logThis("instance_count:".$totalInstances);
   
    // call the Azure function to get the data from the database
    $url = "https://aws-migrate-function.azurewebsites.net/api/checkInstances?code=".$apiKey."&instances=".$instances;
  
    #use CURL to make the function call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    # get
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    // Count the # of JSON objects returned
    $totalRecords = count($json);
    // divide the total memory by the # of records
    $memoryPerMachine = intval($totalMemory) / $totalRecords;
    // round up to the next 10's
    $memoryPerMachine = ceil($memoryPerMachine / 10) * 10;

    // counting integer
    $i=1;
    // connect all the values to the CSV uploads
    if ($response) {
        $json2 = json_decode($response); 
        # returned intance types/cpu array
        //print_r($json2);
        $output = array();
        foreach($json as $row) {
            # set up a variable to add to the object
            $thisRow = array();
            $thisRow["*Server name"]="VM".$i;

            # get the json instance_type so we we look it up agains the json2 data for cpu and memory
            $instanceType = trim($row['Instance_Type']);
            
            # search $json2 for id = $instanceType
            $index = array_search($instanceType, array_column($json2, 'id'));
            # get the cpu and memory values
            $cpu = $json2[$index]->cpu;
            $memory = $json2[$index]->memory;
            
            $thisRow["*Cores"]=$cpu;
            $thisRow["*Memory (In MB)"]=$memory;
            $thisRow["*OS name"]="Microsoft Windows Server 2016 (64-bit)";
            $thisRow["Number of disks"]="1";
            $thisRow["Disk 1 size (In GB)"]=$memoryPerMachine;
            # add all the original values from the CSV
            $thisRow = array_merge($thisRow, $row);
            $i++;
            $output[] = $thisRow;
        }
        //print_r($output);
        # convert the array to a CSV
        // Open a file pointer for writing
        $fp = fopen('MigrateFile.csv', 'w');

        # get the headers in the first row
        $headers = array_keys($output[0]);

        // Add headers to the csv file
        fputcsv($fp, $headers);

        // Loop through the nested array and write each sub-array as a CSV row
        foreach ($output as $row) {
            fputcsv($fp, $row);
        }

        // Close the file pointer
        fclose($fp);

        // Set the appropriate headers for a CSV file download
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="MigrateFile.csv"');

        // Read and output the file content to the browser
        readfile('MigrateFile.csv');
        exit;
        
        
    }else{
        print "no response";
    }
    */
echo json_encode($output);
?>
