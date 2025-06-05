// Session timeout after 15 minutes of inactivity
const INACTIVE_TIMEOUT = 15 * 60 * 1000; // 15 minutes in milliseconds
let inactivityTimer;

function resetInactivityTimer() {
    clearTimeout(inactivityTimer);
    inactivityTimer = setTimeout(logoutDueToInactivity, INACTIVE_TIMEOUT);
}

function logoutDueToInactivity() {
    alert('You have been logged out due to inactivity.');
    window.location.href = '/Culture_New/user/auth/logout.php';
}

// Reset timer on user activity
document.addEventListener('mousemove', resetInactivityTimer);
document.addEventListener('keypress', resetInactivityTimer);
document.addEventListener('click', resetInactivityTimer);
document.addEventListener('scroll', resetInactivityTimer);

// Start the timer when the page loads
window.addEventListener('load', resetInactivityTimer);
