<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $user_id = 1;  // Ideally, this should come from session or logged-in user
    $board_name = $_POST['board_name'];

    $sql = "INSERT INTO pinterest_boards (user_id, board_name) VALUES ('$user_id', '$board_name')";
    
    if ($conn->query($sql) === TRUE) {
        echo "New board created successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Board</title>
</head>
<body>

<h1>Create a New Board</h1>

<form method="POST" action="create_board.php">
    <label for="board_name">Board Name:</label>
    <input type="text" id="board_name" name="board_name" required>
    <input type="submit" name="submit" value="Create Board">
</form>

</body>
</html>
