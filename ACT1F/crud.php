<?php
include 'database1f.php';

function handleRequest($action) {
    global $conn;

    $response = [
        "message" => "",
        "statusCode" => 400,
        "querySet" => null
    ];

    switch ($action) {
        case 'create':
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $job_title = $_POST['job_title'];
            $application_date = $_POST['application_date'];

            $sql = "INSERT INTO applicants (first_name, last_name, email, job_title, application_date) 
                    VALUES ('$first_name', '$last_name', '$email', '$job_title', '$application_date')";
            if ($conn->query($sql)) {
                $response["message"] = "Applicant created successfully.";
                $response["statusCode"] = 200;
            } else {
                $response["message"] = "Failed to create applicant: " . $conn->error;
            }
            break;

        case 'update':
            $id = $_POST['id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $job_title = $_POST['job_title'];
            $application_date = $_POST['application_date'];

            $sql = "UPDATE applicants 
                    SET first_name='$first_name', last_name='$last_name', email='$email', job_title='$job_title', application_date='$application_date' 
                    WHERE id=$id";
            if ($conn->query($sql)) {
                $response["message"] = "Applicant updated successfully.";
                $response["statusCode"] = 200;
            } else {
                $response["message"] = "Failed to update applicant: " . $conn->error;
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            $sql = "DELETE FROM applicants WHERE id=$id";
            if ($conn->query($sql)) {
                $response["message"] = "Applicant deleted successfully.";
                $response["statusCode"] = 200;
            } else {
                $response["message"] = "Failed to delete applicant: " . $conn->error;
            }
            break;

        default:
            $response["message"] = "Invalid action.";
    }

    return $response;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $response = handleRequest($_POST['action']);
    echo json_encode($response);
    exit;
}

$applicants = [];
$sql = "SELECT * FROM applicants";
$result = $conn->query($sql);
if ($result) {
    $applicants = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Applicants</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        form { margin-bottom: 20px; }
        button { margin-right: 10px; }
    </style>
</head>
<body>
    <h1>Applicant Management</h1>

    <form id="crudForm" method="POST">
        <input type="hidden" name="id" id="id">
        <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
        <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <input type="text" name="job_title" id="job_title" placeholder="Job Title" required>
        <input type="date" name="application_date" id="application_date" required>
        <button type="button" onclick="submitForm('create')">Create</button>
        <button type="button" onclick="submitForm('update')">Update</button>
        <button type="button" onclick="resetForm()">Reset</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Job Title</th>
                <th>Application Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($applicants as $applicant) : ?>
                <tr>
                    <td><?= $applicant['id'] ?></td>
                    <td><?= $applicant['first_name'] ?></td>
                    <td><?= $applicant['last_name'] ?></td>
                    <td><?= $applicant['email'] ?></td>
                    <td><?= $applicant['job_title'] ?></td>
                    <td><?= $applicant['application_date'] ?></td>
                    <td>
                        <button onclick="editApplicant(<?= htmlspecialchars(json_encode($applicant)) ?>)">Edit</button>
                        <button onclick="deleteApplicant(<?= $applicant['id'] ?>)">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" style="display:block; margin-top:20px;">Back to Homepage</a>

    <script>
        function submitForm(action) {
            const form = document.getElementById('crudForm');
            const formData = new FormData(form);
            formData.append('action', action);

            fetch('crud.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.statusCode === 200) location.reload();
                })
                .catch(error => console.error('Error:', error));
        }

        function editApplicant(applicant) {
            document.getElementById('id').value = applicant.id;
            document.getElementById('first_name').value = applicant.first_name;
            document.getElementById('last_name').value = applicant.last_name;
            document.getElementById('email').value = applicant.email;
            document.getElementById('job_title').value = applicant.job_title;
            document.getElementById('application_date').value = applicant.application_date;
        }

        function deleteApplicant(id) {
            if (confirm('Are you sure you want to delete this applicant?')) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('action', 'delete');

                fetch('crud.php', {
                    method: 'POST',
                    body: formData,
                })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        if (data.statusCode === 200) location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        function resetForm() {
            document.getElementById('crudForm').reset();
            document.getElementById('id').value = '';
        }
    </script>
</body>
</html>
