import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { Draggable } from 'gsap/Draggable';

gsap.registerPlugin(ScrollTrigger, Draggable);

export class FeaturesAnimations {
    constructor() {
        this.init();
    }

    init() {
        this.setupBentoGridAnimations();
        this.setupMobileScrollAnimations();
        this.setupHoverAnimations();
        this.setupIntersectionObserver();
    }

    setupBentoGridAnimations() {
        // Staggered entrance animation for bento grid items
        gsap.set('.bento-item', {
            opacity: 0,
            y: 50,
            scale: 0.9
        });

        ScrollTrigger.create({
            trigger: '.features-section',
            start: 'top 80%',
            onEnter: () => {
                gsap.to('.bento-item', {
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 0.8,
                    stagger: {
                        grid: [2, 4], // Adjust based on your grid layout
                        from: "start",
                        amount: 0.8
                    },
                    ease: 'power2.out'
                });
            }
        });

        // Animate icons within cards
        gsap.set('.bento-item .card-icon', {
            scale: 0,
            rotation: -180
        });

        ScrollTrigger.create({
            trigger: '.features-section',
            start: 'top 70%',
            onEnter: () => {
                gsap.to('.bento-item .card-icon', {
                    scale: 1,
                    rotation: 0,
                    duration: 0.6,
                    stagger: 0.1,
                    ease: 'back.out(1.7)'
                });
            }
        });
    }

    setupMobileScrollAnimations() {
        const scrollContainer = document.querySelector('.mobile-scroll-container');
        const scrollWrapper = document.querySelector('.mobile-scroll-wrapper');
        
        if (scrollContainer && scrollWrapper) {
            // Enhanced smooth scrolling for mobile
            let scrollTween;
            
            // Auto-scroll functionality with GSAP
            const startAutoScroll = () => {
                const containerWidth = scrollContainer.offsetWidth;
                const wrapperWidth = scrollWrapper.scrollWidth;
                const maxScroll = wrapperWidth - containerWidth;
                
                if (maxScroll > 0) {
                    scrollTween = gsap.to(scrollWrapper, {
                        x: -maxScroll,
                        duration: 20,
                        ease: 'none',
                        repeat: -1,
                        yoyo: true,
                        repeatDelay: 2
                    });
                }
            };

            // Pause on interaction
            const pauseAutoScroll = () => {
                if (scrollTween) {
                    scrollTween.pause();
                }
            };

            const resumeAutoScroll = () => {
                if (scrollTween) {
                    gsap.delayedCall(2, () => {
                        scrollTween.resume();
                    });
                }
            };

            // Make it draggable on mobile
            Draggable.create(scrollWrapper, {
                type: 'x',
                bounds: scrollContainer,
                inertia: true,
                onDragStart: pauseAutoScroll,
                onDragEnd: resumeAutoScroll,
                onThrowUpdate: function() {
                    // Snap to cards on mobile
                    const cardWidth = 288; // w-72 = 288px
                    const progress = Math.abs(this.x) / this.maxX;
                    const snapIndex = Math.round(progress * (scrollWrapper.children.length - 1));
                    
                    gsap.to(scrollWrapper, {
                        x: -snapIndex * cardWidth,
                        duration: 0.3,
                        ease: 'power2.out'
                    });
                }
            });

            // Start auto-scroll
            startAutoScroll();

            // Add touch event listeners
            scrollContainer.addEventListener('touchstart', pauseAutoScroll);
            scrollContainer.addEventListener('touchend', resumeAutoScroll);
            scrollContainer.addEventListener('mouseenter', pauseAutoScroll);
            scrollContainer.addEventListener('mouseleave', resumeAutoScroll);
        }
    }

    setupHoverAnimations() {
        // Enhanced hover animations for cards
        document.querySelectorAll('.bento-item').forEach(item => {
            const icon = item.querySelector('.card-icon');
            const title = item.querySelector('h3');
            const description = item.querySelector('p');

            item.addEventListener('mouseenter', () => {
                gsap.to(item, {
                    scale: 1.05,
                    y: -10,
                    duration: 0.3,
                    ease: 'power2.out'
                });

                gsap.to(icon, {
                    scale: 1.2,
                    rotation: 10,
                    duration: 0.3,
                    ease: 'power2.out'
                });

                gsap.to([title, description], {
                    color: '#1e3a8a',
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });

            item.addEventListener('mouseleave', () => {
                gsap.to(item, {
                    scale: 1,
                    y: 0,
                    duration: 0.3,
                    ease: 'power2.out'
                });

                gsap.to(icon, {
                    scale: 1,
                    rotation: 0,
                    duration: 0.3,
                    ease: 'power2.out'
                });

                gsap.to([title, description], {
                    color: 'initial',
                    duration: 0.3,
                    ease: 'power2.out'
                });
            });
        });

        // Code terminal typing animation
        const codeElement = document.querySelector('.code-content');
        if (codeElement) {
            const codeText = codeElement.textContent;
            codeElement.textContent = '';

            ScrollTrigger.create({
                trigger: codeElement,
                start: 'top 80%',
                onEnter: () => {
                    let currentIndex = 0;
                    const typeInterval = setInterval(() => {
                        if (currentIndex < codeText.length) {
                            codeElement.textContent += codeText[currentIndex];
                            currentIndex++;
                        } else {
                            clearInterval(typeInterval);
                        }
                    }, 50);
                }
            });
        }
    }

    setupIntersectionObserver() {
        // Use Intersection Observer for performance optimization
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    
                    // Trigger specific animations based on element type
                    if (entry.target.classList.contains('custom-development-card')) {
                        this.animateCodeTerminal(entry.target);
                    }
                }
            });
        }, observerOptions);

        // Observe all bento items
        document.querySelectorAll('.bento-item').forEach(item => {
            observer.observe(item);
        });
    }

    animateCodeTerminal(element) {
        const terminalLines = element.querySelectorAll('.terminal-line');
        
        gsap.set(terminalLines, {
            opacity: 0,
            x: -50
        });

        gsap.to(terminalLines, {
            opacity: 1,
            x: 0,
            duration: 0.5,
            stagger: 0.1,
            ease: 'power2.out'
        });

        // Simulate cursor blink
        const cursor = element.querySelector('.cursor');
        if (cursor) {
            gsap.to(cursor, {
                opacity: 0,
                duration: 0.5,
                repeat: -1,
                yoyo: true,
                ease: 'power2.inOut'
            });
        }
    }

    // Method to refresh animations (useful for dynamic content)
    refresh() {
        ScrollTrigger.refresh();
    }

    // Cleanup method
    destroy() {
        ScrollTrigger.getAll().forEach(trigger => trigger.kill());
        Draggable.get('.mobile-scroll-wrapper')?.kill();
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new FeaturesAnimations();
});

// Utility function for mobile detection
export function isMobile() {
    return window.innerWidth <= 768;
}

// Utility function for performance optimization
export function enableHardwareAcceleration(elements) {
    gsap.set(elements, {
        force3D: true,
        transformStyle: 'preserve-3d',
        backfaceVisibility: 'hidden'
    });
}