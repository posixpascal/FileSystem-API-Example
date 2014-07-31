<?php
// This file is used to determine file size as well as sending chunks to the browser
    error_reporting(E_ALL);
    $CHUNK_SIZE = 512;

    // not getting the filename from user because they can
    // potentially spy your filesystem by passing malicious filenames as parameters. never trust the user.
    $files = array('example.csv', 'example.txt');

    $filesCount = count($files);

    if (!isset($_POST['requestFile']) || !isset($_POST['requestType'])){ die('{"error": "No requestFile/requestType set in POST request"}'); }

    // get file
    $requestFileId = intval($_POST['requestFile']);
    if ($filesCount < $requestFileId - 1){
        return json_encode(array("error" => "nope."));
    }

    $requestedFileName = $files[$requestFileId];
    $requestedFilePath = "files/".$requestedFileName;


    $requestedType = $_POST['requestType'];
    if ($requestedType == "content"){

        $chunkNumber = intval($_POST['chunkPosition']);
        if (!$chunkNumber){ $chunkNumber = 0; }

        $cursorPosition = $CHUNK_SIZE * $chunkNumber;

        $filePointer = fopen($requestedFilePath, "rb+");
        fseek($filePointer, $cursorPosition);
        $chunk = fread($filePointer, $CHUNK_SIZE);
        fclose($filePointer);
        header('Content-type: '.mime_content_type($requestedFilePath));
        echo $chunk;

        return;


    } elseif ($requestedType == "metadata"){
        $data = array(
            "fileSize" => filesize($requestedFilePath),
            "fileName" => $requestedFileName,
            "fileMimeType" => mime_content_type($requestedFilePath)
        );

    } else{
        $data = array(
            "error" => "nope"
        );

    }

echo json_encode($data);
?>