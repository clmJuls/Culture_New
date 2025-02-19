// DOM Ready handler
$(document).ready(function() {
  // Initialize post modal
  postModal.init();
  
  // Load initial posts
  loadPosts();
});

// Add these variables at the top of the file
let currentPage = 1;
const postsPerPage = 6;
let isLoading = false;
let hasMorePosts = true;
let currentPostToDelete = null;

// Modified loadPosts function with debugging
function loadPosts(append = false) {
  if (isLoading || (!append && !hasMorePosts)) return;
  
  isLoading = true;
  updateViewMoreButton('Loading...');
  
  console.log('Fetching posts for page:', currentPage); // Debug log

  $.ajax({
      url: 'posts_management.php',
      type: 'POST',
      data: { 
          action: 'fetch_posts',
          page: currentPage,
          per_page: postsPerPage
      },
      success: function(response) {
          console.log('Raw response:', response); // Debug log
          try {
              const data = typeof response === 'object' ? response : JSON.parse(response);
              console.log('Parsed data:', data); // Debug log
              
              if (data.posts && data.posts.length > 0) {
                  displayPosts(data.posts, append);
                  currentPage++;
                  hasMorePosts = data.posts.length === postsPerPage;
              } else {
                  hasMorePosts = false;
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
          console.log('Status:', status);
          console.log('Response:', xhr.responseText);
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
  console.log('Displaying posts:', posts); // Debug log
  const postDisplay = document.getElementById('post-display');
  
  if (!append) {
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
              ${(post.user_id == currentUserId || isAdmin) ? 
                  `<button class="delete-post" onclick="deletePost(${post.id}, ${post.user_id})">
                      <i class="fas fa-trash"></i>
                  </button>` : ''
              }
          </div>
          <div class="post-content">
              <span class="post-title">${post.title}</span>
              <p>${post.description}</p>
              ${mediaHTML}
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
}

function renderComments(comments) {
    console.log('Rendering comments:', comments); // Debug log
    
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
  // Check if user is authorized to delete
  if (!currentUserId) {
      handleUnauthorizedAction('delete posts');
      return;
  }

  // Only allow if user is admin or post owner
  if (currentUserId == postUserId || isAdmin) {
      currentPostToDelete = postId;
      postModal.open();
  } else {
      alert('You are not authorized to delete this post');
  }
}

function confirmDelete(postId) {
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
                  // Remove the post element from the DOM
                  const postElement = document.querySelector(`.post[data-post-id="${postId}"]`);
                  if (postElement) {
                      postElement.remove();
                  }
                  postModal.close();
              } else {
                  console.error('Failed to delete post:', data.message);
                  alert(data.message || 'Failed to delete post');
              }
          } catch (e) {
              console.error('Error processing delete response:', e);
              alert('An error occurred while deleting the post');
          }
      },
      error: function(xhr, status, error) {
          console.error('Error deleting post:', error);
          alert('An error occurred while deleting the post');
      }
  });
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
    const button = document.getElementById('view-more-btn');
    if (!button) return;

    button.textContent = text;
    button.classList.toggle('loading', text === 'Loading...');
    button.style.display = hasMorePosts ? 'inline-block' : 'none';
}

// Add event listeners
$(document).ready(function() {
    loadPosts();

    // View More button click handler
    $('#view-more-btn').on('click', function() {
        loadPosts(true);
    });
});