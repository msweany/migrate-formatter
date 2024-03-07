<?php
function logThis($type,$message,$rows=null, $storage = null, $os = null, $multi = null, $groups = null){
    #use CURL to make the function call
    $url = getEnv('functionUrlGeneral')."log?code=".getEnv('functionKey')."&email=".$_SERVER['HTTP_X_MS_CLIENT_PRINCIPAL_NAME']."&info=".urlencode($message)."&type=".$type."&storage=".urlencode($storage)."&multi=".$multi."&groups=".urlencode($groups)."&os=".urlencode($os)."&rows=".$rows;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    # get
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function sendRequest($url, $request = "GET"){
    #use CURL to make the function call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "$request");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

function writeCSV($output){
    # add the date stamp to the file name.  Format is HHMMSS.MMDDYY
    $date_stamp = date("His.mdy");
    $filename = 'MigrateReadyFile.'.$date_stamp.'.csv';
    # convert the array to a CSV
    // Open a file pointer for writing
    $fp = fopen('../files/'.$filename, 'a');

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
    return $filename;
}

// limit the types of OS's based on what's entered
function matchOS($OS){
    $OS = strtolower($OS);
    if(strpos($OS, "redhat") !== false){
        return "Linux";
    }elseif(strpos($OS, "red hat") !== false){
        return "Linux";
    }elseif(strpos($OS, "linux") !== false){
        return "Linux";
    }elseif(strpos($OS, "ubuntu") !== false){
        return "Linux";
    }elseif(strpos($OS, "suse") !== false){
        return "Linux";
    }elseif(strpos($OS, "google") !== false){
        return "Linux";
    }elseif(strpos($OS, "amazon") !== false){
        return "Linux";
    }elseif(strpos($OS, "container") !== false){
        return "Linux";
    }else{
        return "Microsoft Windows Server 2022 (64-bit)";
    }
}