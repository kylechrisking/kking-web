document.addEventListener('DOMContentLoaded', () => {
    // Form handling
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = contactForm.querySelector('.submit-btn');
            submitBtn.classList.add('loading');

            const formData = {
                name: contactForm.querySelector('#name').value,
                email: contactForm.querySelector('#email').value,
                message: contactForm.querySelector('#message').value
            };

            try {
                const response = await fetch('http://localhost:4567/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to send message');
                }

                // Success handling
                submitBtn.classList.remove('loading');
                contactForm.reset();
                contactForm.classList.add('form-success');

                const successMessage = document.createElement('div');
                successMessage.className = 'success-message';
                successMessage.textContent = data.message;
                contactForm.appendChild(successMessage);

                setTimeout(() => {
                    successMessage.remove();
                    contactForm.classList.remove('form-success');
                }, 3000);

            } catch (error) {
                // Error handling
                submitBtn.classList.remove('loading');
                console.error('Error sending message:', error);

                const errorMessage = document.createElement('div');
                errorMessage.className = 'error-message';
                errorMessage.textContent = error.message;
                contactForm.appendChild(errorMessage);

                setTimeout(() => {
                    errorMessage.remove();
                }, 3000);
            }
        });
    }

    // Form input animations
    const formInputs = document.querySelectorAll('.form-group input, .form-group textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', () => {
            input.parentElement.classList.remove('focused');
            if (input.value) {
                input.parentElement.classList.add('has-value');
            } else {
                input.parentElement.classList.remove('has-value');
            }
        });
    });

    // Mobile Navigation
    const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileLinks = document.querySelectorAll('.mobile-link');

    if (mobileNavToggle) {
        mobileNavToggle.addEventListener('click', () => {
            mobileNavToggle.classList.toggle('active');
            mobileMenu.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        // Close mobile menu when clicking a link
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileNavToggle.classList.remove('active');
                mobileMenu.classList.remove('active');
                document.body.classList.remove('menu-open');
            });
        });
    }

    // Add scroll-based navigation highlight
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link, .mobile-link');

    window.addEventListener('scroll', () => {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (scrollY >= sectionTop - 300) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });

    // Add some easter eggs
    const logo = document.querySelector('.logo');
    let clickCount = 0;

    if (logo) {
        logo.addEventListener('click', () => {
            clickCount++;
            
            if (clickCount === 5) {
                // Add a fun animation to the entire page
                document.body.style.animation = 'spin 1s ease';
                setTimeout(() => {
                    document.body.style.animation = '';
                    clickCount = 0;
                }, 1000);
            }
        });
    }

    // Add a konami code easter egg
    let konamiCode = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'b', 'a'];
    let konamiIndex = 0;

    document.addEventListener('keydown', (e) => {
        if (e.key === konamiCode[konamiIndex]) {
            konamiIndex++;
            
            if (konamiIndex === konamiCode.length) {
                // Trigger a matrix-style animation
                document.body.classList.add('matrix-mode');
                setTimeout(() => {
                    document.body.classList.remove('matrix-mode');
                }, 5000);
                konamiIndex = 0;
            }
        } else {
            konamiIndex = 0;
        }
    });

    // 'K' key easter egg
    document.addEventListener('keydown', (e) => {
        if (e.key.toLowerCase() === 'k') {
            // Create floating elements
            for (let i = 0; i < 20; i++) {
                const element = document.createElement('div');
                element.className = 'floating-element';
                element.style.left = `${Math.random() * 100}vw`;
                element.style.animationDuration = `${Math.random() * 2 + 1}s`;
                element.innerHTML = ['⚡', '💻', '🚀', '✨'][Math.floor(Math.random() * 4)];
                document.body.appendChild(element);

                // Remove after animation
                setTimeout(() => {
                    element.remove();
                }, 3000);
            }
        }
    });

    // Add carousel functionality for projects
    function initProjectCarousel() {
        const projectGrid = document.querySelector('.project-grid');
        const projects = projectGrid.querySelectorAll('.project-card:not(.hidden-project)');
        let currentIndex = 0;

        function showProject(index) {
            projects.forEach(project => project.style.display = 'none');
            projects[index].style.display = 'block';
        }

        // Add navigation buttons
        const nav = document.createElement('div');
        nav.className = 'project-nav';
        nav.innerHTML = `
            <button class="prev">←</button>
            <button class="next">→</button>
        `;
        projectGrid.appendChild(nav);

        // Add event listeners
        nav.querySelector('.prev').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + projects.length) % projects.length;
            showProject(currentIndex);
        });

        nav.querySelector('.next').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % projects.length;
            showProject(currentIndex);
        });
    }

    function initThemeToggle() {
        const toggle = document.createElement('button');
        toggle.className = 'theme-toggle';
        toggle.innerHTML = '🌓';
        document.body.appendChild(toggle);

        toggle.addEventListener('click', () => {
            document.body.classList.toggle('light-theme');
        });
    }
}); 