import { initCursor } from './modules/cursor.js';
import { initParticles } from './modules/particles.js';
import { initTheme } from './modules/theme.js';
import { initAnimations } from './modules/animations.js';
import { initEasterEggs } from './modules/easter-eggs.js';
import { initUtils } from './modules/utils.js';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize all modules
    initCursor();
    initParticles();
    initTheme();
    initAnimations();
    initEasterEggs();
    initUtils();
}); 