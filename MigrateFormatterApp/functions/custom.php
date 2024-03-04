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

// was there a multiple column selected
if(!$inputData['trackClicks']['singleLine']){
    $multiColumn = "multi-column";
}else{
    $multiColumn = "single-column";
}

// were business units selected?
if($inputData['trackClicks']['group']){
    $groupsColumn = "selected";
}else{
    $groupsColumn = "not selected";
}

$groupName[] = 1;
// counting integer
$i=1;
$output = array();
foreach($csvData as $row) {
    // determine if this is one computer per row or multiple
    if($inputData['trackClicks']['singleLine']){
        // it's single, use 1 computer
        $computerCount = 1;
    }else{
        // it's multiple, get the count column
        $computerCount = $row[$inputData['required']['computer_count']];
        if($computerCount == ' ' || $computerCount == '' || $computerCount == 0 || $computerCount == null){
            // skip the row if the computer count is 0
            continue;
        }
    }
    
    // loop through the computer count
    for($x = 0; $x < $computerCount; $x++){
        // set up a variable to add to the object
        $thisRow = array();
        // if there is a business unit column, replace the computer name with the business unit
        if($inputData['trackClicks']['group']){
            # remove any spaces from the business unit
            $group = str_replace(' ', '_', $row[$inputData['required']['group']]);
        }else{
            $group = 'VM';
        }
        // if $groupName[$group] is not set, set it to 1
        if(!isset($groupName[$group])){
            $groupName[$group] = 1;
        }
        $thisRow["*Server name"]=$group."_".$groupName[$group];
        $groupName[$group]++;
        
        // get the cpu and memory values
        $cpu = $row[$inputData['required']['cpu']];
        $memory = $row[$inputData['required']['memory']];
        // check if we need to convert the memory to MB
        // true == GB, false == MB
        if($inputData['trackClicks']['memory']){
            $memory = $memory * 1024;
        }
        
        $thisRow["*Cores"]=$cpu;
        $thisRow["*Memory (In MB)"]=$memory;
        // match the OS or use Server 2022 as a default
        $thisRow["*OS name"] = matchOS($row[$inputData['optional']['os']]);
        
        $thisRow["Number of disks"]="1";
        if($useStorageColumn){
            $storagePerMachine = $row[$storagePerMachineIndex];
        }
        $thisRow["Disk 1 size (In GB)"]=$storagePerMachine;
        
        // check if there are any optional columns we need to add
        if($inputData['optional']){
            foreach($inputData['optional'] as $key => $value){
                if($value != 'Select'){
                    $thisRow['original_'.$key] = $row[intval($value)];
                }
            }
        }
        $output[] = $thisRow;
    }
}

// write the CSV file and return the CSV file name
$filename = writeCSV($output);

// log the activity
if($env == 'prod'){
    $message = "custom file created.";
    $rows = count($output);
    if($useStorageColumn){
        $storage = "CSV column selected";
    }else{
        $storage = "entered manually";
    }

    logThis("custom",$message,$rows,$storage,$osColumn,$multiColumn,$groupsColumn);
}

header("Content-Type: application/json; charset=UTF-8");
// return the file name
$return = array(
    'status' => 100,
    'message' => 'File created',
    'filename' => $filename
);
print json_encode($return);
?>