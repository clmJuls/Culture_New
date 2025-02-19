$(document).ready(function() {
    let currentUserId = null;
    let allPosts = []; // Store all posts for filtering
    let activeFilters = {
        cultureElements: [],
        learningStyles: []
    };
    function initializeFilters() {
        // Culture Elements filters
        $('.menu-section a[href^="geography"], .menu-section a[href^="history"], .menu-section a[href^="demographics"], .menu-section a[href^="culture"]').click(function(e) {
            e.preventDefault();
            const element = $(this).text();
            
            if (activeFilters.cultureElements.includes(element)) {
                activeFilters.cultureElements = activeFilters.cultureElements.filter(item => item !== element);
                $(this).removeClass('active');
            } else {
                activeFilters.cultureElements.push(element);
                $(this).addClass('active');
            }
            
            filterAndDisplayPosts();
        });

        // Learning Styles filters
        $('.menu-section input[type="checkbox"]').change(function() {
            const style = $(this).parent().text().trim();
            
            if (this.checked) {
                activeFilters.learningStyles.push(style);
            } else {
                activeFilters.learningStyles = activeFilters.learningStyles.filter(item => item !== style);
            }
            
            filterAndDisplayPosts();
        });
    }

    function filterAndDisplayPosts() {
        let filteredPosts = allPosts;

        if (activeFilters.cultureElements.length > 0) {
            filteredPosts = filteredPosts.filter(post => {
                const postElements = post.culture_elements ? post.culture_elements.split(',').map(e => e.trim()) : [];
                return activeFilters.cultureElements.some(filter => postElements.includes(filter));
            });
        }

        if (activeFilters.learningStyles.length > 0) {
            filteredPosts = filteredPosts.filter(post => {
                const postStyles = post.learning_styles ? post.learning_styles.split(',').map(s => s.trim()) : [];
                return activeFilters.learningStyles.some(filter => postStyles.includes(filter));
            });
        }

        displayPosts(filteredPosts);
    }

    // Modified fetchPosts function to handle non-logged-in users
    function fetchPosts() {
        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { action: 'fetch_posts' },
            dataType: 'json',
            success: function(response) {
                currentUserId = response.current_user_id;
                allPosts = response.posts;
                filterAndDisplayPosts();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching posts:', error);
            }
        });
    }

    // Display posts function
    function displayPosts(posts) {
        const postDisplay = $('#post-display');
        postDisplay.empty();

        posts.forEach(function(post) {
            const postElement = createPostElement(post);
            postDisplay.append(postElement);
        });
    }
    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('like-btn')) {
fetchPosts();
        }
    });
    
    // Modified createPostElement function to handle non-logged-in state
    function createPostElement(post) {
        const comments = post.comments.map(comment => `
            <div class="comment">
                <img src="${comment.profile_picture}" alt="${comment.username}" class="comment-profile-pic">
                <div class="comment-content">
                    <strong>${comment.username}</strong>
                    <p>${comment.comment_text}</p>
                    ${comment.user_id == currentUserId ? `
                        <button class="delete-comment" data-comment-id="${comment.id}">Delete</button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    
        const cultureElements = post.culture_elements ? `
            <div class="culture-elements">
                <h4>Culture Elements:</h4>
                <ul class="elements-list">
                    ${post.culture_elements.split(',').map(element => `
                        <li>${element.trim()}</li>
                    `).join('')}
                </ul>
            </div>
        ` : '';
    
        const learningStyles = post.learning_styles ? `
            <div class="learning-styles">
                <h4>Learning Styles:</h4>
                <ul class="styles-list">
                    ${post.learning_styles.split(',').map(style => `
                        <li>${style.trim()}</li>
                    `).join('')}
                </ul>
            </div>
        ` : '';
    
        const interactionsHtml = currentUserId ? `
            <div class="post-interactions">
                <button class="like-btn ${post.user_liked > 0 ? 'liked' : ''}">
                    👍 ${post.like_count} Likes
                </button>
                <button class="comment-toggle">
                    💬 ${post.comment_count} Comments
                </button>
            </div>
        ` : `
            <div class="post-interactions">
                <button class="like-btn-disabled" onclick="requireLogin('like')">
                    👍 ${post.like_count} Likes
                </button>
                <button class="comment-toggle-disabled" onclick="requireLogin('comment')">
                    💬 ${post.comment_count} Comments
                </button>
            </div>
        `;
    
        const postHtml = `
            <div class="post" data-post-id="${post.id}">
                <div class="post-header">
                    <img src="${post.profile_picture}" alt="${post.username}" class="profile-pic">
                    <span>${post.username}</span>
                    ${post.user_id == currentUserId ? `
                        <button class="delete-post">
                            <img class="delete-post-icon" src="assets/icons/delete-svgrepo-com.svg" alt="Delete post" style="width: 20px; height: 20px;">
                        </button>
                    ` : ''}
                </div>
                <div class="post-content">
                    <h3>${post.title}</h3>
                    <p>${post.description}</p>
                    ${post.file_path ? `<img src="${post.file_path}" alt="${post.title}" style="width: 100%;">` : ''}
                </div>
                ${cultureElements}
                ${learningStyles}
                ${interactionsHtml}
                ${currentUserId ? `
                    <div class="comments-section" style="display:none;">
                        <div class="comments-list">
                            ${comments}
                        </div>
                        <div class="comment-input">
                            <input type="text" placeholder="Write a comment..." class="comment-text">
                            <button class="submit-comment">Send</button>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    
        return postHtml;
    }
    

    // Like functionality
    $(document).on('click', '.like-btn', function() {
        const postId = $(this).closest('.post').data('post-id');
        const likeBtn = $(this);
        
        // Prevent multiple clicks while processing
        if (likeBtn.hasClass('processing')) return;
        likeBtn.addClass('processing');
        
        // Safer number parsing with fallback
        let currentLikeCount = 0;
        try {
            currentLikeCount = parseInt(likeBtn.text().match(/\d+/) || [0])[0];
            if (isNaN(currentLikeCount)) currentLikeCount = 0;
        } catch (e) {
            currentLikeCount = 0;
        }
        
        const isCurrentlyLiked = likeBtn.hasClass('liked');
        
        // Format like count with safeguard
        const formatLikeCount = (count) => {
            count = Math.max(0, count);
            return `👍 ${count} ${count === 1 ? 'Like' : 'Likes'}`;
        };

        // Add animation class
        likeBtn.addClass('like-animation');
        setTimeout(() => {
            likeBtn.removeClass('like-animation');
        }, 200);

        // Make AJAX call
        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'toggle_like', 
                post_id: postId,
                final_state: !isCurrentlyLiked ? 1 : 0
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update UI only after successful response
                    if (isCurrentlyLiked) {
                        likeBtn.removeClass('liked');
                    } else {
                        likeBtn.addClass('liked');
                    }
                    
                    // Update like count if provided
                    if (response.like_count !== undefined) {
                        likeBtn.html(formatLikeCount(response.like_count));
                    } else {
                        // Fallback to calculated count if server doesn't provide it
                        likeBtn.html(formatLikeCount(isCurrentlyLiked ? currentLikeCount - 1 : currentLikeCount + 1));
                    }
                }
            },
            error: function() {
                // On error, no state change needed since we didn't update UI yet
                console.error('Failed to update like status');
            },
            complete: function() {
                likeBtn.removeClass('processing');
            }
        });
    });

    // Comment toggle
    $(document).on('click', '.comment-toggle', function() {
        $(this).closest('.post').find('.comments-section').toggle();
    });

    // Submit comment
    $(document).on('click', '.submit-comment', function() {
        const post = $(this).closest('.post');
        const postId = post.data('post-id');
        const commentText = post.find('.comment-text').val().trim();

        if (commentText) {
            $.ajax({
                url: 'posts_management.php',
                type: 'POST',
                data: { 
                    action: 'add_comment', 
                    post_id: postId, 
                    comment_text: commentText 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        post.find('.comment-text').val('');
                        fetchPosts();
                    }
                }
            });
        }
    });

    // Delete post
    $(document).on('click', '.delete-post', function() {
        if (!confirm('Are you sure you want to delete this post?')) return;

        const postId = $(this).closest('.post').data('post-id');

        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'delete_post', 
                post_id: postId 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    fetchPosts();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Delete comment
    $(document).on('click', '.delete-comment', function() {
        if (!confirm('Are you sure you want to delete this comment?')) return;

        const commentId = $(this).data('comment-id');

        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'delete_comment', 
                comment_id: commentId 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    fetchPosts();
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Add this function to handle login requirement
    window.requireLogin = function(action) {
        if (confirm(`Please log in to ${action} posts. Click OK to go to login page.`)) {
            window.location.href = 'auth/login.php';
        }
    };

    // Initial fetch of posts
    fetchPosts();
});