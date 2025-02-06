async function handleLogin(event) {
    event.preventDefault();
    const password = document.getElementById('adminPassword').value;
    
    try {
        const response = await fetch('/blog/admin/api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ password: password })
        });
        
        if (!response.ok) throw new Error('Invalid password');
        
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('adminPanel').style.display = 'block';
        document.querySelector('.logout-btn').style.display = 'block';
        showNotification('Successfully logged in');
        loadPosts();
    } catch (error) {
        showNotification('Invalid password', 'error');
    }
}

function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.style.display = 'block';
    notification.classList.add('show');
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.style.display = 'none';
        }, 300);
    }, 3000);
}

function checkAuth() {
    if (localStorage.getItem('adminAuthenticated') === 'true') {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('adminPanel').style.display = 'block';
        document.querySelector('.logout-btn').style.display = 'block';
    }
}

function logout() {
    localStorage.removeItem('adminAuthenticated');
    showNotification('Logged out successfully');
    location.reload();
}

function showNewPostForm() {
    document.getElementById('postForm').style.display = 'block';
    // Initialize SimpleMDE
    if (!window.simplemde) {
        window.simplemde = new SimpleMDE({ 
            element: document.getElementById("postContent"),
            spellChecker: false,
            autosave: {
                enabled: true,
                unique_id: "blogPost"
            }
        });
    }
}

function cancelPost() {
    document.getElementById('postForm').style.display = 'none';
    document.getElementById('postTitle').value = '';
    document.getElementById('postTags').value = '';
    if (window.simplemde) {
        window.simplemde.value('');
    }
}

function refreshPosts() {
    // We'll implement this when we add the backend
    showNotification('Refreshing posts...');
}

async function handleNewPost(event) {
    event.preventDefault();
    const title = document.getElementById('postTitle').value;
    const tags = document.getElementById('postTags').value;
    const content = window.simplemde.value();
    
    try {
        const response = await fetch('/blog/admin/api/posts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ title, tags, content })
        });
        
        if (!response.ok) throw new Error('Failed to create post');
        
        showNotification('Post created successfully');
        cancelPost();
        loadPosts();
    } catch (error) {
        showNotification(error.message, 'error');
    }
}

async function loadPosts() {
    try {
        const response = await fetch('/blog/admin/api/posts.php');
        const posts = await response.json();
        
        const postsList = document.querySelector('.posts-list');
        postsList.innerHTML = posts.map(post => `
            <div class="post-item">
                <h3>${post.title}</h3>
                <p class="post-meta">
                    Posted on ${new Date(post.created_at).toLocaleDateString()}
                    <span class="post-tags">${post.tags}</span>
                </p>
                <div class="post-actions">
                    <button onclick="editPost(${post.id})">Edit</button>
                    <button onclick="deletePost(${post.id})">Delete</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        showNotification('Failed to load posts', 'error');
    }
}

document.addEventListener('DOMContentLoaded', checkAuth); 