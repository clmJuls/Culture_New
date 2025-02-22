<div id="createPostModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New History Post</h2>
            <button class="close-btn" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="createPostForm" method="POST" action="process_history_post.php" enctype="multipart/form-data">
            <div class="form-container">
                <div class="form-section">
                    <div class="input-group">
                        <label for="title">Title</label>
                        <input 
                            type="text" 
                            id="title" 
                            name="title" 
                            placeholder="Enter an engaging title for your post"
                            required
                        >
                    </div>

                    <div class="input-group">
                        <label for="category">Category</label>
                        <div class="select-wrapper">
                            <select id="category" name="category" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="ancient">Ancient Civilizations</option>
                                <option value="wars">World Wars</option>
                                <option value="renaissance">Renaissance</option>
                                <option value="movements">Revolutionary Movements</option>
                                <option value="colonialism">Colonialism</option>
                                <option value="evolution">Cultural Evolution</option>
                                <option value="figures">Historical Figures</option>
                                <option value="heritage">Cultural Heritage</option>
                            </select>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="description">Description</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            placeholder="Write a brief description of your post"
                            required
                        ></textarea>
                    </div>

                    <div class="input-group">
                        <label for="content">Content</label>
                        <textarea 
                            id="content" 
                            name="content" 
                            placeholder="Write your full post content here"
                            required
                        ></textarea>
                    </div>

                    <div class="input-group">
                        <label>Featured Image</label>
                        <div class="drop-zone">
                            <div class="drop-zone__prompt">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & drop your file here</p>
                                <span>or</span>
                                <button type="button" class="browse-btn">Browse Files</button>
                            </div>
                            <input type="file" name="image" class="drop-zone__input" accept="image/*" required>
                            <div class="drop-zone__thumb" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create Post
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
}

.modal-content {
    background: #fff;
    margin: 2% auto;
    width: 98%;
    max-width: 1400px;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
    max-height: 95vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 24px 32px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #111827;
}

.close-btn {
    background: none;
    border: none;
    padding: 8px;
    cursor: pointer;
    color: #6B7280;
    font-size: 20px;
    transition: all 0.2s;
}

.close-btn:hover {
    color: #111827;
}

.form-container {
    padding: 24px 32px;
}

.form-section {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.input-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.input-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.input-group input,
.input-group textarea,
.input-group select {
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    background: #fff;
}

.input-group input:focus,
.input-group textarea:focus,
.input-group select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    outline: none;
}

.input-group textarea {
    min-height: 150px;
    resize: vertical;
}

.select-wrapper {
    position: relative;
}

.select-wrapper select {
    width: 100%;
    appearance: none;
    padding-right: 40px;
}

.select-wrapper i {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #6B7280;
    pointer-events: none;
}

.drop-zone {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    transition: all 0.2s;
    background: #f9fafb;
}

.drop-zone:hover {
    border-color: #2563eb;
    background: #f3f4f6;
}

.drop-zone__prompt {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.drop-zone__prompt i {
    font-size: 32px;
    color: #2563eb;
}

.drop-zone__prompt p {
    margin: 0;
    font-size: 16px;
    color: #374151;
}

.drop-zone__prompt span {
    color: #6B7280;
}

.browse-btn {
    background: #fff;
    border: 1px solid #d1d5db;
    padding: 8px 16px;
    border-radius: 6px;
    color: #374151;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.browse-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.form-actions {
    padding: 24px 32px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    border-top: 1px solid #e5e7eb;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.btn-secondary {
    background: #fff;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-secondary:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.btn-primary {
    background: #2563eb;
    border: none;
    color: #fff;
}

.btn-primary:hover {
    background: #1d4ed8;
}

@media (max-width: 768px) {
    .modal-content {
        margin: 0;
        width: 100%;
        height: 100%;
        max-height: 100vh;
        border-radius: 0;
    }

    .form-container {
        padding: 20px;
    }

    .form-section {
        gap: 16px;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style> 