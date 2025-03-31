// DOM Ready handler
$(document).ready(function() {
  // Initialize post modal
  postModal.init();
  
  // Initialize learning style filters
  initializeLearningStyleFilters();
  
  // Load initial posts
  loadPosts();
});

// Add these variables at the top of the file
let currentPage = 1;
const postsPerPage = 6;
let isLoading = false;
let hasMorePosts = true;
let currentPostToDelete = null;
let isSnapScrolling = false; // Add this new variable for snap scrolling
let selectedLearningStyles = new Set();

// Modified loadPosts function to handle empty results naturally
function loadPosts(append = false) {
  if (isLoading || (!append && !hasMorePosts)) return;
  
  isLoading = true;
  updateViewMoreButton('Loading...');
  
  $.ajax({
      url: 'posts_management.php',
      type: 'POST',
      data: { 
          action: 'fetch_posts',
          page: currentPage,
          per_page: postsPerPage,
          learning_styles: Array.from(selectedLearningStyles),
          include_premium_status: true  // Add this to request premium status
      },
      success: function(response) {
          try {
              const data = typeof response === 'object' ? response : JSON.parse(response);
              
              if (data.posts && data.posts.length > 0) {
                  displayPosts(data.posts, append);
                  currentPage++;
                  hasMorePosts = data.posts.length === postsPerPage;
              } else {
                  hasMorePosts = false;
                  if (!append) {
                      const postDisplay = document.getElementById('post-display');
                      if (postDisplay) {
                          postDisplay.innerHTML = ''; // Simply clear the display if no posts found
                      }
                  }
              }
              updateViewMoreButton();
          } catch (e) {
              console.error('Error parsing response:', e);
              updateViewMoreButton('Try Again');
          }
          isLoading = false;
      },
      error: function(xhr, status, error) {
          console.error('Ajax error:', error);
          isLoading = false;
          updateViewMoreButton('Try Again');
      }
  });
}

// Utility functions
function toggleDropdown() {
  var dropdownContent = document.querySelector(".dropdown-content");
  dropdownContent.classList.toggle("show");
}

function handleUnauthorizedAction(action) {
  if (!currentUserId) {
      if (confirm('Please log in to ' + action + '. Click OK to go to login page.')) {
          window.location.href = 'auth/login.php';
      }
      return false;
  }
  return true;
}

function handleLogout() {
  if (confirm('Are you sure you want to log out?')) {
      window.location.href = 'auth/logout.php';
  }
}

