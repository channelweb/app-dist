<?php

if (!empty($_GET['id'])) {
    $folder = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'files' 
        . DIRECTORY_SEPARATOR . $_GET['id'];

    if (is_dir($folder)) {
        $files = glob($folder . '/*');
        foreach ($files as $file) {
            unlink($file);
        }
        rmdir($folder);
    }

}

header('Location: ' . 'list.php');
