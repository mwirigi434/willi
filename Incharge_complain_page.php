<!DOCTYPE html>
<html>
<head>
<?php
session_start();
if (!isset($_SESSION['x'])) {
    header("location:inchargelogin.php");
}

// Connect to the database
$conn = mysqli_connect("localhost", "root", "Liam5641.", "crime_portal");
if (!$conn) {
    die("Could not connect: " . mysqli_connect_error());
}

// Fetch all complaints
$query = "SELECT c_id, type_crime, d_o_c, location, inc_status, p_id FROM complaint ORDER BY c_id DESC";
$result = mysqli_query($conn, $query);

// Fetch all police officers
$police_query = "SELECT p_id, p_name FROM police";
$police_result = mysqli_query($conn, $police_query);

// Initialize variables for pop-up message
$popupMessage = '';
$popupType = ''; // 'success' or 'error'

// Assign Police Logic
if (isset($_POST['assign_police'])) {
    $cid = $_POST['case_id'];
    $police_id = $_POST['police_id'];

    $update_query = "UPDATE complaint SET p_id='$police_id', inc_status='Assigned' WHERE c_id='$cid'";
    if (mysqli_query($conn, $update_query)) {
        $popupMessage = "Police Officer Assigned Successfully!";
        $popupType = 'success';
    } else {
        $popupMessage = "Failed to Assign Police Officer. Please Try Again.";
        $popupType = 'error';
    }
}

// Unassign Police Logic
if (isset($_POST['unassign_police'])) {
    $cid = $_POST['case_id'];

    $update_query = "UPDATE complaint SET p_id=NULL, inc_status='Unassigned' WHERE c_id='$cid'";
    if (mysqli_query($conn, $update_query)) {
        $popupMessage = "Police Officer Unassigned Successfully!";
        $popupType = 'success';
    } else {
        $popupMessage = "Failed to Unassign Police Officer. Please Try Again.";
        $popupType = 'error';
    }
}
?>

<title>Incharge Homepage</title>
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

<style>
/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    max-width: 400px;
    width: 90%;
}

.modal-content.success h4 {
    color: #28a745; /* Green for success */
}

.modal-content.error h4 {
    color: #dc3545; /* Red for error */
}

.modal-content button {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

.modal-content button:hover {
    background: #0056b3;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>
</head>

<body style="background-color: #dfdfdf">
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
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
                <li><a href="inchargelogin.php">Incharge Login</a></li>
                <li class="active"><a href="Incharge_complain_page.php">Incharge Home</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="Incharge_complain_page.php">View Complaints</a></li>
                <li><a href="incharge_view_police.php">Police Officers</a></li>
                <li><a href="inc_logout.php">Logout &nbsp <i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Modal for Pop-Up Message -->
<?php if ($popupMessage): ?>
    <div class="modal" id="popupModal">
        <div class="modal-content <?php echo $popupType; ?>">
            <h4><?php echo $popupMessage; ?></h4>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>
<?php endif; ?>

<!-- Complaint Table -->
<div style="padding: 50px;">
    <h2>All Complaints</h2>
    <table class="table table-bordered">
        <thead class="thead-dark" style="background-color: black; color: white;">
        <tr>
            <th scope="col">Complaint Id</th>
            <th scope="col">Type of Crime</th>
            <th scope="col">Date of Crime</th>
            <th scope="col">Location</th>
            <th scope="col">Complaint Status</th>
            <th scope="col">Police ID</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>

        <?php while ($rows = mysqli_fetch_assoc($result)) { ?>
            <tbody style="background-color: white; color: black;">
            <tr>
                <td><?php echo $rows['c_id']; ?></td>
                <td><?php echo $rows['type_crime']; ?></td>
                <td><?php echo $rows['d_o_c']; ?></td>
                <td><?php echo $rows['location']; ?></td>
                <td><?php echo $rows['inc_status']; ?></td>
                <td><?php echo $rows['p_id'] ?? 'None'; ?></td>
                <td>
                    <!-- Assign Police Form -->
                    <?php if ($rows['inc_status'] == "Unassigned") { ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="case_id" value="<?php echo $rows['c_id']; ?>">
                            <select name="police_id" required>
                                <option value="" disabled selected>Select Police</option>
                                <?php
                                mysqli_data_seek($police_result, 0); // Reset the pointer for the result set
                                while ($police = mysqli_fetch_assoc($police_result)) { ?>
                                    <option value="<?php echo $police['p_id']; ?>">
                                        <?php echo $police['p_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <button class="btn btn-primary" type="submit" name="assign_police">Assign</button>
                        </form>
                    <?php } else { ?>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="case_id" value="<?php echo $rows['c_id']; ?>">
                            <button class="btn btn-danger" type="submit" name="unassign_police">Unassign</button>
                        </form>
                    <?php } ?>
                </td>
            </tr>
            </tbody>
        <?php } ?>

    </table>
</div>

<!-- Footer -->
<div style="position: fixed; left: 0; bottom: 0; width: 100%; height: 30px; background-color: rgba(0,0,0,0.8); color: white; text-align: center;">
    <h4 style="color: white;">&copy <b>For Help call 999 | All Rights Reserved</b></h4>
</div>

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
};
</script>
</body>
</html>