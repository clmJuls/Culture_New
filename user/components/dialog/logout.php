<div id="logoutDialog" class="dialog-overlay">
    <div class="dialog-content">
        <h2>Logout Confirmation</h2>
        <p>Are you sure you want to log out?</p>
        <div class="dialog-buttons">
            <button onclick="closeLogoutDialog()" class="btn-secondary">Cancel</button>
            <button onclick="confirmLogout()" class="btn-primary">Logout</button>
        </div>
    </div>
</div>

<style>
.dialog-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1001;
    justify-content: center;
    align-items: center;
}

.dialog-content {
    background-color: white;
    padding: 24px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    text-align: center;
}

.dialog-content h2 {
    color: #365486;
    margin-bottom: 16px;
}

.dialog-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 24px;
}

.btn-primary, .btn-secondary {
    padding: 8px 24px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.btn-primary {
    background-color: #365486;
    color: white;
}

.btn-primary:hover {
    background-color: #1e3c72;
}

.btn-secondary {
    background-color: #e0e0e0;
    color: #333;
}

.btn-secondary:hover {
    background-color: #d0d0d0;
}
</style>

<script>
function showLogoutDialog() {
    document.getElementById('logoutDialog').style.display = 'flex';
}

function closeLogoutDialog() {
    document.getElementById('logoutDialog').style.display = 'none';
}

function confirmLogout() {
    window.location.href = 'auth/logout.php';
}

// Close dialog when clicking outside
document.getElementById('logoutDialog').addEventListener('click', function(event) {
    if (event.target === this) {
        closeLogoutDialog();
    }
});
</script>
