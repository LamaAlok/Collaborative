// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Get the logout button
    const logoutButton = document.querySelector('.logout-button');

    // Add click event listener to the logout button
    logoutButton.addEventListener('click', function() {
        // Here you would typically handle the logout logic
        console.log('Logging out...');
        // Example: window.location.href = '/logout';
    });

    // Add hover effect to navigation links
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.opacity = '0.8';
        });
        link.addEventListener('mouseleave', function() {
            this.style.opacity = '1';
        });
    });
});
