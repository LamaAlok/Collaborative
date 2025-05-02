document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.querySelector('input[type="password"]');
    const loginButton = document.querySelector('.login-button');
    const signupLink = document.querySelector('.signup-link');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
    });

    // Login form submission
    loginButton.addEventListener('click', function(e) {
        e.preventDefault();
        const username = document.querySelector('input[type="text"]').value;
        const password = passwordInput.value;

        if (!username || !password) {
            alert('Please fill in all fields');
            return;
        }

        // Here you would typically handle the login logic
        console.log('Login attempted with:', { username, password });
    });

    // Signup link handler
    signupLink.addEventListener('click', function() {
        // Handle signup navigation
        console.log('Navigate to signup page');
    });

    // Prevent form submission on enter
    document.querySelector('.login-form').addEventListener('submit', function(e) {
        e.preventDefault();
    });
});
