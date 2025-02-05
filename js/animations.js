document.addEventListener('DOMContentLoaded', () => {
    // Handle loading screen
    const loadingScreen = document.querySelector('.loading-screen');
    
    window.addEventListener('load', () => {
        setTimeout(() => {
            loadingScreen.style.opacity = '0';
            setTimeout(() => {
                loadingScreen.style.display = 'none';
            }, 500);
        }, 1500);
    });

    // Scroll reveal
    const revealElements = document.querySelectorAll('.reveal');
    
    function reveal() {
        revealElements.forEach(element => {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', reveal);
    reveal(); // Initial check

    // Glitch text effect
    const glitchText = document.querySelector('.glitch-text');
    if (glitchText) {
        glitchText.setAttribute('data-text', glitchText.textContent);
    }

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Project card hover effects
    const projectCards = document.querySelectorAll('.project-card');
    projectCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-10px)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });

    // Hidden project reveal
    const hiddenProjects = document.querySelectorAll('.hidden-project');
    hiddenProjects.forEach(project => {
        project.addEventListener('click', () => {
            project.classList.add('revealed');
            // Add your easter egg reveal logic here
        });
    });

    // Smooth reveal animation for project cards
    const projectGrid = document.querySelector('.project-grid');
    if (projectGrid) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        projectGrid.querySelectorAll('.project-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            observer.observe(card);
        });
    }
}); 