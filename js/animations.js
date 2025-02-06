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
    const sectionTitles = document.querySelectorAll('.section-title');
    
    function reveal() {
        // Handle reveal elements
        revealElements.forEach(element => {
            const windowHeight = window.innerHeight;
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                element.classList.add('active');
            }
        });

        // Handle section titles
        sectionTitles.forEach(title => {
            const windowHeight = window.innerHeight;
            const elementTop = title.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                title.classList.add('visible');
            }
        });

        const characterPanel = document.querySelector('.character-panel');
        if (characterPanel) {
            const windowHeight = window.innerHeight;
            const elementTop = characterPanel.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < windowHeight - elementVisible) {
                initializeCharacterStats();
            }
        }
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

    function animateSkills() {
        const skillBars = document.querySelectorAll('.skill-progress');
        skillBars.forEach(bar => {
            const target = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = target;
            }, 300);
        });
    }

    // Call when skills section enters viewport

    function initializeCharacterStats() {
        const progressFills = document.querySelectorAll('.progress-fill');
        const statValues = document.querySelectorAll('.stat-value');
        
        // Animate all progress bars
        progressFills.forEach(fill => {
            const percentage = fill.getAttribute('data-percentage');
            setTimeout(() => {
                fill.style.width = `${percentage}%`;
            }, 200);
        });
        
        // Animate stat values - make it faster
        statValues.forEach(stat => {
            const value = stat.getAttribute('data-value');
            if (value) {
                let current = 0;
                const increment = value / 15; // Changed from 30 to 15 for faster animation
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= value) {
                        current = value;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current);
                }, 25); // Changed from 50 to 25 for faster updates
            }
        });
    }
}); 