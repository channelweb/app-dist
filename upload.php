<?php
$folder = 'files';
 
if (!empty($_FILES)) {
    $tempFile = $_FILES['file']['tmp_name'];
    $uploadId = time();

    $targetPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . $folder 
        . DIRECTORY_SEPARATOR . $uploadId . DIRECTORY_SEPARATOR;
    mkdir($targetPath);
    $targetFile =  $targetPath. $_FILES['file']['name'];
    move_uploaded_file($tempFile, $targetFile);


    require('php' . DIRECTORY_SEPARATOR . 'ipaTools.php');
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

