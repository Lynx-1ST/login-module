<?php
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['fullname'] ?? "");
$email = trim($data['email'] ?? "");
$username = trim($data['username'] ?? "");
$password = trim($data['password'] ?? "");

// Validate
if (!$name || !$email || !$username || !$password) {
  echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
  exit;
}
if (strlen($username) < 3) {
  echo json_encode(['success' => false, 'message' => 'Tài khoản phải > 2 ký tự!']);
  exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['success' => false, 'message' => 'Email không hợp lệ!']);
  exit;
}
if (strlen($password) < 6) {
  echo json_encode(['success' => false, 'message' => 'Mật khẩu phải >= 6 ký tự!']);
  exit;
}

// Đọc users hiện tại
$usersFile = __DIR__ . '/users.json';
$users = [];
if (file_exists($usersFile)) {
  $users = json_decode(file_get_contents($usersFile), true) ?: [];
}

// Kiểm tra trùng
foreach ($users as $u) {
  if ($u['username'] === $username) {
    echo json_encode(['success' => false, 'message' => 'Tài khoản đã tồn tại!']);
    exit;
  }
  if ($u['email'] === $email) {
    echo json_encode(['success' => false, 'message' => 'Email đã tồn tại!']);
    exit;
  }
}

// Thêm user mới
$users[] = [
  'fullname' => $name,
  'email' => $email,
  'username' => $username,
  'password' => password_hash($password, PASSWORD_BCRYPT)
];

// Ghi lại file
if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
  echo json_encode(['success' => true, 'message' => 'Đăng ký thành công!']);
} else {
  echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu dữ liệu!']);
}
?>