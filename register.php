<!DOCTYPE html>
<?php include "header.php"; ?>
<nav class="navi">
    <ul>
    <li>
        <a href="index.php">Home</a>
        </li>
        <li>
            <a href="register.php">Register</a>
        </li>
        <li>
            <a href="login.php">Log in</a>
        </li>
        
    </ul>
    </nav>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic Registration</title>
</head>
<section>
<body>
<h2 style="font-size: 3em; font-weight: bold; color: #333; text-align: center; margin-top: 30px;">Register Your Company</h2>
    <br>
    <div class = "login_form" >
    <form action="registersql.php" method="post">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>
        
        <label for="same_password">Confirm Password:</label><br>
        <input type="password" id="same_password" name="same_password" required><br>

        <input type="submit" value="Register">
        
         <p class = "e_message">
                    <?php if (!empty($message)):
                        echo $message;
                    endif; ?>
         </p>    
    </form>
    </div>
</section>
<br>
<?php include "footer.php"; ?>
