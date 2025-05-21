<?php
<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "prynceindiv");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$fullname = $_POST['fullname'] ?? '';
$address = $_POST['address'] ?? '';
$gender = $_POST['gender'] ?? '';

if (!$username || !$password || !$fullname || !$address || !$gender) {
    echo json_encode(['success' => false, 'message' => 'All fields are required!']);
    exit;
}

// Check if username exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Username already exists!']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Generate a unique voter_id
$voter_id = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (username, password, fullname, address, gender, voter_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $username, $hashed_password, $fullname, $address, $gender, $voter_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'voterId' => $voter_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed!']);
}
$stmt->close();
$conn->close();
?>