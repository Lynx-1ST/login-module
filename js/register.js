function validateRegisterForm() {
  let name = document.getElementById("fullname").value.trim();
  let mail = document.getElementById("email").value.trim();
  let user = document.getElementById("username").value.trim();
  let pass = document.getElementById("password").value.trim();
  let repass = document.getElementById("repass").value.trim();
  let errors = [];

  if (!name) errors.push("Nhập họ và tên");
  if (!mail) errors.push("Nhập email");
  else if (!/^[^@]+@[^@]+\.[^@]+$/.test(mail))
    errors.push("Email không hợp lệ");
  if (!user) errors.push("Nhập tên tài khoản");
  else if (user.length < 3) errors.push("Tài khoản ít nhất 3 ký tự");
  if (!pass) errors.push("Nhập mật khẩu");
  else if (pass.length < 6) errors.push("Mật khẩu ít nhất 6 ký tự");
  if (!repass) errors.push("Nhập lại mật khẩu");
  else if (pass && repass && pass !== repass)
    errors.push("Mật khẩu nhập lại không khớp");

  return { name, mail, user, pass, valid: errors.length === 0, errors };
}

async function handleRegister() {
  const { name, mail, user, pass, valid, errors } = validateRegisterForm();

  if (!valid) {
    return showToast("error", errors.join(" | "));
  }

  const registerBtn = document.getElementById("registerBtn");
  registerBtn.disabled = true;

  try {
    const res = await fetch("../php/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        fullname: name,
        email: mail,
        username: user,
        password: pass,
      }),
      credentials: "include",
    });

    const data = await res.json();

    if (data.success) {
      showToast("success", "Đăng ký thành công! Đăng nhập ngay...");
      setTimeout(() => {
        window.location.href = "login.html";
      }, 1800);
    } else {
      showToast("error", data.message || "Đăng ký thất bại!");
    }
  } catch (error) {
    showToast("error", "Không thể kết nối server!");
  }

  registerBtn.disabled = false;
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
    }, 400);
  }, 2000);
}
