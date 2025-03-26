document.getElementById('login-button').addEventListener('click', function () {
    const email = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;

    if (!email || !password) {
        alert("Please fill out both fields.");
        return;
    }

    fetch('login.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            email,
            password
        })
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            alert("Login successful!");
            window.location.href = "dashboard.html";
        } else {
            alert("Invalid email or password.");
        }
    });
});