// Post display and interaction functions
function displayPosts(posts, append = false) {
  const postDisplay = document.getElementById('post-display');
  
  if (!append) {
      postDisplay.innerHTML = '';
  }

  // Define an array of premium gradients
  const premiumGradients = [
      'linear-gradient(135deg, #1e3c72, #2a5298)',  // Blue
      'linear-gradient(135deg, #ff416c, #ff4b2b)',  // Red-Orange
      'linear-gradient(135deg, #8e2de2, #4a00e0)',  // Purple
      'linear-gradient(135deg, #11998e, #38ef7d)',  // Green
      'linear-gradient(135deg, #f953c6, #b91d73)',  // Pink
      'linear-gradient(135deg, #f12711, #f5af19)',  // Orange
      'linear-gradient(135deg, #667eea, #764ba2)',  // Indigo
      'linear-gradient(135deg, #00b09b, #96c93d)'   // Teal
  ];

  posts.forEach(post => {
      const postElement = document.createElement('div');
      postElement.className = 'post';
      postElement.setAttribute('data-post-id', post.id);
      postElement.setAttribute('data-user-id', post.user_id);
      
      // Add premium class if user is premium
      if (post.is_premium) {
          postElement.classList.add('premium-post');
          
          // Apply a random gradient
          const randomGradient = premiumGradients[Math.floor(Math.random() * premiumGradients.length)];
          postElement.style.setProperty('--premium-gradient', randomGradient);
      }
      
      // Add snap scroll attribute
      postElement.style.scrollSnapAlign = 'start';
      
      // Create the delete button HTML only if user is authorized
      const deleteButtonHtml = (post.user_id == currentUserId || isAdmin) ? 
          `<button class="delete-post" data-post-id="${post.id}" data-user-id="${post.user_id}">
              <i class="fas fa-trash"></i>
          </button>` : '';

      // Add premium badge if user is premium
      const premiumBadgeHtml = post.is_premium ? 
          `<span class="premium-badge"><i class="fas fa-crown"></i> Premium</span>` : '';

      let mediaHTML = '';
      if (post.file_path) {
          const fileExtension = post.file_path.split('.').pop().toLowerCase();
          const isVideo = ['mp4', 'webm', 'mov'].includes(fileExtension);
          const isDocument = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'].includes(fileExtension);
          
          if (isVideo) {
              mediaHTML = `
                  <video class="post-media" controls>
                      <source src="${post.file_path}" type="video/mp4">
                      Your browser does not support the video tag.
                  </video>`;
          } else if (isDocument) {
              // Get appropriate icon class based on file type
              let iconClass = 'fa-file-alt'; // default document icon
              if (fileExtension === 'pdf') {
                  iconClass = 'fa-file-pdf';
              } else if (['doc', 'docx'].includes(fileExtension)) {
                  iconClass = 'fa-file-word';
              } else if (['xls', 'xlsx'].includes(fileExtension)) {
                  iconClass = 'fa-file-excel';
              }

              mediaHTML = `
                  <div class="document-container">
                      <a href="${post.file_path}" download class="document-download">
                          <i class="fas ${iconClass}"></i>
                          <span class="document-name">${post.file_path.split('/').pop()}</span>
                      </a>
                  </div>`;
          } else {
              mediaHTML = `<img class="post-media" src="${post.file_path}" alt="Post media">`;
          }
      }

      postElement.innerHTML = `
          <div class="post-header">
              <div style="display: flex; align-items: center;">
                  <img src="${post.profile_picture || 'assets/default-profile.png'}" class="profile-pic" alt="Profile Picture">
                  <span>${post.username}</span>
                  ${premiumBadgeHtml}
              </div>
              ${deleteButtonHtml}
          </div>
          <div class="post-content">
              <span class="post-title">${post.title}</span>
              <p class="post-description">${post.description}</p>
              <div class="media-container">
                  ${mediaHTML}
              </div>
          </div>
          <div class="post-interactions">
              <button class="like-btn ${post.user_liked ? 'liked' : ''}" onclick="toggleLike(${post.id})">
                  <i class="fas fa-heart"></i> ${post.like_count} Likes
              </button>
          </div>
          <div class="comments-section" id="comments-${post.id}" style="display: none;">
              ${renderComments(post.comments)}
          </div>`;

      // Add click event listener to the post content
      const postContent = postElement.querySelector('.post-content');
      if (postContent) {
          postContent.addEventListener('click', function(e) {
              e.stopPropagation();
              postModal.showExpandedPost({
                  ...post,
                  profile_picture: post.profile_picture || 'assets/default-profile.png',
                  comments: post.comments || []
              });
          });
      }

      // Make sure interaction buttons don't trigger modal
      const interactionButtons = postElement.querySelectorAll('.like-btn, .delete-post');
      interactionButtons.forEach(button => {
          button.addEventListener('click', (e) => {
              e.stopPropagation();
          });
      });

      postDisplay.appendChild(postElement);
  });

  // Add click handlers for delete buttons
  $('.delete-post').off('click').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const postId = $(this).data('post-id');
      const postUserId = $(this).data('user-id');
      deletePost(postId, postUserId);
  });
  
  // Add CSS to standardize post card heights
  normalizePostCardHeights();
}

