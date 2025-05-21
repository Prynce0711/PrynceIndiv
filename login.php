<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "prynceindiv");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, password, fullname, address, gender, voter_id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($id, $hashed_password, $fullname, $address, $gender, $voter_id);

if ($stmt->fetch() && password_verify($password, $hashed_password)) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $id,
            'username' => $username,
            'fullname' => $fullname,
            'address' => $address,
            'gender' => $gender,
            'voter_id' => $voter_id
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid username or password!']);
}
$stmt->close();
$conn->close();
?>