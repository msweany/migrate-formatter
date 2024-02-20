<?php
header('Content-Type: application/json');

// delete the file in files
$filename = $_POST['filename'];
unlink('../files/'.$filename);

print json_encode(array('status' => 100, 'message' => 'File deleted'));