// Add this new function to standardize post card heights
function normalizePostCardHeights() {
  // Set fixed heights for media containers
  const mediaContainers = document.querySelectorAll('.media-container');
  mediaContainers.forEach(container => {
    container.style.height = '180px';
    container.style.overflow = 'hidden';
    container.style.display = 'flex';
    container.style.alignItems = 'center';
    container.style.justifyContent = 'center';
  });
  
  // Make post media fit within container
  const postMedia = document.querySelectorAll('.post-media');
  postMedia.forEach(media => {
    if (media.tagName === 'IMG') {
      media.style.maxHeight = '100%';
      media.style.maxWidth = '100%';
      media.style.objectFit = 'contain';
    } else if (media.tagName === 'VIDEO') {
      media.style.maxHeight = '100%';
      media.style.maxWidth = '100%';
    }
  });
  
  // Style post titles with larger font size
  const titles = document.querySelectorAll('.post-title');
  titles.forEach(title => {
    title.style.fontSize = '16px';
    title.style.fontWeight = 'bold';
    title.style.display = 'block';
    title.style.marginBottom = '6px';
  });
  
  // Limit description height
  const descriptions = document.querySelectorAll('.post-description');
  descriptions.forEach(desc => {
    desc.style.maxHeight = '50px';
    desc.style.overflow = 'hidden';
    desc.style.textOverflow = 'ellipsis';
    desc.style.display = '-webkit-box';
    desc.style.webkitLineClamp = '2';
    desc.style.webkitBoxOrient = 'vertical';
  });
  
  // Make all post cards the same height
  const posts = document.querySelectorAll('.post');
  posts.forEach(post => {
    post.style.height = '380px';
    post.style.display = 'flex';
    post.style.flexDirection = 'column';
  });
  
  // Add premium styling
  const premiumPosts = document.querySelectorAll('.premium-post');
  premiumPosts.forEach(post => {
    // Add a gold border and subtle gradient background
    post.style.border = '2px solid #ffd700';
    post.style.background = 'linear-gradient(to bottom, #fffdf0, #ffffff)';
    post.style.boxShadow = '0 4px 8px rgba(255, 215, 0, 0.2)';
  });
  
  // Style premium badges
  const premiumBadges = document.querySelectorAll('.premium-badge');
  premiumBadges.forEach(badge => {
    badge.style.backgroundColor = '#ffd700';
    badge.style.color = '#333';
    badge.style.padding = '2px 6px';
    badge.style.borderRadius = '10px';
    badge.style.fontSize = '12px';
    badge.style.marginLeft = '8px';
    badge.style.fontWeight = 'bold';
  });
}

function renderComments(comments) {
    
    if (!comments || !comments.length) {
        return '<p class="no-comments">No comments yet</p>';
    }

    return comments.map(comment => `
        <div class="comment">
            <div class="comment-user-info">
                <img src="${comment.profile_picture || 'assets/default-profile.png'}" class="comment-profile-pic" alt="${comment.username}'s profile">
                <div class="comment-content">
                    <div class="comment-header">
                        <strong class="comment-username">${comment.username}</strong>
                        <span class="comment-time">${formatTimestamp(comment.created_at)}</span>
                    </div>
                    <p class="comment-text">${comment.comment_text}</p>
                </div>
            </div>
            ${comment.user_id == currentUserId || isAdmin ? 
                `<button class="delete-comment" onclick="deleteComment(${comment.id}, ${comment.post_id})">
                    <i class="fas fa-trash"></i>
                </button>` : ''
            }
        </div>
    `).join('');
}

// Add this helper function to format timestamps
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

function submitComment(postId) {
    if (!currentUserId) {
        handleUnauthorizedAction('comment on posts');
        return;
    }

    const commentInput = document.querySelector(`#comments-${postId} .comment-text`);
    const commentText = commentInput.value.trim();
    
    if (!commentText) return;

    $.ajax({
        url: 'posts_management.php',
        type: 'POST',
        data: {
            action: 'add_comment',
            post_id: postId,
            comment_text: commentText
        },
        success: function(response) {
            try {
                const data = typeof response === 'object' ? response : JSON.parse(response);
                if (data.status === 'success') {
                    // Clear input
                    commentInput.value = '';
                    // Refresh comments
                    updateComments(postId);
                }
            } catch (e) {
                console.error('Error processing comment response:', e);
            }
        }
    });
}

function updateComments(postId) {
    $.ajax({
        url: 'posts_management.php',
        type: 'POST',
        data: {
            action: 'get_comments',
            post_id: postId
        },
        success: function(response) {
            try {
                const data = typeof response === 'object' ? response : JSON.parse(response);
                if (data.status === 'success') {
                    const commentsSection = document.querySelector(`#comments-${postId}`);
                    commentsSection.innerHTML = renderComments(data.comments);
                }
            } catch (e) {
                console.error('Error updating comments:', e);
            }
        }
    });
}

// Post interaction handlers
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
          updatePostLike(postId);
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
          } catch (e) {
              console.error('Error updating like status:', e);
          }
      }
  });
}

function toggleComments(postId) {
  const commentsSection = document.getElementById(`comments-${postId}`);
  commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
}

