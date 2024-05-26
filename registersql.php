<?php
include "database.php"; ?>

<?php
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $same_password = $_POST["same_password"];
    if ($password === $same_password) {
        $sql =
            "INSERT INTO company (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $password);

        if ($stmt->execute()) {
            $message = "Registration successful";
        } else {
            $message = "Error: Unable to register";
        }
    } else {
        $message = "Error: Unable to register";
    }
    include "register.php";
}
?>
