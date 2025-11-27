function validateForm() {
  let user = document.getElementById("username").value.trim();
  let pw = document.getElementById("password").value.trim();
  let msg = [];

  if (!user) {
    msg.push("Bạn chưa nhập tài khoản!");
  }
  if (!pw) {
    msg.push("Bạn chưa nhập mật khẩu!");
  }

  // return đối tượng
  return {
    user: user,
    pw: pw,
    valid: msg.length === 0,
    msg: msg,
  };
}

async function handleLogin() {
  const { user, pw, valid, msg } = validateForm();

  if (!valid) {
    return showToast("error", msg.join(" "));
  }

  const loginBtn = document.getElementById("loginBtn");
  loginBtn.disabled = true;

  try {
    const res = await fetch("../php/login.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ username: user, password: pw }),
      credentials: "include",
    });

    const data = await res.json();

    if (data.success) {
      showToast("success", "Đăng nhập thành công!");
      setTimeout(() => {
        window.location.href = "dashboard.html";
      }, 1500);
    } else {
      showToast("error", data.message || "Lỗi đăng nhập!");
    }
  } catch (error) {
    showToast("error", "Không thể kết nối máy chủ!");
  }

  loginBtn.disabled = false;
}

function showToast(type, msg) {
  const oldToast = document.querySelector(".simple-toast");
  if (oldToast) oldToast.remove();

  const t = document.createElement("div");
  t.className = `simple-toast ${type}`;
  t.textContent = msg;
  document.body.appendChild(t);

  setTimeout(() => {
    t.classList.add("show");
  }, 10);

  setTimeout(() => {
    t.classList.remove("show");
    setTimeout(() => {
      t.remove();
    }, 350);
  }, 2000);
}
