<?php
header('Content-Type: application/json');

// Initialize error and input variables
$nameErr = $emailErr = $mobileErr = "";
$name = $email = $mobile = "";
$response = "";

// Function to sanitize input
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate name
    if (empty($_POST["name"])) {
        $nameErr = "Name is required";
    } else {
        $name = test_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
            $nameErr = "Only letters and white space allowed";
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    // Validate mobile
    if (empty($_POST["mobile"])) {
        $mobileErr = "Mobile number is required";
    } else {
        $mobile = test_input($_POST["mobile"]);
        if (!preg_match("/^[0-9]{10}$/", $mobile)) {
            $mobileErr = "Invalid mobile number (10 digits required)";
        }
    }

    // If no validation errors, insert data into the database
    if (empty($nameErr) && empty($emailErr) && empty($mobileErr)) {
        // Database connection details
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "formtest";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
            exit;
        }

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO form_datas (name, email, mobile) VALUES (?, ?, ?)");
        if (!$stmt) {
            echo json_encode(["success" => false, "message" => "Query preparation failed: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("sss", $name, $email, $mobile);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Registration successful!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
        $conn->close();
    } else {
        // Return validation errors
        echo json_encode([
            "success" => false,
            "nameErr" => $nameErr,
            "emailErr" => $emailErr,
            "mobileErr" => $mobileErr,
        ]);
    }
}
?>