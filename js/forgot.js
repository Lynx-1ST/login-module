function handleForgot() {
  const username = document.getElementById("username").value.trim();
  const email = document.getElementById("email").value.trim();
  const newpw = document.getElementById("newpw").value.trim();

  if (!username || !email || !newpw) {
    return showToast("error", "Vui lòng nhập đủ thông tin!");
  }
  if (newpw.length < 6) {
    return showToast("error", "Mật khẩu mới ít nhất 6 ký tự!");
  }

  const forgotBtn = document.getElementById("forgotBtn");
  forgotBtn.disabled = true;

  fetch("../php/forgot.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ username, email, newpw }),
    credentials: "include",
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        showToast("success", "Đổi mật khẩu thành công! Đăng nhập lại.");
        setTimeout(() => (window.location.href = "login.html"), 1800);
      } else {
        showToast("error", data.message || "Thất bại, thử lại!");
      }
    })
    .catch(() => {
      showToast("error", "Không kết nối server!");
    })
    .finally(() => {
      forgotBtn.disabled = false;
    });
}

function showToast(type, msg) {
  const oldToast = document.querySelector(".simple-toast");
  if (oldToast) oldToast.remove();
  const t = document.createElement("div");
  t.className = `simple-toast ${type}`;
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.classList.add("show"), 10);
  setTimeout(() => {
    t.classList.remove("show");
    setTimeout(() => t.remove(), 400);
  }, 2000);
}
