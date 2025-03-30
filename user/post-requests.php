<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <?php
    require 'db_conn.php';
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    include 'components/layout/admin/navbar.php'; 

    
    ?>

    <div class="post-requests-container">
        <div class="page-header">
            <h2>Post Requests</h2>
            <div class="filter-section">
                <select id="statusFilter">
                    <option value="all">All Requests</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <div class="search-box">
                    <input type="text" placeholder="Search requests...">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>

        <div class="requests-grid">
            <!-- Example of a post request card -->
            <div class="request-card">
                <div class="request-header">
                    <img src="assets/hero/default-avatar.png" alt="User Avatar" class="user-avatar">
                    <div class="user-info">
                        <h3>John Doe</h3>
                    </div>
                    <span class="status pending">Pending</span>
                </div>
                <div class="post-content">
                    <h4>Traditional Dance Festival</h4>
                    <p>A celebration of cultural heritage through traditional dance performances...</p>
                    <div class="post-media">
                        <img src="assets/posts/sample-post.jpg" alt="Post Image">
                    </div>
                    <div class="post-tags">
                        <span class="tag learning-style visual"><i class="fas fa-eye"></i> Visual</span>
                        <span class="tag learning-style kinesthetic"><i class="fas fa-running"></i> Kinesthetic</span>
                        <span class="tag learning-style auditory"><i class="fas fa-headphones"></i> Auditory</span>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="approve-btn">✓ Approve</button>
                    <button class="reject-btn">✕ Reject</button>
                    <p class="timestamp">2 hours ago</p>
                </div>
            </div>

            <!-- Add more request cards here -->
        </div>
    </div>

    <!-- Post Details Modal -->
    <div id="postDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-header">
                <h2>Post Details</h2>
            </div>
            <div class="modal-body">
                <!-- Post details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button class="approve-btn"><i class="fas fa-check"></i> Approve</button>
                <button class="reject-btn"><i class="fas fa-times"></i> Reject</button>
                <button class="close-btn">Close</button>
            </div>
        </div>
    </div>

    <style>
    /* General */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            color: #4A4947;
            line-height: 1.6;
            padding-top: 80px;
        }
        .rankings-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 20px;
        }

        .ranking-box {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }

        .ranking-title {
            color: #365486;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ranking-table th,
        .ranking-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .ranking-table th {
            background-color: #f8f9fa;
            color: #365486;
            font-weight: bold;
        }

        .ranking-table tr:hover {
            background-color: #f5f5f5;
        }

        .rank {
            font-weight: bold;
            color: #365486;
            width: 50px;
            text-align: center;
        }

        .usage-bar {
            background: #e9ecef;
            height: 20px;
            border-radius: 10px;
            overflow: hidden;
        }

        .usage-fill {
            background: #365486;
            height: 100%;
            transition: width 0.3s ease;
        }

        .percentage {
            color: #666;
            font-size: 0.9em;
        }
        .chart-container {
            width: 45%;
            margin: 20px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: inline-block;
            vertical-align: top;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 20px;
            gap: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            flex: 1;
            min-width: 200px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #365486;
        }
        .stat-label {
            color: #666;
            margin-top: 5px;
        }
    </style>

    <style>

        .post-requests-container {
            position: relative;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            width: 100%;
            max-width: 1200px;
            padding: 40px;
            margin: 120px auto 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-header h2 {
            color: #365486;
            font-size: 24px;
        }

        .filter-section {
            display: flex;
            gap: 15px;
        }

        #statusFilter {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 15px;
            padding-right: 35px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 200px;
        }

        .search-box i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .request-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }

        .request-header {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
            position: relative;
        }

        .request-header .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-info {
            flex-grow: 1;
        }

        .user-info h3 {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .timestamp {
            position: absolute;
            bottom: 15px;
            right: 15px;
            font-size: 12px;
            color: #666;
        }

        .status {
            margin-left: auto;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .post-content {
            padding: 15px;
        }

        .post-content h4 {
            margin: 0 0 10px 0;
            color: #365486;
        }

        .post-content p {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 14px;
        }

        .post-media {
            margin: 15px 0;
        }

        .post-media img {
            width: 100%;
            border-radius: 5px;
            object-fit: cover;
        }

        .post-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }

        .tag {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 12px;
            color: #495057;
        }

        .action-buttons {
            padding: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-start;
            border-top: 1px solid #eee;
        }

        .approve-btn, .reject-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .approve-btn {
            background: #28a745;
            color: white;
        }

        .reject-btn {
            background: #dc3545;
            color: white;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1001;
        }

        .modal-content {
            position: relative;
            background: white;
            margin: 50px auto;
            padding: 20px;
            width: 90%;
            max-width: 800px;
            border-radius: 10px;
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-footer {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Add hover effects to buttons */
        .action-buttons button:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        .post-tags .learning-style {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .learning-style.visual {
            background: #e3f2fd;
            color: #1976d2;
        }

        .learning-style.kinesthetic {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .learning-style.auditory {
            background: #e8f5e9;
            color: #388e3c;
        }

        .learning-style i {
            font-size: 12px;
        }
    </style>

    <script>
        // Toggle post details modal
        function toggleModal(show = true) {
            const modal = document.getElementById('postDetailsModal');
            modal.style.display = show ? 'block' : 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('postDetailsModal');
            if (event.target == modal) {
                toggleModal(false);
            }
        }

        // Close modal when clicking close button
        document.querySelector('.close-modal').onclick = function() {
            toggleModal(false);
        }

        document.querySelector('.close-btn').onclick = function() {
            toggleModal(false);
        }

        // View details button click handler
        document.querySelectorAll('.view-details-btn').forEach(button => {
            button.onclick = function() {
                toggleModal(true);
            }
        });

        // Filter change handler
        document.getElementById('statusFilter').onchange = function() {
            // Implement filtering logic here
            console.log('Filter changed to:', this.value);
        }
    </script>
</body>
</html>