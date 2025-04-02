<?php
session_start();
if (!isset($_SESSION['x'])) {
    header("location:policelogin.php"); // Redirect to login if session doesn't exist
    exit;
}

// Database connection
$conn = mysqli_connect("localhost", "root", "Liam5641.", "crime_portal");
if (!$conn) {
    die("Could not connect: " . mysqli_connect_error());
}

$p_id = $_SESSION['pol']; // Get logged-in officer's ID

// Fetch all complaints assigned to the officer
$result = mysqli_query($conn, "
    SELECT 
        c.c_id, c.location, c.type_crime, c.d_o_c, c.description, 
        c.inc_status, c.pol_status, c.p_id, u.u_id AS email
    FROM complaint c
    JOIN user u ON c.a_no = u.a_no
    WHERE c.p_id = '$p_id'
    ORDER BY c.c_id DESC
");

if (!$result) {
    die("Error fetching data: " . mysqli_error($conn));
}

// Search for a specific complaint by ID
if (isset($_POST['s2'])) {
    $cid = mysqli_real_escape_string($conn, $_POST['cid']);
    $_SESSION['cid'] = $cid;

    // Redirect to complaint details if ID exists
    $query = "SELECT c_id FROM complaint WHERE c_id = '$cid'";
    $search_result = mysqli_query($conn, $query);

    if (mysqli_num_rows($search_result) > 0) {
        header("location:police_complainDetails.php");
        exit;
    } else {
        echo "<script>alert('Complaint not found.');</script>";
    }
}

// Update the progress of a specific complaint
if (isset($_POST['update_progress'])) {
    $c_id = mysqli_real_escape_string($conn, $_POST['c_id']);
    $inc_status = mysqli_real_escape_string($conn, $_POST['inc_status']);

    if (empty($c_id) || empty($inc_status)) {
        echo "<script>alert('Complaint ID and Incident Status are required.');</script>";
    } else {
        // Validate and fetch the complainant's information
        $email_query = "SELECT u.u_id FROM complaint c JOIN user u ON c.a_no = u.a_no WHERE c.c_id='$c_id'";
        $email_result = mysqli_query($conn, $email_query);

        if (mysqli_num_rows($email_result) > 0) {
            $email_row = mysqli_fetch_assoc($email_result);

            // Ensure 'u_id' key exists (which stores the email)
            if (isset($email_row['u_id'])) {
                $complainant_email = $email_row['u_id']; // u_id contains the email
            } else {
                echo "<script>alert('Email field is undefined.');</script>";
                $complainant_email = null;
            }
        } else {
            echo "<script>alert('No email found for the given Complaint ID.');</script>";
            $complainant_email = null;
        }

        // Update the incident status in the database
        $update_query = "UPDATE complaint SET inc_status = '$inc_status' WHERE c_id = '$c_id' AND p_id = '$p_id'";
        if (mysqli_query($conn, $update_query)) {
            echo "<script>alert('Progress updated successfully.');</script>";
            echo "<script>window.location.href='police_pending_complain.php';</script>";
        } else {
            echo "<script>alert('Failed to update progress: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Police Pending Complaints</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false"
                aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="home.php"><b>Crime Portal</b></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="official_login.php">Official Login</a></li>
                <li><a href="policelogin.php">Police Login</a></li>
                <li class="active"><a href="police_pending_complain.php">Police Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="police_pending_complain.php">Pending Complaints</a></li>
                <li><a href="police_complete.php">Completed Complaints</a></li>
                <li><a href="p_logout.php">Logout &nbsp;<i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Search Complaint by ID -->
<form style="margin-top: 7%; margin-left: 40%;" method="post">
    <input type="text" name="cid" style="width: 250px; height: 30px; background-color:white; color:grey; margin-top:5px;"
        placeholder="&nbsp; Complaint ID" required>
    <div>
        <input class="btn btn-primary" type="submit" value="Search" name="s2" style="margin-top: 10px; margin-left: 11%;">
    </div>
</form>

<!-- Complaints Table -->
<?php if (mysqli_num_rows($result) > 0) { ?>
<div style="padding:50px;">
    <table class="table table-bordered">
        <thead class="thead-dark" style="background-color: black; color: white;">
            <tr>
                <th>Complaint ID</th>
                <th>Email Address</th>
                <th>Location</th>
                <th>Type of Crime</th>
                <th>Date of Crime</th>
                <th>Description</th>
                <th>Incident Status</th>
                <th>Police Status</th>
                <th>Police ID</th>
                <th>Update Progress</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['c_id']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['location']; ?></td>
                <td><?php echo $row['type_crime']; ?></td>
                <td><?php echo $row['d_o_c']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['inc_status']; ?></td>
                <td><?php echo $row['pol_status']; ?></td>
                <td><?php echo $row['p_id']; ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="c_id" value="<?php echo $row['c_id']; ?>">
                        <input type="text" name="inc_status" placeholder="Update Progress" required>
                        <button type="submit" name="update_progress" class="btn btn-primary">Update</button>
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php } else { ?>
<p style="margin: 50px;">No pending complaints found.</p>
<?php } ?>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>