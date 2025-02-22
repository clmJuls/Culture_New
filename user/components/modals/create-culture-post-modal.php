<div id="createPostModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Create Culture Post</h2>
        
        <form id="createPostForm" enctype="multipart/form-data">
            <div class="input-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" placeholder="Enter post title" required>
            </div>

            <div class="input-group">
                <label for="description">Short Description</label>
                <textarea id="description" name="description" 
                    placeholder="Enter a brief description of your post" required></textarea>
            </div>

            <div class="input-group">
                <label for="content">Full Content</label>
                <textarea id="content" name="content" 
                    placeholder="Enter the full content of your post" required></textarea>
            </div>

            <div class="input-group">
                <label for="category">Category</label>
                <div class="select-wrapper">
                    <select id="category" name="category" required>
                        <option value="" disabled selected>Select a category</option>
                        <option value="traditions">Cultural Traditions</option>
                        <option value="language">Language & Communication</option>
                        <option value="art">Art & Expression</option>
                        <option value="religion">Religious Practices</option>
                        <option value="heritage">Cultural Heritage</option>
                        <option value="cuisine">Cuisine & Food</option>
                        <option value="folklore">Folklore & Mythology</option>
                        <option value="diversity">Cultural Diversity</option>
                    </select>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <div class="input-group">
                <label for="image">Upload Image</label>
                <div class="file-upload">
                    <input type="file" id="image" name="image" accept="image/*" required>
                    <label for="image" class="file-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Choose a file</span>
                    </label>
                    <div id="file-name" class="file-name"></div>
                </div>
            </div>

            <?php if ($_SESSION['isAdmin'] == 1) { ?>
                <div class="input-group">
                    <label>Culture Elements</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="culture_elements[]" value="Traditions">
                            <span>Traditions</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="culture_elements[]" value="Language">
                            <span>Language</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="culture_elements[]" value="Art">
                            <span>Art</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="culture_elements[]" value="Religion">
                            <span>Religion</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="culture_elements[]" value="Heritage">
                            <span>Heritage</span>
                        </label>
                    </div>
                </div>
            <?php } ?>

            <div class="button-group">
                <button type="submit" class="submit-btn">Create Post</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow-y: auto;
}

.modal-content {
    background-color: #fff;
    margin: 50px auto;
    padding: 30px;
    border-radius: 8px;
    max-width: 600px;
    position: relative;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.close:hover {
    color: #333;
}

/* Form Styles */
.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

input[type="text"],
textarea,
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

textarea {
    min-height: 100px;
    resize: vertical;
}

/* Select Wrapper */
.select-wrapper {
    position: relative;
}

.select-wrapper select {
    appearance: none;
    padding-right: 30px;
}

.select-wrapper i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
    pointer-events: none;
}

/* File Upload */
.file-upload {
    position: relative;
}

.file-upload input[type="file"] {
    position: absolute;
    width: 0;
    height: 0;
    opacity: 0;
}

.file-label {
    display: inline-block;
    padding: 10px 20px;
    background-color: #f0f0f0;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.file-label:hover {
    background-color: #e0e0e0;
}

.file-label i {
    margin-right: 8px;
}

.file-name {
    margin-top: 8px;
    font-size: 14px;
    color: #666;
}

/* Checkbox Group */
.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 8px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 8px;
}

/* Button Group */
.button-group {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.submit-btn,
.cancel-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.3s;
}

.submit-btn {
    background-color: #022597;
    color: white;
    flex: 1;
}

.submit-btn:hover {
    background-color: #0052b1;
}

.cancel-btn {
    background-color: #f0f0f0;
    color: #333;
}

.cancel-btn:hover {
    background-color: #e0e0e0;
}

/* Responsive Design */
@media (max-width: 768px) {
    .modal-content {
        margin: 20px;
        padding: 20px;
    }

    .checkbox-group {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>

<script>
// File upload preview
document.getElementById('image').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    document.getElementById('file-name').textContent = fileName || '';
});

// Close modal function
function closeModal() {
    document.getElementById('createPostModal').style.display = 'none';
}
</script>