function deletePost(postId, postUserId) {
    if (!currentUserId) {
        handleUnauthorizedAction('delete posts');
        return;
    }

    if (currentUserId == postUserId || isAdmin) {
        // Show delete confirmation modal
        const modal = document.getElementById('deleteConfirmModal');
        modal.style.display = 'block';

        // Handle close button
        const closeBtn = modal.querySelector('.close-modal');
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Handle cancel button
        const cancelBtn = modal.querySelector('.cancel-btn');
        cancelBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Handle confirm delete button
        const confirmBtn = modal.querySelector('.confirm-delete-btn');
        confirmBtn.onclick = function() {
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
                            // Hide modal first
                            modal.style.display = 'none';
                            
                            // Remove the post with animation
                            $(`.post[data-post-id="${postId}"]`).fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            alert('Error deleting post: ' + (data.message || 'Unknown error'));
                        }
                    } catch (e) {
                        console.error('Error processing delete response:', e);
                        alert('Error deleting post');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete request failed:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    alert('Error deleting post. Please try again.');
                }
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    } else {
        alert('You are not authorized to delete this post');
    }
}

function deleteComment(commentId, postId) {
    if (confirm('Are you sure you want to delete this comment?')) {
        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'delete_comment',
                comment_id: commentId
            },
            success: function(response) {
                try {
                    const data = typeof response === 'object' ? response : JSON.parse(response);
                    if (data.status === 'success') {
                        // Update both modal and main view comments
                        updateModalComments(postId);
                        updateComments(postId);
                    }
                } catch (e) {
                    console.error('Error deleting comment:', e);
                }
            }
        });
    }
}

// Add these new functions
function updateViewMoreButton(text = 'View More') {
    // This function can be removed, but we'll keep it empty for now
    // in case it's referenced elsewhere in code we can't see
}

// Add learning style filter handlers
function initializeLearningStyleFilters() {
    const filterContainer = document.getElementById('learning-styles-filter');
    if (!filterContainer) return;

    const checkboxes = filterContainer.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedLearningStyles.add(this.value);
            } else {
                selectedLearningStyles.delete(this.value);
            }
            // Reset pagination and reload posts with new filters
            currentPage = 1;
            hasMorePosts = true;
            loadPosts(false); // false to clear existing posts
        });
    });
}

// Add event listeners
$(document).ready(function() {
    initializeLearningStyleFilters();
    loadPosts();
    
    // Initialize snap scroll
    initSnapScroll();

    // Implement infinite scroll with snap scrolling
    $(window).scroll(function() {
        if(!isSnapScrolling && $(window).scrollTop() + $(window).height() > $(document).height() - 200) {
            if(!isLoading && hasMorePosts) {
                loadPosts(true);
            }
        }
    });
});

// Add this new function for snap scrolling
function initSnapScroll() {
    // Get the post display container
    const postDisplay = document.getElementById('post-display');
    
    // Apply CSS for snap scrolling container
    if (postDisplay) {
        postDisplay.style.scrollSnapType = 'y mandatory';
        postDisplay.style.overflowY = 'scroll';
        postDisplay.style.height = 'calc(100vh - 150px)'; // Adjust height as needed
    }
    
    // Add scroll event listener for snap scrolling
    window.addEventListener('scroll', handleSnapScroll, { passive: true });
}

function handleSnapScroll() {
    if (isSnapScrolling) return;
    
    const posts = document.querySelectorAll('.post');
    if (!posts.length) return;
    
    // Calculate row height (assuming posts are in a grid)
    const postHeight = posts[0].offsetHeight;
    const rowHeight = postHeight + 20; // Add margin/padding
    
    // Get current scroll position
    const scrollPosition = window.scrollY;
    
    // Calculate which row we should snap to
    const targetRow = Math.round(scrollPosition / rowHeight);
    const targetScrollPosition = targetRow * rowHeight;
    
    // Only snap if we're not too far from a snap point
    if (Math.abs(scrollPosition - targetScrollPosition) > 20) {
        isSnapScrolling = true;
        
        // Smooth scroll to the target position
        window.scrollTo({
            top: targetScrollPosition,
            behavior: 'smooth'
        });
        
        // Reset the flag after animation completes
        setTimeout(() => {
            isSnapScrolling = false;
        }, 500);
    }
}