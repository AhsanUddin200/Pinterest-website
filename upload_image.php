<?php
include 'db.php';

if (isset($_POST["submit"])) {
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Directory to save the uploaded images
        $target_dir = "uploads/";

        // Check if the uploads folder exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);  // Create the folder if it doesn't exist
        }

        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Save the file to the uploads folder
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Insert the image details into the database
                $user_id = 1; // Ideally, fetch this from session or logged-in user
                $title = $_POST['title'];
                $tags = $_POST['tags']; // Get tags from the form

                $sql = "INSERT INTO pinterest_images (user_id, title, image_path, tags) VALUES ('$user_id', '$title', '$target_file', '$tags')";

                if ($conn->query($sql) === TRUE) {
                    echo "Image uploaded successfully.";
                    header("Location: home.php");
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Sorry, your file is not an image.";
        }
    } else {
        echo "Please select an image to upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
</head>
<body>

    <h1>Upload an Image</h1>
    <form method="POST" action="upload_image.php" enctype="multipart/form-data">
        <label for="title">Image Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="tags">Tags (comma-separated):</label>
        <input type="text" id="tags" name="tags" required><br><br>

        <label for="image">Choose Image:</label>
        <input type="file" name="image" id="image" required><br><br>

        <input type="submit" name="submit" value="Upload">
    </form>

</body>
</html>
