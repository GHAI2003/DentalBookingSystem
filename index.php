<!DOCTYPE html>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
</head>

<body>
    <h1>Book Your Dental Appointment Today</h1>
    <h1>Select Dental Clinic</h1>
<div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php 
                $query = "SELECT * FROM company";
                $result = $db->query($query);
                $companies = $result->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <select id="company" class="form-control">
                    <?php foreach ($companies as $company) {
                        echo '<option value="' .
                            htmlspecialchars($company["name"]) .
                            '">' .
                            htmlspecialchars($company["name"]) .
                            "</option>";
                    } ?>
                </select>

               <script>
              function book(date) {
                var selectedCompany = document.getElementById("company").value;
                    window.location.href = 'book.php?date=' + date + '&company=' + encodeURIComponent(selectedCompany);
                }
                </script>
              
               
                <br>
                <?php
                function build_calendar($month, $year)
                {
                    $daysOfWeek = [
                        "Sunday",
                        "Monday",
                        "Tuesday",
                        "Wednesday",
                        "Thursday",
                        "Friday",
                        "Saturday",
                    ];
                    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
                    $numberDays = date("t", $firstDayOfMonth);
                    $dateComponents = getdate($firstDayOfMonth);
                    $monthName = $dateComponents["month"];
                    $dayOfWeek = $dateComponents["wday"] + 1;
                    $calendar = "<div class='calendar-controls'>";
                    $calendar .= "<h2>$monthName $year</h2>";
                    $calendar .= "<div>";
                    $calendar .= "<a class='btn btn-primary' style='margin-right: 10px' href='?month=" .
                        ($month == 1 ? 12 : $month - 1) .
                        "&year=" .
                        ($month == 1 ? $year - 1 : $year) .
                        "'>Previous Month</a>";
                    
                    $calendar .= "<a class='btn btn-primary' style='margin-left: 10px' href='?month=" .
                        ($month == 12 ? 1 : $month + 1) .
                        "&year=" .
                        ($month == 12 ? $year + 1 : $year) .
                        "'>Next Month</a>";
                    $calendar .= "</div>";
                    $calendar .= "</div>";
                    $calendar .= "<table class='table table-bordered'>";
                    $calendar .= "<thead>";
                    $calendar .= "<tr>";
                    foreach ($daysOfWeek as $day) {
                        $calendar .= "<th class='header'>$day</th>";
                    }
                    $calendar .= "</tr>";
                    $calendar .= "</thead>";
                    $calendar .= "<tbody>";
                    $calendar .= "<tr>";
                    if ($dayOfWeek > 1) {
                        for ($k = 1; $k < $dayOfWeek; $k++) {
                            $calendar .= "<td></td>";
                        }
                    }
                    $currentDay = 1;
                    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
                    while ($currentDay <= $numberDays) {
                        if ($dayOfWeek == 8) {
                            $dayOfWeek = 1;
                            $calendar .= "</tr><tr>";
                        }
                        $currentDayRel = str_pad(
                            $currentDay,
                            2,
                            "0",
                            STR_PAD_LEFT
                        );
                        $date = "$year-$month-$currentDayRel";
                        $today = $date == date("Y-m-d") ? "today" : "";
                        $dayname = date("l", strtotime($date));
                        if ($dayname == "Saturday" || $dayname == "Sunday") {
                            $calendar .= "<td><h4>$currentDay</h4><button class='btn btn-danger'>Holiday</button></td>";
                        } elseif ($date < date("Y-m-d")) {
                            $calendar .= "<td><h4>$currentDay</h4><button class='btn btn-danger'>N/A</button></td>";
                        } else {
                            $calendar .= "<td class='$today'><h4>$currentDay</h4><a onclick='book(\"$date\")' class='btn btn-success'>Book</a></td>";
                        }
                        $currentDay++;
                        $dayOfWeek++;
                    }
                    if ($dayOfWeek != 1) {
                        $remainingDays = 8 - $dayOfWeek;
                        for ($i = 0; $i < $remainingDays; $i++) {
                            $calendar .= "<td></td>";
                        }
                    }
                    $calendar .= "</tr>";
                    $calendar .= "</tbody>";
                    $calendar .= "</table>";
                    return $calendar;
                }
                $month = isset($_GET["month"]) ? $_GET["month"] : date("n");
                $year = isset($_GET["year"]) ? $_GET["year"] : date("Y");
                echo build_calendar($month, $year);
                function checkSlot($mysqli, $date)
                {
                    $stmt = $mysqli->prepare(
                        "SELECT * FROM booking WHERE date = ?"
                    );
                    $stmt->bind_param("s", $date);
                    $totalbookings = 0;
                    if ($stmt->execute()) {
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $totalbookings++;
                            }
                        }
                        $stmt->close();
                    }
                    return $totalbookings;
                }
                ?>
            </div>
        </div>
    </div>
<?php include "footer.php"; ?>
