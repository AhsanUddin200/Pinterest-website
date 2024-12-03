<?php
include 'db.php';

if (isset($_POST['submit'])) {
    // Debugging output
    echo "Form submitted.<br>";
    echo "POST data: <pre>" . print_r($_POST, true) . "</pre>";

    if (isset($_POST['board_id']) && isset($_POST['image_id'])) {
        $image_id = $_POST['image_id'];
        $board_id = $_POST['board_id'];

        // Check if the image_id exists
        $check_image_sql = "SELECT * FROM pinterest_images WHERE id = '$image_id'";
        $check_image_result = $conn->query($check_image_sql);

        if ($check_image_result->num_rows > 0) {
            // Check if the board_id exists
            $check_board_sql = "SELECT * FROM pinterest_boards WHERE id = '$board_id'";
            $check_board_result = $conn->query($check_board_sql);

            if ($check_board_result->num_rows > 0) {
                // Insert into pinterest_image_boards
                $sql = "INSERT INTO pinterest_image_boards (image_id, board_id) VALUES ('$image_id', '$board_id')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "Image saved to board successfully.";
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Error: The selected board does not exist.";
            }
        } else {
            echo "Error: The selected image does not exist.";
        }
    } else {
        echo "Error: board_id or image_id is not set.";
    }
}
?>

<form method="POST" action="save_image_to_board.php">
    <label for="board_id">Select Board:</label>
    <select name="board_id" id="board_id" required>
        <?php
        $user_id = 1;  // Ideally, this should come from session or logged-in user
        $result = $conn->query("SELECT * FROM pinterest_boards WHERE user_id = '$user_id'");
        
        // Debugging: Check if boards are fetched
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['board_name']}</option>";
            }
        } else {
            echo "<option value=''>No boards available</option>";
        }
        ?>
    </select>
    
    <label for="image_id">Select Image:</label>
    <select name="image_id" id="image_id" required>
        <?php
        $result = $conn->query("SELECT * FROM pinterest_images WHERE user_id = '$user_id'");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['title']}</option>";
        }
        ?>
    </select>

    <input type="submit" name="submit" value="Save Image to Board">
</form>
