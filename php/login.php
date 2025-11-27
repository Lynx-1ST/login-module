<?php
header('Content-Type: application/json; charset=utf-8');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? "");
$password = trim($data['password'] ?? "");

// Validate input
if (!$username || !$password) {
  echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin!']);
  exit;
}

// Đọc users từ file
$usersFile = __DIR__ . '/users.json';
if (!file_exists($usersFile)) {
  echo json_encode(['success' => false, 'message' => 'Hệ thống chưa sẵn sàng!']);
  exit;
}

$users = json_decode(file_get_contents($usersFile), true) ?: [];

// Tìm user
$user = null;
foreach ($users as $u) {
  if ($u['username'] === $username) {
    $user = $u;
    break;
  }
}

if (!$user) {
  echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác!']);
  exit;
}

// Kiểm tra password
if (!password_verify($password, $user['password'])) {
  echo json_encode(['success' => false, 'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác!']);
  exit;
}

// ============================================
// LOGIN SUCCESS - Tạo TOKEN & LƯU SESSION
// ============================================
$token = bin2hex(random_bytes(16));
$tokenExpiry = time() + (7 * 24 * 60 * 60);

// Lưu SESSION
$_SESSION['user_id'] = md5($user['username']);
$_SESSION['username'] = $user['username'];
$_SESSION['fullname'] = $user['fullname'];
$_SESSION['email'] = $user['email'];
$_SESSION['auth_token'] = $token;
$_SESSION['token_expiry'] = $tokenExpiry;
$_SESSION['login_time'] = date('d/m/Y H:i:s');

// Lưu COOKIE
setcookie('auth_token', $token, $tokenExpiry, '/', '', false, true);
setcookie('user_id', md5($user['username']), $tokenExpiry, '/', '', false, false);
setcookie('username', $user['username'], $tokenExpiry, '/', '', false, false);

echo json_encode([
  'success' => true,
  'message' => 'Đăng nhập thành công!',
  'token' => $token,
  'fullname' => $user['fullname'],
  'email' => $user['email'],
  'username' => $user['username']
]);
?>