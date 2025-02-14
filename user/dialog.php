<?php
function showLoginDialog() {
    echo '
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Login Required</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <p>Please log in to interact with posts.</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn cancel-btn">Cancel</button>
                <button class="modal-btn login-btn">Log In</button>
            </div>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 0;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            padding: 15px 20px;
            background-color: #365486;
            color: white;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.25rem;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #DCF2F1;
        }

        .modal-body {
            padding: 20px;
            text-align: center;
        }

        .modal-footer {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .cancel-btn {
            background-color: #6c757d;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #5a6268;
        }

        .login-btn {
            background-color: #365486;
            color: white;
        }

        .login-btn:hover {
            background-color: #1e3c72;
        }
    </style>

    <script>
        function showModal() {
            const modal = document.getElementById("loginModal");
            modal.style.display = "block";
        }

        document.querySelector(".close").onclick = function() {
            document.getElementById("loginModal").style.display = "none";
        }

        document.querySelector(".cancel-btn").onclick = function() {
            document.getElementById("loginModal").style.display = "none";
        }

        document.querySelector(".login-btn").onclick = function() {
            window.location.href = "../user/auth/login.php";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("loginModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Show the modal immediately
        showModal();
    </script>';
}
?>
