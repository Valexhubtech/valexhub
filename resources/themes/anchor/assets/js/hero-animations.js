import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { TextPlugin } from 'gsap/TextPlugin';

gsap.registerPlugin(ScrollTrigger, TextPlugin);

export class HeroAnimations {
    constructor() {
        this.tl = gsap.timeline({ paused: true });
        this.isMobile = window.innerWidth <= 768;
        this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        if (this.prefersReducedMotion) {
            return; // Skip animations if user prefers reduced motion
        }
        
        this.init();
    }

    init() {
        this.setupRetroGrid();
        this.setupTextAnimations();
        
        if (!this.isMobile) {
            this.setupScrollAnimations();
        } else {
            this.setupMobileOptimizedAnimations();
        }
        
        this.play();
    }

    setupRetroGrid() {
        // Animate the retro grid background
        gsap.set('.retro-grid', {
            opacity: 0,
            scale: 0.8
        });

        this.tl.to('.retro-grid', {
            opacity: 1,
            scale: 1,
            duration: 2,
            ease: 'power2.out'
        });

        // Continuous grid animation
        gsap.to('.retro-grid-line', {
            strokeDashoffset: -1000,
            duration: 20,
            ease: 'none',
            repeat: -1
        });
    }

    setupTextAnimations() {
        // Split text for better animation control
        const heroTitle = document.querySelector('.hero-title');
        const heroSubtitle = document.querySelector('.hero-subtitle');
        
        if (heroTitle) {
            // Split title into words
            const words = heroTitle.textContent.split(' ');
            heroTitle.innerHTML = words.map(word => `<span class="word">${word}</span>`).join(' ');
            
            // Animate each word
            gsap.set('.word', { opacity: 0, y: 100, rotationX: -90 });
            
            this.tl.to('.word', {
                opacity: 1,
                y: 0,
                rotationX: 0,
                duration: 0.2,
                stagger: 0.02,
                ease: 'power2.out'
            }, '-=0.2');
        }

        if (heroSubtitle) {
            gsap.set(heroSubtitle, { opacity: 0, y: 50 });
            
            this.tl.to(heroSubtitle, {
                opacity: 1,
                y: 0,
                duration: 0.3,
                ease: 'power2.out'
            }, '-=0.1');
        }

        // Animate CTA buttons
        gsap.set('.hero-cta', { opacity: 0, scale: 0.8, y: 30 });
        
        this.tl.to('.hero-cta', {
            opacity: 1,
            scale: 1,
            y: 0,
            duration: 0.3,
            stagger: 0.05,
            ease: 'power2.out'
        }, '-=0.1');
    }

    setupScrollAnimations() {
        // Parallax effect for hero content
        gsap.to('.hero-content', {
            y: -100,
            opacity: 0.5,
            scale: 0.9,
            scrollTrigger: {
                trigger: '.hero-section',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });

        // Retro grid parallax
        gsap.to('.retro-grid', {
            y: -200,
            scrollTrigger: {
                trigger: '.hero-section',
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });
    }

    setupFloatingElements() {
        // Floating elements removed for performance
        return;
    }

    setupMobileOptimizedAnimations() {
        // Simplified scroll effects for mobile
        gsap.to('.hero-content', {
            y: -50,
            opacity: 0.8,
            scrollTrigger: {
                trigger: '.hero-section',
                start: 'top top',
                end: 'bottom top',
                scrub: 1 // Smoother scrubbing for mobile
            }
        });

        // Simple retro grid parallax
        gsap.to('.retro-grid', {
            y: -100,
            scrollTrigger: {
                trigger: '.hero-section',
                start: 'top top',
                end: 'bottom top',
                scrub: 2 // Even smoother for mobile
            }
        });
    }

    play() {
        this.tl.play();
    }

    // Method to restart animations when needed
    restart() {
        this.tl.restart();
    }

    // Cleanup method for performance
    destroy() {
        this.tl.kill();
        ScrollTrigger.getAll().forEach(trigger => trigger.kill());
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new HeroAnimations();
});

// Utility function for creating retro grid SVG
export function createRetroGrid(container) {
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.classList.add('retro-grid');
    svg.setAttribute('viewBox', '0 0 1200 800');
    svg.setAttribute('preserveAspectRatio', 'xMidYMid slice');

    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    
    // Grid pattern
    const pattern = document.createElementNS('http://www.w3.org/2000/svg', 'pattern');
    pattern.setAttribute('id', 'grid');
    pattern.setAttribute('width', '40');
    pattern.setAttribute('height', '40');
    pattern.setAttribute('patternUnits', 'userSpaceOnUse');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', 'M 40 0 L 0 0 0 40');
    path.setAttribute('fill', 'none');
    path.setAttribute('stroke', '#3b82f6');
    path.setAttribute('stroke-width', '1');
    path.setAttribute('stroke-opacity', '0.3');
    path.classList.add('retro-grid-line');

    pattern.appendChild(path);
    defs.appendChild(pattern);
    svg.appendChild(defs);

    const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
    rect.setAttribute('width', '100%');
    rect.setAttribute('height', '100%');
    rect.setAttribute('fill', 'url(#grid)');

    svg.appendChild(rect);

    if (container) {
        container.appendChild(svg);
    }

    return svg;
}