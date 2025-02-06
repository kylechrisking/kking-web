let kingBuffer = '';
const kingCode = 'king';
let crownTimeout;

document.addEventListener('keydown', (e) => {
    // Clear the buffer if it's been more than 1 second since last keypress
    clearTimeout(crownTimeout);
    
    // Add the new character to the buffer
    kingBuffer += e.key.toLowerCase();
    
    // Keep only the last 4 characters
    if (kingBuffer.length > 4) {
        kingBuffer = kingBuffer.slice(-4);
    }
    
    // Check if the buffer matches 'king'
    if (kingBuffer.includes(kingCode)) {
        // Scroll to top smoothly
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
        
        // Wait for scroll to complete before showing crown
        setTimeout(() => {
            const crown = document.querySelector('.crown');
            crown.classList.remove('hidden');
            crown.classList.add('show');
            
            // Add sparkle effect
            createSparkles();
            
            // Reset after 3 seconds
            setTimeout(() => {
                crown.classList.remove('show');
                crown.classList.add('hidden');
            }, 3000);
        }, 500); // Wait 500ms for scroll to complete
        
        // Reset buffer
        kingBuffer = '';
    }
    
    // Set timeout to clear buffer after 1 second
    crownTimeout = setTimeout(() => {
        kingBuffer = '';
    }, 1000);
});

function createSparkles() {
    const colors = ['#FFD700', '#FFA500', '#FF8C00'];
    const heroSection = document.querySelector('.hero');
    
    for (let i = 0; i < 20; i++) {
        const sparkle = document.createElement('div');
        sparkle.className = 'sparkle';
        sparkle.style.left = Math.random() * 100 + '%';
        sparkle.style.top = Math.random() * 100 + '%';
        sparkle.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        
        heroSection.appendChild(sparkle);
        
        // Animate and remove sparkle
        setTimeout(() => {
            sparkle.remove();
        }, 1000);
    }
} 