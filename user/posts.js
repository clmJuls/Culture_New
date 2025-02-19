$(document).ready(function() {
    let currentUserId = null;
    let allPosts = []; // Store all posts for filtering
    let activeFilters = {
        cultureElements: [],
        learningStyles: []
    };
    let currentPage = 1;
    const postsPerPage = 6;
    let isLoading = false;
    let hasMorePosts = true;

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
    function displayPosts(posts, clearExisting = false) {
        const postDisplay = document.getElementById('post-display');
        
        if (clearExisting) {
            postDisplay.innerHTML = '';
        }

        posts.forEach(post => {
            const postElement = document.createElement('div');
            postElement.className = 'post';
            postElement.setAttribute('data-post-id', post.id);
            
            let mediaHTML = '';
            if (post.file_path) {
                const fileExtension = post.file_path.split('.').pop().toLowerCase();
                const isVideo = ['mp4', 'webm', 'mov'].includes(fileExtension);
                
                if (isVideo) {
                    mediaHTML = `
                        <video class="post-media" controls>
                            <source src="${post.file_path}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>`;
                } else {
                    mediaHTML = `<img class="post-media" src="${post.file_path}" alt="Post media">`;
                }
            }

            postElement.innerHTML = `
                <div class="post-header">
                    <div style="display: flex; align-items: center;">
                        <img src="${post.profile_picture || 'assets/default-profile.png'}" class="profile-pic" alt="Profile Picture">
                        <span>${post.username}</span>
                    </div>
                    ${post.user_id === currentUserId || isAdmin ? 
                        `<button class="delete-post">
                            <i class="fas fa-trash"></i>
                        </button>` : ''
                    }
                </div>
                <div class="post-content">
                    <h3>${post.title}</h3>
                    <p>${post.description}</p>
                    ${mediaHTML}
                </div>
                <div class="post-interactions">
                    <button class="like-btn ${post.user_liked ? 'liked' : ''}">
                        <i class="fas fa-heart"></i> ${post.like_count} Likes
                    </button>
                </div>`;

            // Add click event listeners
            postElement.addEventListener('click', function(e) {
                // Don't open modal if clicking on interaction buttons
                if (e.target.closest('.delete-post') || 
                    e.target.closest('.like-btn')) {
                    return;
                }
                postModal.showExpandedPost(post);
            });

            // Add like button event listener
            const likeBtn = postElement.querySelector('.like-btn');
            if (likeBtn) {
                likeBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent modal from opening
                    toggleLike(post.id);
                });
            }

            // Add delete button event listener
            const deleteBtn = postElement.querySelector('.delete-post');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent modal from opening
                    deletePost(post.id, post.user_id);
                });
            }

            postDisplay.appendChild(postElement);
        });
    }

    // Add these functions for post interactions
    function toggleLike(postId) {
        if (!currentUserId) {
            handleUnauthorizedAction('like posts');
            return;
        }

        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'toggle_like',
                post_id: postId
            },
            success: function(response) {
                try {
                    const data = typeof response === 'object' ? response : JSON.parse(response);
                    if (data.status === 'success') {
                        updatePostLike(postId);
                    }
                } catch (e) {
                    console.error('Error processing like response:', e);
                }
            }
        });
    }

    function updatePostLike(postId) {
        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'get_post_likes',
                post_id: postId
            },
            success: function(response) {
                try {
                    const data = typeof response === 'object' ? response : JSON.parse(response);
                    if (data.status === 'success') {
                        const likeBtn = document.querySelector(`.post[data-post-id="${postId}"] .like-btn`);
                        if (likeBtn) {
                            likeBtn.classList.toggle('liked', data.user_liked);
                            likeBtn.innerHTML = `<i class="fas fa-heart"></i> ${data.like_count} Likes`;
                        }
                    }
                } catch (e) {z
                    console.error('Error updating like status:', e);
                }
            }
        });
    }

    function deletePost(postId, postUserId) {
        if (!currentUserId) {
            handleUnauthorizedAction('delete posts');
            return;
        }

        if (currentUserId == postUserId || isAdmin) {
            if (confirm('Are you sure you want to delete this post?')) {
                $.ajax({
                    url: 'posts_management.php',
                    type: 'POST',
                    data: { 
                        action: 'delete_post',
                        post_id: postId
                    },
                    success: function(response) {
                        try {
                            const data = typeof response === 'object' ? response : JSON.parse(response);
                            if (data.status === 'success') {
                                const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
                                if (postElement) {
                                    postElement.remove();
                                }
                            }
                        } catch (e) {
                            console.error('Error processing delete response:', e);
                        }
                    }
                });
            }
        } else {
            alert('You are not authorized to delete this post');
        }
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
                    <span class="post-title">${post.title}</span>
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

    // Infinite scroll handler
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            if (!isLoading && hasMorePosts) {
                loadMorePosts();
            }
        }
    });

    function loadPosts() {
        isLoading = true;
        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'fetch_posts',
                page: currentPage,
                per_page: postsPerPage
            },
            success: function(response) {
                try {
                    const data = typeof response === 'object' ? response : JSON.parse(response);
                    if (data.posts) {
                        if (data.posts.length < postsPerPage) {
                            hasMorePosts = false;
                        }
                        displayPosts(data.posts, currentPage === 1);
                    } else {
                        console.error('No posts data in response');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                isLoading = false;
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', error);
                isLoading = false;
            }
        });
    }

    function loadMorePosts() {
        currentPage++;
        loadPosts();
    }

    // Initialize
    postModal.init();
    loadPosts();
    initializeFilters();
});