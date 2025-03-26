document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    // Toggle Password Visibility
    togglePassword.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });

    // Login Button Action
    const loginButton = document.getElementById('login-button');
    loginButton.addEventListener('click', function () {
        const username = document.getElementById('username').value.trim();
        const password = passwordInput.value;

        if (!username || !password) {
            alert("Please fill out both fields.");
            return;
        }

        // Simulate login validation
        const validUsername = "admin";
        const validPassword = "1234";

        if (username === validUsername && password === validPassword) {
            alert("Login successful!");
            window.location.href = "dashboard.html"; // Redirect to dashboard
        } else {
            alert("Invalid username or password.");
        }
    });
});