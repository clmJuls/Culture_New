<?php
function showLoginDialog() {
    echo "<script>
            if (confirm('Please log in to interact with posts. Click OK to go to login page.')) {
                window.location.href = 'auth/login.php';
            }
          </script>";
}
?>
