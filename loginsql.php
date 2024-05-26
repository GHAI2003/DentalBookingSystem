<?php
include 'database.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $company = $_POST['company'];

    // Check the database
    $stmt = $db->prepare("SELECT * FROM company WHERE name = :company AND email = :username AND password = :password LIMIT 1");
    $stmt->execute([':company' => $company, ':username' => $username, ':password' => $password]);
    
      
    // Fetch result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
       
       
        header("Location: admin.php?company=" . urlencode($company));
        exit(); // Stop further execution
      
    } else {
        $error_message = 'Invalid Username or Password!';
       
    }
    
    include('login.php');

}
?>
