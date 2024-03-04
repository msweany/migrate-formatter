<?php 
session_start();
require_once('config.php');
require_once('functions.php');

// get the CSV data from the session
$csvData = $_SESSION['csvData'];
// remove the first row, it's the header
array_shift($csvData);

// Retrieve raw POST data
$inputJSON = file_get_contents('php://input');
// Decode JSON data
$inputData = json_decode($inputJSON, true);

// determine if we're using total storage or line level storage
if($inputData['required']['storage'] == 'storage_column'){
    // it's total storage, split it among all machines
    $useStorageColumn = false;
    // count all rows in the CSV, minus the header
    $totalRows = count($csvData) - 1;
    // divide the total storage by the # of rows
    $storagePerMachine = intval($inputData['required']['total_storage']) / $totalRows;
    // round up to the next 10's
    $storagePerMachine = ceil($storagePerMachine / 10) * 10;    
}else{
    // it's line level storage, use the column header
    $useStorageColumn = true;
    $storagePerMachineIndex = $inputData['required']['storage'];
}

// was the OS column selected?
if($inputData['optional']['os'] != "Select"){
    $osColumn = "selected";
}else{
    $osColumn = "not selected";
}

// what column is the instane type in?
$instanceTypeIndex = $inputData['required']['instance_type'];

// Extract all the instance type values from each sub-array
$instanceValues = array_column($csvData, $instanceTypeIndex);

// Get unique values
$instanceTypes = array_unique($instanceValues);

# implode and trim the array to append it as a GET variable
$instances = implode(',', $instanceTypes); 

# count how many unique instance types there are
$totalInstances = count($instanceTypes);
# log this number 
//logThis("instance_count:".$totalInstances);

// call the Azure function to get the data from the database
$url = $awsUrl."checkInstances?code=".$apiKey."&instances=".$instances;

$response = sendRequest($url);

// counting integer
$i=1;
// connect all the values to the CSV uploads
if ($response) {
    $json = json_decode($response); 
    # returned intance types/cpu array
    $output = array();
    foreach($csvData as $row) {
        # set up a variable to add to the object
        $thisRow = array();
        $thisRow["*Server name"]="VM".$i;

        # get the csvData instance_type so we we look it up agains the json data for cpu and memory
        $instanceType = trim($row[$instanceTypeIndex]);
        
        # search $json for id = $instanceType
        $index = array_search($instanceType, array_column($json, 'id'));
        # get the cpu and memory values
        $cpu = $json[$index]->cpu;
        $memory = $json[$index]->memory;
        
        $thisRow["*Cores"]=$cpu;
        $thisRow["*Memory (In MB)"]=$memory;
        // match the OS or use Server 2022 as a default
        $thisRow["*OS name"] = matchOS($row[$inputData['optional']['os']]);
        $thisRow["Number of disks"]="1";
        if($useStorageColumn){
            $storagePerMachine = $row[$storagePerMachineIndex];
        }
        $thisRow["Disk 1 size (In GB)"]=$storagePerMachine;
        # copy over the original instance type and any optional columns selected
        $thisRow["original_instance_type"]=$instanceType;
        
        // check if there are any optional columns we need to add
        if($inputData['optional']){
            foreach($inputData['optional'] as $key => $value){
                if($value != 'Select'){
                    $thisRow['original_'.$key] = $row[intval($value)];
                }
            }
        }
        $i++;
        $output[] = $thisRow;
    }
    
    // write the CSV file and return the CSV file name
    $filename = writeCSV($output);

    // log the activity
    if($env == 'prod'){
        $message = "aws file created.";
        $rows = count($output);
        if($useStorageColumn){
            $storage = "CSV column selected";
        }else{
            $storage = "entered manually";
        }
        logThis("aws",$message,$rows, $storage, $osColumn);
    }
    
    header("Content-Type: application/json; charset=UTF-8");
    // return the file name
    $return = array(
        'status' => 100,
        'message' => 'File created',
        'filename' => $filename
    );
    print json_encode($return);
}else{
    print "no response";
}
?>