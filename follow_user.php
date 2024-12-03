<?php
include 'db.php';

session_start(); // Start the session

if (isset($_POST['submit'])) {
    $follower_id = $_SESSION['user_id'];  // Fetch from session
    $following_id = $_POST['following_id']; // The user being followed

    $sql = "INSERT INTO pinterest_followers (follower_id, following_id) VALUES ('$follower_id', '$following_id')";
    
    if ($conn->query($sql) === TRUE) {
        echo "You are now following this user.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<form method="POST" action="follow_user.php">
    <label for="following_id">Select User to Follow:</label>
    <select name="following_id" id="following_id">
        <?php
        // Fetch users from the database
        $result = $conn->query("SELECT * FROM pinterest_users");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['username']}</option>";
        }
        ?>
    </select>

    <input type="submit" name="submit" value="Follow User">
</form>
