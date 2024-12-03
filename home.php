<?php
include 'db.php';
session_start(); // Start the session

// Check if user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Fetch user data from the database
$sql_user = "SELECT * FROM pinterest_users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();

if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc(); // Fetch user data
} else {
    // Handle case where user is not found
    echo "User not found.";
}

// Fetch all images from the database
$sql = "SELECT * FROM pinterest_images";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinterest Home</title>
    <style>
        /* Basic Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        /* Header Styling */
        .header {
            background-color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #e60023;
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 30px; /* Adjust logo size */
            margin-right: 10px;
        }

        .search-bar {
            width: 500px;
            padding: 8px;
            border-radius: 20px;
            border: 1px solid #ddd;
            margin: 0 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .message-icon {
            width: 25px;
            height: 25px;
            background: url('message-icon.png') no-repeat center center;
            background-size: cover;
            cursor: pointer;
            margin-left: 10px;
        }

        /* Button Styles */
        .button {
            background-color: #e60023;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px; /* Adjusted padding for smaller button */
            cursor: pointer;
            margin-left: 10px; /* Space between logo and button */
            font-size: 14px; /* Smaller font size */
        }

        /* Image Grid */
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .image-grid img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Image Title */
        .image-title {
            text-align: center;
            margin-top: 5px;
            font-weight: bold;
        }

        /* Upload Button Style */
        .upload-button {
            width: 50px;
            height: 50px;
            background-color: #e60023;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            right: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="logo">  
            <img src="data:image/png;base64,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" alt="Logo"> <!-- Replace with your logo path -->
            Pinterest
        </div>
        <button class="button" onclick="window.location.href='create_board.php';">Create Board</button>
        <input type="text" class="search-bar" placeholder="Search for...">
        <div class="user-profile">
            <img src="uploads/<?php echo $user['profile_pic']; ?>" alt="Profile Picture">
            <span><?php echo $user['username']; ?></span> <!-- User Name -->
            <div class="message-icon"></div> <!-- Message Icon -->
            <form method="POST" action="follow_user.php" style="display:inline;">
                <input type="hidden" name="following_id" value="<?php echo $user['id']; ?>">
                <input type="submit" name="submit" value="Follow" style="margin-left: 10px;">
            </form>
        </div>
    </div>

    <!-- Display Images -->
    <div class="image-grid">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="image-item">
                <img src="<?php echo $row['image_path']; ?>" alt="<?php echo $row['title']; ?>">
                <p class="image-title"><?php echo htmlspecialchars($row['title']); ?></p> <!-- Display the title below the image -->
            </div>
        <?php } ?>
    </div>

    <!-- Upload Button -->
    <div class="upload-button" onclick="window.location.href='upload_image.php';">
        +
    </div>

</body>
</html>


