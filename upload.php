<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$folder = 'files';
 
if (!empty($_FILES)) {
    $tempFile = $_FILES['file']['tmp_name'];
    if (empty($tempFile)) {
        die('error uploading file - error code: ' . $_FILES['file']['error']);
    }
    
    $uploadId = md5_file($tempFile);

    $targetPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $folder 
        . DIRECTORY_SEPARATOR . $uploadId . DIRECTORY_SEPARATOR;
    if (file_exists($targetPath)) {
        die('file already uploaded!');
    }

    mkdir($targetPath);
    $targetFile =  $targetPath. $_FILES['file']['name'];
    if (!move_uploaded_file($tempFile, $targetFile)) {
        die('error uploading file');
    }


    require('php/ipaTools.php');
    $ipa = new IpaTools($targetFile);
    $response = json_encode(array(
            'devices' => $ipa->getDevices(),
            'url' => $ipa->applink,
            'id' => $uploadId,
            'filename' => $_FILES['file']['name']
        ));

    header('Content-type: application/json');
    echo $response;
}

