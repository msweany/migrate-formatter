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
        $computerCount = $inputData['required']['computer_count'];
        if($computerCount == ' ' || $computerCount == '' || $computerCount == 0 || $computerCount == null){
            // skip the row if the computer count is 0
            continue;
        }
    }
    
    // loop through the computer count
    for($x = 0; $x < $computerCount; $x++){
        // set up a variable to add to the object
        $thisRow = array();
        $thisRow["*Server name"]="VM".$i;
        
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
        # copy over the original instance type and any optional columns selected
        $thisRow["original_instance_type"]=$row[$inputData['required']['instance_type']];
        
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
}

// write the CSV file and return the CSV file name
$filename = writeCSV($output);

// return the file name
$output = array(
    'status' => 100,
    'message' => 'File created',
    'filename' => $filename
);
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($output);

?>