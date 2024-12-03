<?php
include 'db.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password for security

    // Handle profile picture upload
    $profile_pic = 'default.jpg';  // Default profile picture

    // Check if a file is uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";  // Specify the directory for uploads
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

        // Check file type (you can add more checks here if needed)
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                $profile_pic = basename($_FILES["profile_pic"]["name"]);  // Save the filename to database
            } else {
                echo "<p style='color: red;'>Error uploading profile picture.</p>";
                return;
            }
        } else {
            echo "<p style='color: red;'>Invalid file type. Please upload an image.</p>";
            return;
        }
    }

    // Insert user into the database
    $sql = "INSERT INTO pinterest_users (username, email, password, profile_pic) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $profile_pic);

    if ($stmt->execute()) {
        // Redirect to login page after successful signup
        header("Location: login.php");
        exit;
    } else {
        echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.16/tailwind.min.css">
</head>
<body>
    <div class="container mx-auto my-8 p-6 bg-white shadow-lg rounded-lg max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6">Sign Up</h2>
        <form method="POST" enctype="multipart/form-data">
            <!-- Username Input -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-semibold text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="w-full p-3 mt-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Email Input -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="w-full p-3 mt-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Password Input -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="w-full p-3 mt-2 border border-gray-300 rounded-md" required>
            </div>

            <!-- Profile Picture Input -->
            <div class="mb-4">
                <label for="profile_pic" class="block text-sm font-semibold text-gray-700">Profile Picture (Optional)</label>
                <input type="file" id="profile_pic" name="profile_pic" class="w-full p-3 mt-2 border border-gray-300 rounded-md">
            </div>

            <!-- Submit Button (Red) -->
            <div class="mb-4">
                <button type="submit" class="w-full p-3 bg-red-500 text-white font-semibold rounded-md hover:bg-red-600 focus:outline-none">
                    Sign Up
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-sm text-gray-600">Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login</a></p>
        </div>
    </div>
</body>
</html>
