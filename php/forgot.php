<?php
header('Content-Type: application/json; charset=utf-8');
$data = json_decode(file_get_contents('php://input'), true);
$username = trim($data['username'] ?? "");
$email = trim($data['email'] ?? "");
$newpw = trim($data['newpw'] ?? "");

if (!$username || !$email || !$newpw) {
  echo json_encode(['success'=>false, 'message'=>'Vui lòng nhập đủ thông tin!']);
  exit;
}
if (strlen($newpw) < 6) {
  echo json_encode(['success'=>false, 'message'=>'Mật khẩu mới quá ngắn!']);
  exit;
}

// Đọc users
$usersFile = __DIR__ . '/users.json';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
$found = false;

foreach ($users as &$u) {
  if ($u['username'] === $username && $u['email'] === $email) {
    $u['password'] = password_hash($newpw, PASSWORD_BCRYPT);
    $found = true;
    break;
  }
}

if (!$found) {
  echo json_encode(['success'=>false, 'message'=>'Tên đăng nhập hoặc email không đúng!']);
  exit;
}

file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['success'=>true, 'message'=>'Đã đổi mật khẩu!']);
?>
