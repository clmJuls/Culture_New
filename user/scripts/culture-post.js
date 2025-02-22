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

    // Delete post
    window.deletePost = function(postId) {
        if (confirm('Are you sure you want to delete this post?')) {
            $.ajax({
                url: 'handlers/delete_culture_post.php',
                type: 'POST',
                data: { post_id: postId },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            alert('Post deleted successfully!');
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
                    alert('Error deleting post: ' + error);
                }
            });
        }
    }

    // View post
    window.viewPost = function(postId) {
        window.location.href = `view_culture_post.php?id=${postId}`;
    }
});
