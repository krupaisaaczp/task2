<?php

// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Change to your MySQL password
$database = 'team_dashboard';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Get action
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding members
    if (isset($_POST['name']) && isset($_POST['role']) && isset($_POST['email'])) {
        $name = trim($_POST['name']);
        $role = trim($_POST['role']);
        $email = trim($_POST['email']);

        // Validate inputs
        if (empty($name) || empty($role) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid input data."]);
            exit;
        }

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO members (name, role, email) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $role, $email);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Member added successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    }

    // Handle removing members
    elseif (isset($_POST['removeMemberId'])) {
        $removeMemberId = (int)$_POST['removeMemberId'];

        // Validate ID
        if ($removeMemberId <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid member ID."]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM members WHERE id = ?");
        $stmt->bind_param('i', $removeMemberId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Member removed successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    }

    // Handle assigning tasks
    elseif (isset($_POST['taskName']) && isset($_POST['description']) && isset($_POST['deadline']) && isset($_POST['priority']) && isset($_POST['memberId'])) {
        $taskName = trim($_POST['taskName']);
        $description = trim($_POST['description']);
        $deadline = trim($_POST['deadline']);
        $priority = trim($_POST['priority']);
        $memberId = (int)$_POST['memberId'];

        // Validate inputs
        if (empty($taskName) || empty($description) || empty($deadline) || empty($priority) || $memberId <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid input data."]);
            exit;
        }

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO tasks (name, description, deadline, priority, member_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssi', $taskName, $description, $deadline, $priority, $memberId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Task assigned successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    }

    // Handle removing tasks
    elseif (isset($_POST['removeTaskId'])) {
        $removeTaskId = (int)$_POST['removeTaskId'];

        // Validate ID
        if ($removeTaskId <= 0) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Invalid task ID."]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param('i', $removeTaskId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Task removed successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
        }

        $stmt->close();
    }

    // Invalid POST request
    else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid request."]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch members
    if ($action === 'get_members') {
        $query = "SELECT * FROM members";
        $result = $conn->query($query);

        $members = [];
        while ($row = $result->fetch_assoc()) {
            $members[] = $row;
        }

        echo json_encode($members);
    }

    // Fetch tasks
    elseif ($action === 'get_tasks') {
        $query = "SELECT tasks.*, members.name AS member_name FROM tasks LEFT JOIN members ON tasks.member_id = members.id";
        $result = $conn->query($query);

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        echo json_encode($tasks);
    }

    // Invalid GET request
    else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid action."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
