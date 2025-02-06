document.addEventListener('DOMContentLoaded', () => {
    const cursorOuter = document.querySelector('.cursor-outer');
    const cursorInner = document.querySelector('.cursor-inner');
    
    document.addEventListener('mousemove', (e) => {
        cursorOuter.style.left = e.clientX + 'px';
        cursorOuter.style.top = e.clientY + 'px';
        cursorInner.style.left = e.clientX + 'px';
        cursorInner.style.top = e.clientY + 'px';
    });

    // Add hover effect for clickable elements
    document.querySelectorAll('a, button').forEach(element => {
        element.addEventListener('mouseenter', () => {
            cursorOuter.style.transform = 'translate(-50%, -50%) scale(1.5)';
            cursorOuter.style.borderColor = 'var(--color-accent-light)';
        });

        element.addEventListener('mouseleave', () => {
            cursorOuter.style.transform = 'translate(-50%, -50%) scale(1)';
            cursorOuter.style.borderColor = 'var(--color-accent)';
        });
    });
}); 