$(document).ready(function() {
    // Modal handling
    const modal = document.getElementById('createPostModal');
    const btn = document.querySelector('.cta-button');
    const span = document.getElementsByClassName('close')[0];

    window.openModal = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Form submission
    $('#createPostForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Get selected culture elements
        const cultureElements = [];
        $('input[name="culture_elements[]"]:checked').each(function() {
            cultureElements.push($(this).val());
        });
        
        $.ajax({
            url: 'handlers/create_culture_post.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    // Ensure response is properly parsed
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (data.success) {
                        alert('Post created successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error occurred'));
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error: Invalid server response');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {xhr, status, error});
                alert('Error creating post: ' + error);
            }
        });
    });

    let postIdToDelete = null; // Variable to store the post ID to be deleted

    // Delete post
    window.deletePost = function(postId) {
        postIdToDelete = postId; // Store the post ID
        $('#deleteConfirmModal').css('display', 'block');
        $('body').css('overflow', 'hidden'); // Prevent background scrolling
    }

    // Show success modal
    function showSuccessModal(message) {
        $('#successMessage').text(message);
        $('#successModal').css('display', 'block');
        $('body').css('overflow', 'hidden');
    }

    // Close success modal
    window.closeSuccessModal = function() {
        $('#successModal').css('display', 'none');
        $('body').css('overflow', 'auto');
        location.reload(); // Reload the page after closing success modal
    }

    // Handle delete confirmation modal
    $('#confirmDelete').click(function() {
        if (postIdToDelete) {
            $.ajax({
                url: 'handlers/delete_culture_post.php',
                type: 'POST',
                data: { post_id: postIdToDelete },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            // Close the delete confirmation modal
                            $('#deleteConfirmModal').css('display', 'none');
                            
                            // Show success modal
                            showSuccessModal('Post deleted successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error occurred'));
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Error: Invalid server response');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', {xhr, status, error});
                    alert('Error deleting post: ' + error);
                }
            });
        }
    });

    // Close delete modal when clicking Cancel
    $('#cancelDelete').click(function() {
        $('#deleteConfirmModal').css('display', 'none');
        $('body').css('overflow', 'auto');
        postIdToDelete = null;
    });

    // Close delete modal when clicking the X button
    $('.delete-close').click(function() {
        $('#deleteConfirmModal').css('display', 'none');
        $('body').css('overflow', 'auto');
        postIdToDelete = null;
    });

    // Close delete modal when clicking outside
    $(window).click(function(event) {
        if (event.target.id === 'deleteConfirmModal') {
            $('#deleteConfirmModal').css('display', 'none');
            $('body').css('overflow', 'auto');
            postIdToDelete = null;
        }
    });

    // Close success modal when clicking outside
    $(window).click(function(event) {
        if (event.target.id === 'successModal') {
            closeSuccessModal();
        }
    });

    // View post in modal
    window.viewPost = function(postId) {
        // Fetch post details via AJAX
        $.ajax({
            url: 'handlers/get_culture_post.php',
            type: 'GET',
            data: { post_id: postId },
            success: function(response) {
                try {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    if (data.success) {
                        // Format the date
                        const date = new Date(data.post.created_at);
                        const formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        // Populate modal with post data
                        $('#modalImage').attr('src', data.post.image_url);
                        $('#modalTitle').text(data.post.title);
                        $('#modalDescription').text(data.post.description);
                        $('#modalCategory').text(data.post.category);
                        $('#modalDate').text(formattedDate);
                        $('#modalContent').text(data.post.content);
                        
                        // Show the modal
                        $('#postModal').css('display', 'block');
                        
                        // Prevent body scrolling when modal is open
                        $('body').css('overflow', 'hidden');
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error occurred'));
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error: Invalid server response');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {xhr, status, error});
                alert('Error loading post: ' + error);
            }
        });
    }

    // Close modal when clicking the close button
    $('.post-modal .close').click(function() {
        $('#postModal').css('display', 'none');
        $('body').css('overflow', 'auto'); // Restore body scrolling
    });

    // Close modal when clicking outside
    $(window).click(function(event) {
        if (event.target.id === 'postModal') {
            $('#postModal').css('display', 'none');
            $('body').css('overflow', 'auto'); // Restore body scrolling
        }
    });

    // Search functionality
    function searchPosts() {
        const searchTerm = $('#searchInput').val().trim();
        
        $.ajax({
            url: 'handlers/search_culture_posts.php',
            type: 'GET',
            data: { search: searchTerm },
            success: function(response) {
                try {
                    if (response.success) {
                        updatePostsGrid(response.posts);
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    alert('Error: Invalid server response');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', {xhr, status, error});
                alert('Error searching posts: ' + error);
            }
        });
    }

    // Function to update the posts grid with search results
    function updatePostsGrid(posts) {
        const grid = $('.journal-grid');
        grid.empty(); // Clear existing posts

        if (posts.length === 0) {
            grid.html('<div class="no-results">No posts found matching your search.</div>');
            return;
        }

        posts.forEach(post => {
            const deleteButton = isAdminUser ? 
                `<button class="delete-btn" onclick="deletePost(${post.id})">
                    <i class="fas fa-times"></i>
                </button>` : '';

            const postCard = `
                <div class="journal-card">
                    <div class="card-image-container">
                        <img src="${post.image_url}" alt="${post.title}">
                        ${deleteButton}
                    </div>
                    <div class="journal-card-content">
                        <h3>${post.title}</h3>
                        <p>${post.description}</p>
                        <a href="#" class="read-more" onclick="viewPost(${post.id})">Read More</a>
                    </div>
                </div>
            `;
            grid.append(postCard);
        });
    }

    // Add click event listener for search button
    $('#searchButton').click(function() {
        searchPosts();
    });

    // Add event listener for Enter key in search input
    $('#searchInput').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            searchPosts();
        }
    });
});
