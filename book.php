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
<?php
$msg = "";
$date = "";
$company = ""; 
$booking = [];
$mysqli = new mysqli("127.0.0.1", "root", "", "bookingcalendar");

if (isset($_GET["date"]) && isset($_GET["company"])) {
    $date = $_GET["date"];
    $company = $_GET["company"];

    $stmt = $mysqli->prepare(
        "SELECT timeslot FROM booking WHERE date = ? AND company_name = ?"
    );
    $stmt->bind_param("ss", $date, $company);
    //$booking = array();

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $booking[] = $row["timeslot"];
            }
            $stmt->close();
        }
    } else {
        echo "Error executing database query: " . $mysqli->error;
    }
}

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $timeslot = $_POST["timeslot"];
    $company = $_POST["company"];
    $stmt = $mysqli->prepare(
        "INSERT INTO booking(name,timeslot,email,date,company_name) VALUES(?,?,?,?,?)"
    );
    $stmt->bind_param("sssss", $name, $timeslot, $email, $date, $company);
    $stmt->execute();
    $msg = "<div class='alert alert-success'>Booking Successful</div>";
    $booking[] = $timeslot;
    $stmt->close();
}

$duration = 10;
$cleanup = 0;
$start = "09:00";
$end = "15:00";

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
        $slots[] = $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
    }
    return $slots;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book a Date</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="book.css">
</head>

<body>
    <div class="container">
        <h1 class="text-center">Book a Date: <?php
        echo date("F d,Y", strtotime($date)) . "<br>";
        echo "Company: " . htmlspecialchars($_GET["company"]) . "<br>";
        ?></h1>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <?php echo isset($msg) ? $msg : ""; ?>
            </div>
            <?php
            $timeslots = timeslots($duration, $cleanup, $start, $end);
            foreach ($timeslots as $ts) { ?>
                <div class="col-md-2">
                    <div class="form-group">
                        <?php if (in_array($ts, $booking)) { ?>
                            <button class="btn btn-danger"><?php echo $ts; ?></button>
                        <?php } else { ?>
                            <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                        <?php } ?>
                    </div>
                </div>
            <?php }
            ?>
        </div>
    </div>

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Booking</h4>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="timeslot">Timeslot</label>
                            <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input required type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input required type="email" name="email" class="form-control">
                            
                        </div>
                        
                       <div class="form-group">
                        <?php // Validate

$company = isset($_GET["company"]) ? htmlspecialchars($_GET["company"]) : ""; ?>
                        <input type="hidden" name="company" value="<?php echo $company; ?>">
                    </div>

                        
                        <div class="modal-footer">
                            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".book").click(function() {
                var timeslot = $(this).attr('data-timeslot');
                $("#slot").text(timeslot);
                $("#timeslot").val(timeslot);
                $("#myModal").modal("show");
            });
        });
    </script>
<?php include "footer.php"; ?>
