document.getElementById('register-button').addEventListener('click', function () {
    const fullName = document.getElementById('full-name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (!fullName || !email || !password || !confirmPassword) {
        alert("Please fill in all fields.");
        return;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return;
    }

    fetch('signup.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({
            fullName,
            email,
            password
        })
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            alert("Signup successful!");
            window.location.href = "welcome.html";
        } else {
            alert("Signup failed.");
        }
    });
});
