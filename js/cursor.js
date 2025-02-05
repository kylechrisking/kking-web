document.addEventListener('DOMContentLoaded', () => {
    const cursorOuter = document.querySelector('.cursor-outer');
    const cursorInner = document.querySelector('.cursor-inner');
    
    document.addEventListener('mousemove', (e) => {
        cursorOuter.style.transform = `translate(${e.clientX - 15}px, ${e.clientY - 15}px)`;
        cursorInner.style.transform = `translate(${e.clientX - 4}px, ${e.clientY - 4}px)`;
    });

    // Add hover effect for clickable elements
    document.querySelectorAll('a, button').forEach(element => {
        element.addEventListener('mouseenter', () => {
            cursorOuter.style.transform = 'scale(1.5)';
            cursorOuter.style.borderColor = 'var(--color-accent-light)';
        });

        element.addEventListener('mouseleave', () => {
            cursorOuter.style.transform = 'scale(1)';
            cursorOuter.style.borderColor = 'var(--color-accent)';
        });
    });
}); 