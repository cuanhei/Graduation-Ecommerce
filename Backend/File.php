<?php
function uploadedImg($uploadDir,$file) {

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $originalName = basename($file["name"]);
    $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'png', 'jpeg'];
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }

    // Generate a unique file name
    $newFileName = uniqid() . "." . $fileType;
    $targetFilePath = $uploadDir . $newFileName;

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return $targetFilePath;
    }

    return false;
}

function deleteFile($filePath) {
    // Check if file exists
    if (file_exists($filePath)) {
        // Try to delete the file
        if (unlink($filePath)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

