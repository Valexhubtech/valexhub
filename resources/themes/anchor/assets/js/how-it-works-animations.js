export class HowItWorksAnimations {
    constructor() {
        this.init();
    }

    init() {
        if (window.innerWidth < 768) return;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const section = document.querySelector('#how-it-works-desktop');
        const track   = document.querySelector('.how-it-works-track');

        if (!section || !track) return;

        const getScrollDistance = () => track.scrollWidth - track.parentElement.clientWidth + 80;

        gsap.to(track, {
            x: () => -getScrollDistance(),
            ease: 'none',
            scrollTrigger: {
                trigger: '#how-it-works',
                start: 'top top',
                end: () => `+=${getScrollDistance() + window.innerHeight * 0.5}`,
                pin: true,
                scrub: 1.2,
                anticipatePin: 1,
                invalidateOnRefresh: true,
            },
        });

        // Arrow nudge animation
        const arrow = document.querySelector('.how-it-works-arrow');
        if (arrow) {
            gsap.to(arrow, {
                x: 5,
                repeat: -1,
                yoyo: true,
                duration: 0.8,
                ease: 'power1.inOut',
            });
        }
    }
}
