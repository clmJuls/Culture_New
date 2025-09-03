/**
 * Shared utility functions for the application
 */

/**
 * Format timestamp to human-readable format
 * @param {string} timestamp - The timestamp to format
 * @returns {string} - Formatted time string
 */
function formatTimestamp(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        const hours = Math.floor(diffTime / (1000 * 60 * 60));
        if (hours === 0) {
            const minutes = Math.floor(diffTime / (1000 * 60));
            return minutes === 0 ? 'just now' : `${minutes}m ago`;
        }
        return `${hours}h ago`;
    } else if (diffDays === 1) {
        return 'yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}

/**
 * Handle unauthorized actions by prompting user to login
 * @param {string} action - The action that requires authentication
 */
function handleUnauthorizedAction(action) {
    if (confirm(`Please log in to ${action}. Click OK to go to login page.`)) {
        window.location.href = 'auth/login.php';
    }
}

/**
 * Show loading indicator
 * @param {string} containerId - ID of container to show loading in
 */
function showLoading(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = '<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    }
}

/**
 * Show error message
 * @param {string} containerId - ID of container to show error in
 * @param {string} message - Error message to display
 */
function showError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `<div class="error-message"><i class="fas fa-exclamation-triangle"></i> ${message}</div>`;
    }
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} - Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
