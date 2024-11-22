<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../backend/config/database.php';


// Returns the file upload path
function getFileUploadPath($original_filename, $upload_subfolder_name = 'uploads') {
    $path_segments = [$upload_subfolder_name, basename($original_filename)];
    return join(DIRECTORY_SEPARATOR, $path_segments);
}


// Checks a file for image-ness.
// Returns true if the file is an image. False, otherwise.
function checkValidImage($temporary_path, $new_path) {
    
    $allowed_mime_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $allowed_file_extensions = ['jpeg', 'jpg', 'png', 'JPEG', 'JPG', 'PNG'];

    // Returns info about the file
    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type = getimagesize($temporary_path)['mime'];

    // Verify that the file has valid mime type & file extension
    $is_file_extension_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $is_mime_type_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $is_file_extension_valid && $is_mime_type_valid;
}

function addImageToDatabase($db, $image_path) {
    $query = "INSERT INTO image(image_url) VALUES (:image_url)";

    $statement = $db->prepare($query);
    $statement->bindValue(":image_url", $image_path);

    if ($statement->execute()) {
        // Retrieve the ID for new review post
        return $db->lastInsertId();
    }
}

function getImageUrlFromDatabase($db, $image_id) {
    if (!$image_id) {
        return false;   // return false if image_id is empty
    }
    $query = "SELECT image_url FROM image WHERE image_id = :image_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':image_id', $image_id, PDO::PARAM_INT);

    if ($statement->execute()) {
        $row = $statement->fetch();
        return $row[0];

    }
}


// when a review is removed, the image should be removed too
// when a review is created, the image could be created too (if user submits it in the form)



?>


