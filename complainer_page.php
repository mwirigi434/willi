<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Registration</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="complainer_page.css" rel="stylesheet" type="text/css" media="all">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 80%;
            max-width: 500px;
        }
        .modal-content h2 {
            color: #28a745; /* Green color */
        }
        .modal-content p {
            margin: 15px 0;
        }
        .close-btn {
            background-color: #dc3545; /* Red color */
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .close-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body style="background-size: cover; background-image: url(home_bg1.jpeg); background-position: center;">
    <?php
    session_start();
    if (!isset($_SESSION['x'])) {
        header("location:userlogin.php");
    }

    $conn = new mysqli("localhost", "root", "Liam5641.", "crime_portal");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $u_id = $_SESSION['u_id'];
    $result = $conn->query("SELECT a_no FROM user WHERE u_id='$u_id'");
    $q2 = $result->fetch_assoc();
    $a_no = $q2['a_no'];

    $result1 = $conn->query("SELECT u_name FROM user WHERE u_id='$u_id'");
    $q2 = $result1->fetch_assoc();
    $u_name = $q2['u_name'];

    $popupMessage = ""; // Initialize the message variable

    if (isset($_POST['s'])) {
        $con = new mysqli('localhost', 'root', 'Liam5641.', 'crime_portal');
        if ($con->connect_error) {
            die('Connection failed: ' . $con->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $location = $_POST['location'];
            $type_crime = $_POST['type_crime'];
            $d_o_c = $_POST['d_o_c'];
            $description = $_POST['description'];
            $var = strtotime(date("Ymd")) - strtotime($d_o_c);

            if ($var >= 0) {
                $comp = "INSERT INTO complaint (a_no, location, type_crime, d_o_c, description) VALUES ('$a_no', '$location', '$type_crime', '$d_o_c', '$description')";
                $res = $con->query($comp);

                if (!$res) {
                    $popupMessage = "Complaint already filed";
                } else {
                    $popupMessage = "Complaint Registered Successfully!<br><br> The police will handle your case as follows:<br> - Assign an officer for investigation.<br> - Collect evidence and witness statements.<br> - Take appropriate action based on findings.<br> - Keep you updated on the progress.";
                }
            } else {
                $popupMessage = "Enter a valid date.";
            }
        }
    }
    ?>

    <?php if ($popupMessage): ?>
    <div class="modal" id="popupModal">
        <div class="modal-content">
            <h2>Notification</h2>
            <p><?php echo $popupMessage; ?></p>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>
    <?php endif; ?>

    <script>
        // Show the modal
        window.onload = function() {
            var modal = document.getElementById('popupModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        };

        // Close the modal
        function closeModal() {
            var modal = document.getElementById('popupModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function f1() {
            var sta1 = document.getElementById("desc").value;
            var x1 = sta1.trim();
            if (sta1 != "" && x1 == "") {
                document.getElementById("desc").value = "";
                document.getElementById("desc").focus();
                alert("Space Found");
            }
        }
    </script>

    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="home.php"><b>Home</b></a>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="userlogin.php">User Login</a></li>
                    <li class="active"><a href="complainer_page.php">User Home</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="complainer_page.php">Log New Complain</a></li>
                    <li><a href="complainer_complain_history.php">Complaint History</a></li>
                    <li><a href="logout.php">Logout &nbsp <i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="video" style="margin-top: 5%">
        <div class="center-container">
            <div class="bg-agile">
                <br><br>
                <div class="login-form">
                    <p><h2 style="color:white">Welcome <?php echo "$u_name"; ?></h2></p><br>
                    <p><h2>Log New Complain</h2></p><br>
                    <form action="#" method="post" style="color: gray">
                        ID Number <input type="text" name="aadhar_number" placeholder="Aadhar Number" required="" disabled value="<?php echo "$a_no"; ?>">
                        <div class="top-w3-agile" style="color: gray">
                            Location of Crime <select class="form-control" name="location">
                                <option>Nairobi</option>
                                <option>Mombasa</option>
                                <option>Kisumu</option>
                                <option>Nakuru</option>
                                <option>Eldoret</option>
                                <option>Thika</option>
                                <option>Malindi</option>
                                <option>Kitale</option>
                                <option>Kakamega</option>
                                <option>Nyeri</option>
                            </select>
                        </div>
                        <div class="top-w3-agile" style="color: gray">
                            Type of Crime <select class="form-control" name="type_crime">
                                <option>Theft</option>
                                <option>Robbery</option>
                                <option>Pick Pocket</option>
                                <option>Murder</option>
                                <option>Rape</option>
                                <option>Molestation</option>
                                <option>Kidnapping</option>
                                <option>Missing Person</option>
                            </select>
                        </div>
                        <div class="Top-w3-agile" style="color: gray">
                            Date Of Crime : &nbsp &nbsp <input style="background-color: #313131;color: white" type="date" name="d_o_c" required>
                        </div>
                        <br>
                        <div class="top-w3-agile" style="color: gray">
                            Description <textarea name="description" rows="20" cols="50" placeholder="Describe the incident in details with time" onfocusout="f1()" id="desc" required></textarea>
                        </div>
                        <input type="submit" value="Submit" name="s">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div style="position: relative; left: 0; bottom: 0; width: 100%; height: 30px; background-color: rgba(0,0,0,0.8); color: white; text-align: center;">
        <h4 style="color: white;">&copy <b>For help call 999</b></h4>
    </div>

    <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>