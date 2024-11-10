<?php
include "connection.php";
if($_SERVER['REQUEST_METHOD']== 'POST'){
    $title = $_POST["title"];
    $description = $_POST["description"];

    $sql_insert = "INSERT INTO `notes` (`title`, `description`) VALUES ('$title', '$description')";
    $result = mysqli_query( $conn, $sql_insert);

    if ($result) {
        // Redirect after successful form submission to avoid resubmission on refresh
        // header("Location: /crud_php/index.php");
        echo'done';    
        $insert = true;
        // exit();
    } else {
        echo "Error inserting data.";
    }
}


?>