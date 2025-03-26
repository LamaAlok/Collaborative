document.addEventListener('DOMContentLoaded', function () {
    const registerButton = document.getElementById('register-button');

    registerButton.addEventListener('click', function () {
        const fullName = document.getElementById('full-name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        // Input validation
        if (!fullName || !email || !password || !confirmPassword) {
            alert("Please fill in all fields.");
            return;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return;
        }

        // Simulate successful signup and redirect
        alert("Sign up successful! Redirecting to the welcome page...");
        window.location.href = "welcome.html";
    });
});