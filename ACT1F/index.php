<?php
include 'database1f.php';

$search_results = [];
$sql = "SELECT * FROM applicants";
$result = $conn->query($sql);
if ($result) {
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $keyword = $conn->real_escape_string($_POST['keyword']);
    $sql = "SELECT * FROM applicants 
            WHERE first_name LIKE '%$keyword%' 
            OR last_name LIKE '%$keyword%' 
            OR email LIKE '%$keyword%' 
            OR job_title LIKE '%$keyword%' 
            OR application_date LIKE '%$keyword%'";
    $result = $conn->query($sql);
    if ($result) {
        $search_results = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application System</title>
</head>
<body>
    <h1>Welcome to the Job Application System</h1>
    <form method="POST" action="">
        <input type="text" name="keyword" placeholder="Search Applicants">
        <button type="submit" name="search">Search</button>
    </form>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Job Title</th>
            <th>Application Date</th>
        </tr>
        <?php foreach ($search_results as $row) : ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['first_name'] ?></td>
                <td><?= $row['last_name'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['job_title'] ?></td>
                <td><?= $row['application_date'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <a href="crud.php">Manage Applicants</a>
</body>
</html>




