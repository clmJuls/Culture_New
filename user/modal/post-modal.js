// Modal handling code
const postModal = {
  element: null,
  contentElement: null,

  init: function() {
      this.element = document.getElementById('postViewModal');
      this.contentElement = document.querySelector('.post-modal-content');
      this.setupEventListeners();
      commentDeleteModal.init(); // Initialize the comment delete modal
  },

  setupEventListeners: function() {
      const closeBtn = document.querySelector('.close-post-modal');
      
      // Close on X button click
      if (closeBtn) {
          closeBtn.onclick = (e) => {
              e.stopPropagation();
              this.close();
          };
      }

      // Close on outside click
      if (this.element) {
          this.element.onclick = (e) => {
              if (e.target === this.element) {
                  this.close();
              }
          };
      }

      // Prevent modal content clicks from closing the modal
      if (this.contentElement) {
          this.contentElement.onclick = (e) => {
              e.stopPropagation();
          };
      }
  },

  open: function() {
      this.element.classList.add('active');
      document.body.style.overflow = 'hidden';
  },

  close: function() {
      this.element.classList.remove('active');
      document.body.style.overflow = '';
  },

  showExpandedPost: function(post) {
      const content = document.getElementById('expanded-post-content');
      let mediaHTML = '';
      
      if (post.file_path) {
          const fileExtension = post.file_path.split('.').pop().toLowerCase();
          const isVideo = ['mp4', 'webm', 'mov'].includes(fileExtension);
          
          mediaHTML = `<div class="post-media-container">`;
          if (isVideo) {
              mediaHTML += `
                  <video class="post-media" controls>
                      <source src="${post.file_path}" type="video/mp4">
                      Your browser does not support the video tag.
                  </video>`;
          } else {
              mediaHTML += `<img class="post-media" src="${post.file_path}" alt="Post media">`;
          }
          mediaHTML += `</div>`;
      }

      content.innerHTML = `
          <div class="post-header">
              <div style="display: flex; align-items: center; gap: 12px;">
                  <img src="${post.profile_picture}" class="profile-pic" alt="Profile Picture">
                  <span class="username">${post.username}</span>
              </div>
          </div>
          <div class="post-content">
              <span class="post-title">${post.title}</span>
              <p class="post-description">${post.description}</p>
              ${mediaHTML}
              ${post.culture_elements ? `
                  <div class="culture-elements">
                      <h4>Culture Elements:</h4>
                      <ul class="elements-list">
                          ${post.culture_elements.split(',').map(element => `
                              <li>${element.trim()}</li>
                          `).join('')}
                      </ul>
                  </div>
              ` : ''}
              ${post.learning_styles ? `
                  <div class="learning-styles">
                      <h4>Learning Styles:</h4>
                      <ul class="styles-list">
                          ${post.learning_styles.split(',').map(style => `
                              <li>${style.trim()}</li>
                          `).join('')}
                      </ul>
                  </div>
              ` : ''}
          </div>
          <div class="modal-comments-section">
              <div class="comments-header">
                  <span class="comments-header-text">Comments</span>
                  <button class="view-all-comments" data-post-id="${post.id}" style="display: none;">
                      View all comments
                  </button>
              </div>
              <div class="comments-list" id="modal-comments-${post.id}">
                  <!-- Comments will be loaded here -->
              </div>
              ${currentUserId ? `
                  <div class="modal-comment-input">
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

      // Add event listeners and load comments
      this.setupCommentHandlers(post.id);
      this.updateModalComments(post.id);
      this.open();
  },

  setupCommentHandlers: function(postId) {
      const commentButton = document.querySelector('.modal-submit-comment');
      if (commentButton) {
          commentButton.addEventListener('click', () => this.submitModalComment(postId));
      }
  },

  submitModalComment: function(postId) {
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
          success: (response) => {
              try {
                  const data = typeof response === 'object' ? response : JSON.parse(response);
                  if (data.status === 'success') {
                      // Clear input
                      commentInput.value = '';
                      
                      // Refresh comments to show the new one
                      this.updateModalComments(postId, true);
                  }
              } catch (e) {
                  console.error('Error parsing comment response:', e);
              }
          }
      });
  },

  updateModalComments: function(postId, showAll = false) {
      $.ajax({
          url: 'posts_management.php',
          type: 'POST',
          data: {
              action: 'get_comments',
              post_id: postId
          },
          success: (response) => {
              try {
                  const data = typeof response === 'object' ? response : JSON.parse(response);
                  if (data.status === 'success') {
                      const modalCommentsSection = document.querySelector(`#modal-comments-${postId}`);
                      const viewAllButton = document.querySelector('.view-all-comments');
                      
                      if (modalCommentsSection) {
                          // Sort comments by ID in descending order (latest first)
                          const comments = data.comments.sort((a, b) => b.id - a.id);
                          const totalComments = comments.length;
                          const commentsToShow = showAll ? comments : comments.slice(0, 3);

                          modalCommentsSection.innerHTML = this.renderComments(commentsToShow);

                          // Show/hide "View all comments" button
                          if (viewAllButton) {
                              if (totalComments > 3 && !showAll) {
                                  viewAllButton.style.display = 'block';
                                  viewAllButton.textContent = `View all ${totalComments} comments`;
                                  viewAllButton.onclick = () => this.updateModalComments(postId, true);
                              } else if (showAll && totalComments > 3) {
                                  viewAllButton.style.display = 'block';
                                  viewAllButton.textContent = 'Show less';
                                  viewAllButton.onclick = () => this.updateModalComments(postId, false);
                              } else {
                                  viewAllButton.style.display = 'none';
                              }
                          }
                      }
                  }
              } catch (e) {
                  console.error('Error parsing comment response:', e);
              }
          }
      });
  },

  renderComments: function(comments) {
      if (!comments || comments.length === 0) {
          return '<p class="no-comments">No comments yet</p>';
      }

      return comments.map(comment => `
          <div class="comment" data-comment-id="${comment.id}">
              <img src="${comment.profile_picture || 'assets/default-profile.png'}" class="comment-profile-pic" alt="${comment.username}">
              <div class="comment-content">
                  <div class="comment-header">
                      <strong class="comment-username">${comment.username}</strong>
                      <span class="comment-time">${this.formatTimestamp(comment.created_at)}</span>
                  </div>
                  <p class="comment-text">${comment.comment_text}</p>
              </div>
              ${(comment.user_id == currentUserId || isAdmin) ? `
                  <button class="delete-comment" onclick="postModal.openDeleteModal(${comment.id}, '${comment.comment_text}', ${comment.post_id})">
                      <i class="fas fa-trash"></i>
                  </button>
              ` : ''}
          </div>
      `).join('');
  },

  deleteComment: function(commentId, postId) {
      if (confirm('Are you sure you want to delete this comment?')) {
          $.ajax({
              url: 'posts_management.php',
              type: 'POST',
              data: {
                  action: 'delete_comment',
                  comment_id: commentId
              },
              success: (response) => {
                  try {
                      const data = typeof response === 'object' ? response : JSON.parse(response);
                      if (data.status === 'success') {
                          // Refresh comments in the modal
                          this.updateModalComments(postId);
                          
                          // Also refresh comments in the main post view if visible
                          const mainPostComments = document.querySelector(`#comments-${postId}`);
                          if (mainPostComments) {
                              updateComments(postId);
                          }
                      } else {
                          alert(data.message || 'Failed to delete comment');
                      }
                  } catch (e) {
                      console.error('Error processing delete response:', e);
                      alert('An error occurred while deleting the comment');
                  }
              },
              error: function() {
                  alert('An error occurred while deleting the comment');
              }
          });
      }
  },

  formatTimestamp: function(timestamp) {
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
  },

  openDeleteModal: function(commentId, commentText, postId) {
      commentDeleteModal.open({
          id: commentId,
          text: commentText,
          postId: postId
      });
  }
};

