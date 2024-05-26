<!DOCTYPE html>
<?php include "header.php"; ?>
<?php include "database.php"; ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Add/Remove Timeslots</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="admin.css">

    <nav class="navi">
    <ul>
    <li>
        <a href="index.php">Home</a>
        </li>
        <li>
            <a href="register.php">Register</a>
        </li>
       
        <li class="logout-button">
            <a href="index.php">Log out</a>
        </li>
    </ul>
    </nav>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</head>
<body>
    <div class="container">
        <header>
            <h1>Add/Remove Timeslots <?php echo $_GET["company"] ?? ""; ?></h1>
        </header>

        <main>
            <section id="addRemoveTimeslots">
                <h2>Add/Remove Timeslots</h2>
                <form id="timeslotForm" method="post">
                    
                   
                    
                    <div class="form-group">
                        <label for="date">Date:</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label>Timeslots:</label><br>
                        <?php
                        $timeslots = timeslots(10, 0, "09:00", "15:00");
                        foreach ($timeslots as $ts) {
                            echo "<div class='form-check'>";
                            echo "<input type='checkbox' class='form-check-input' name='timeslots[]' value='" .
                                $ts .
                                "' id='timeslot_" .
                                $ts .
                                "'>";
                            echo "<label class='form-check-label' for='timeslot_" .
                                $ts .
                                "'>" .
                                $ts .
                                "</label>";
                            echo "</div>";
                        }
                        ?><br>
                    </div>
                    <button type="submit" class="btn btn-success" name="addTimeslots">Add Timeslots</button>
                    <button type="submit" class="btn btn-danger" name="removeTimeslots">Remove Timeslots</button>
                </form>
            </section>
        </main>

        <section id="existingTimeslots">
            <h2>Existing Timeslots</h2>
            <?php
            $mysqli = new mysqli("127.0.0.1", "root", "", "bookingcalendar");
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            $company = $_GET["company"];
            $stmt = $mysqli->prepare(
                "SELECT DISTINCT date, timeslot, company_name FROM booking WHERE company_name = ?  AND name = ''"
            );
            $stmt->bind_param("s", $company);

            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>Date: " .
                        $row["date"] .
                        ", Timeslot: " .
                        $row["timeslot"] .
                        ", Company Name: " .
                        $row["company_name"] .
                        "</li>";
                }
                echo "</ul>";
            } else {
                echo "No timeslots found.";
            }
            $stmt->close();
            $mysqli->close();
            ?>
        </section>

        <section id="bookings">
           <h2>Bookings with Names</h2>
            <?php
            $mysqli = new mysqli("127.0.0.1", "root", "", "bookingcalendar");
            if ($mysqli->connect_error) {
                die("Connection failed: " . $mysqli->connect_error);
            }
            $company = $_GET["company"];
            $stmt = $mysqli->prepare(
                "SELECT DISTINCT date, timeslot, name, company_name FROM booking WHERE company_name = ? AND name <> ''"
            );
            $stmt->bind_param("s", $company);

            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    echo "<li>Date: " .
                        $row["date"] .
                        ", Timeslot: " .
                        $row["timeslot"] .
                        ", Name: " .
                        $row["name"] .
                        ", Company Name: " .
                        $row["company_name"] .
                        "</li>";
                }
                echo "</ul>";
            } else {
                echo "No bookings found.";
            }
            $stmt->close();
            $mysqli->close();
            ?>
        </section>     
        </section>
    </div>

    <?php
    $msg = "";
    $date = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $mysqli = new mysqli("127.0.0.1", "root", "", "bookingcalendar");
        if ($mysqli->connect_error) {
            die("Connection failed: " . $mysqli->connect_error);
        }
        if (isset($_POST["addTimeslots"])) {
            $date = $_POST["date"];
            $timeslots = $_POST["timeslots"];
            $company = $_GET["company"];
            foreach ($timeslots as $timeslot) {
                $stmt = $mysqli->prepare(
                    "INSERT INTO booking (date, timeslot, company_name) VALUES (?, ?, ?)"
                );
                $stmt->bind_param("sss", $date, $timeslot, $company);
                if ($stmt->execute()) {
                    $msg .=
                        "Timeslot added successfully: " . $timeslot . "<br>";
                    $success = true;
                } else {
                    $msg .= "Error adding timeslot: " . $mysqli->error . "<br>";
                }
                $stmt->close();
            }
            echo "<script>window.location.href = window.location.href;</script>";
        }
        if (isset($_POST["removeTimeslots"])) {
            $date = $_POST["date"];
            $timeslotsToRemove = $_POST["timeslots"];
            $company = $_GET["company"];
            foreach ($timeslotsToRemove as $timeslot) {
                $stmt = $mysqli->prepare(
                    "DELETE FROM booking WHERE date = ? AND timeslot = ? AND company_name = ?"
                );
                $stmt->bind_param("sss", $date, $timeslot, $company);
                if ($stmt->execute()) {
                    $msg .=
                        "Timeslot removed successfully: " . $timeslot . "<br>";
                    $success = true;
                    // Initialize the success flag
                } else {
                    $msg .=
                        "Error removing timeslot: " . $mysqli->error . "<br>";
                }
                $stmt->close();
            }
            echo "<script>window.location.href = window.location.href;</script>";
        }
        $mysqli->close();
    }
    function timeslots($duration, $cleanup, $start, $end)
    {
        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = new DateInterval("PT" . $duration . "M");
        $cleanupinterval = new DateInterval("PT" . $cleanup . "M");
        $slots = [];
        for (
            $intStart = $start;
            $intStart < $end;
            $intStart->add($interval)->add($cleanupinterval)
        ) {
            $endPeriod = clone $intStart;
            $endPeriod->add($interval);
            if ($endPeriod > $end) {
                break;
            }
            $slots[] =
                $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
        }
        return $slots;
    }
    ?>
</body>
</html>
