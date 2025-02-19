// DOM Ready handler
$(document).ready(function() {
  loadPosts();
  modal.init();
});

// Add these variables at the top of the file
let currentPage = 1;
const postsPerPage = 6;
let isLoading = false;
let hasMorePosts = true;
let currentPostToDelete = null;

const modal = {
    element: null,
    init: function() {
        this.element = document.getElementById('deleteModal');
        if (!this.element) return;

        // Close modal when clicking outside
        this.element.addEventListener('click', (e) => {
            if (e.target === this.element) {
                this.close();
            }
        });

        // Close button handler
        const closeBtn = this.element.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }

        // Cancel button handler
        const cancelBtn = this.element.querySelector('.cancel-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.close());
        }

        // Delete confirmation handler
        const deleteBtn = this.element.querySelector('.delete-confirm-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                if (currentPostToDelete) {
                    confirmDelete(currentPostToDelete);
                    this.close();
                }
            });
        }
    },
    open: function() {
        this.element.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    },
    close: function() {
        this.element.style.display = 'none';
        document.body.style.overflow = '';
        currentPostToDelete = null;
    }
};

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

      postDisplay.appendChild(postElement);

      // Add this to your existing displayPosts function, inside the posts.forEach loop
      postElement.addEventListener('click', function(e) {
          // Don't open modal if clicking on buttons
          if (e.target.closest('.delete-post') || 
              e.target.closest('.like-btn') || 
              e.target.closest('.comment-toggle')) {
              return;
          }
          
          showExpandedPost(post);
      });
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
      modal.open();
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
                  modal.close();
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

// Update the modal handling code
document.addEventListener('DOMContentLoaded', function() {
    const postModal = document.getElementById('postViewModal');
    const closePostBtn = document.querySelector('.close-post-modal');
    const modalContent = document.querySelector('.post-modal-content');

    // Close on X button click
    if (closePostBtn) {
        closePostBtn.onclick = function(e) {
            e.stopPropagation(); // Prevent event from bubbling to modal
            postModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Close on outside click
    if (postModal) {
        postModal.onclick = function(e) {
            if (e.target === postModal) {
                postModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    }

    // Prevent modal content clicks from closing the modal
    if (modalContent) {
        modalContent.onclick = function(e) {
            e.stopPropagation();
        }
    }
});

// Update the showExpandedPost function
function showExpandedPost(post) {
    const modal = document.getElementById('postViewModal');
    const content = document.getElementById('expanded-post-content');
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

    content.innerHTML = `
        <div class="post-content">
            <div class="post-header">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="${post.profile_picture || 'assets/default-profile.png'}" class="profile-pic" alt="Profile Picture">
                    <span class="username">${post.username}</span>
                </div>
            </div>
            <span class="post-title">${post.title}</span>
            <p class="post-description">${post.description}</p>
            ${mediaHTML}
        </div>
        <div class="modal-comments-container">
            <div class="comments-section" id="modal-comments-${post.id}">
                <!-- Comments will be rendered here -->
            </div>
            ${currentUserId ? `
                <div class="modal-comment-input">
                    <img src="${currentUserProfilePic || 'assets/default-profile.png'}" class="comment-profile-pic" alt="Your Profile Picture">
                    <div class="comment-input-wrapper">
                        <input type="text" class="modal-comment-text" placeholder="Write a comment...">
                        <button class="modal-submit-comment">Post</button>
                    </div>
                </div>
            ` : `
                <div class="modal-comment-login">
                    <p>Please <a href="auth/login.php">log in</a> to comment</p>
                </div>
            `}
        </div>`;

    // Add event listener for the comment button
    const commentButton = content.querySelector('.modal-submit-comment');
    if (commentButton) {
        commentButton.addEventListener('click', () => submitModalComment(post.id));
    }

    // After setting up the modal content, fetch and display comments
    updateModalComments(post.id);
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

// Update the submitModalComment function
function submitModalComment(postId) {
    const commentInput = document.querySelector('.modal-comment-text');
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
                    // Refresh comments in both modal and main post
                    updateModalComments(postId);
                    updateComments(postId);
                }
            } catch (e) {
                console.error('Error processing comment response:', e);
            }
        }
    });
}

// Update the updateModalComments function
function updateModalComments(postId) {
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
                    // Update comments in modal
                    const modalCommentsSection = document.querySelector(`#modal-comments-${postId}`);
                    if (modalCommentsSection) {
                        modalCommentsSection.innerHTML = renderComments(data.comments);
                    }
                    
                    // Also update comments in the main post view if it exists
                    const mainCommentsSection = document.querySelector(`#comments-${postId}`);
                    if (mainCommentsSection) {
                        mainCommentsSection.innerHTML = renderComments(data.comments);
                    }
                } else {
                    console.error('Error fetching comments:', data.message);
                }
            } catch (e) {
                console.error('Error parsing comment response:', e);
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
        }
    });
}