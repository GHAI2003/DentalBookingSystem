<?php include "header.php"; ?>
<?php include "database.php"; ?>
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
</head>
<main>
<h2 style="font-size: 2.5em; font-weight: bold; color: #333; text-align: center; margin-top: 20px;">Log in</h2>
<section>
        <div class = "login_form" >
            <form action="loginsql.php" method="POST">
                <label for="username">Username:</label><br>
                <input type="email" id="username" name="username" required><br>

                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required><br>              
                    <?php
                    $query = "SELECT * FROM company";
                    $result = $db->query($query);
                    $companies = $result->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                <select id="company" name="company" class="form-control">

                    <?php foreach ($companies as $company) {
                        echo '<option value="' .
                            htmlspecialchars($company["name"]) .
                            '">' .
                            htmlspecialchars($company["name"]) .
                            "</option>";
                    } ?>
                </select>
                  <p class = "e_message">
                    <?php if (!empty($error_message)):
                        echo $error_message;
                    endif; ?>
                  </p>
                   <input type="submit" value="Login">
                   </form>     
         </div>
</section>    
</main>
<br>
<?php include "footer.php"; ?>