// Add this to your postModal object
const commentDeleteModal = {
    modal: null,
    commentToDelete: null,

    init: function() {
        this.modal = document.getElementById('commentDeleteModal');
        this.setupEventListeners();
    },

    setupEventListeners: function() {
        // Close button
        const closeBtn = this.modal.querySelector('.close-comment-modal');
        closeBtn.onclick = () => this.close();

        // Cancel button
        const cancelBtn = this.modal.querySelector('.cancel-delete');
        cancelBtn.onclick = () => this.close();

        // Confirm delete button
        const confirmBtn = this.modal.querySelector('.confirm-delete');
        confirmBtn.onclick = () => this.confirmDelete();

        // Close on outside click
        this.modal.onclick = (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        };
    },

    open: function(comment) {
        this.commentToDelete = comment;
        const preview = this.modal.querySelector('.comment-text-preview');
        preview.textContent = comment.text;
        this.modal.classList.add('active');
    },

    close: function() {
        this.modal.classList.remove('active');
        this.commentToDelete = null;
    },

    confirmDelete: function() {
        if (!this.commentToDelete) return;

        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: {
                action: 'delete_comment',
                comment_id: this.commentToDelete.id
            },
            success: (response) => {
                try {
                    const data = typeof response === 'object' ? response : JSON.parse(response);
                    if (data.status === 'success') {
                        // Refresh comments in the modal
                        postModal.updateModalComments(this.commentToDelete.postId);
                        
                        // Also refresh comments in the main post view if visible
                        const mainPostComments = document.querySelector(`#comments-${this.commentToDelete.postId}`);
                        if (mainPostComments) {
                            updateComments(this.commentToDelete.postId);
                        }
                    } else {
                        alert(data.message || 'Failed to delete comment');
                    }
                } catch (e) {
                    console.error('Error processing delete response:', e);
                    alert('An error occurred while deleting the comment');
                }
                this.close();
            },
            error: () => {
                alert('An error occurred while deleting the comment');
                this.close();
            }
        });
    }
};

// Export the modal object
window.postModal = postModal;