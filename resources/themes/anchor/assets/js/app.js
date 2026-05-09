import Alpine from 'alpinejs'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { TextPlugin } from 'gsap/TextPlugin'
import { Draggable } from 'gsap/Draggable'

// Import our animation modules
import { HeroAnimations } from './hero-animations.js'
import { FeaturesAnimations } from './features-animations.js'

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger, TextPlugin, Draggable)

// Make GSAP available globally
window.gsap = gsap
window.Alpine = Alpine

// Start Alpine.js
Alpine.start()

// Initialize animations after DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Initialize hero animations
    window.heroAnimations = new HeroAnimations()
    
    // Initialize features animations
    window.featuresAnimations = new FeaturesAnimations()
    
    // Add structured data for SEO
    addStructuredData()
})

// Function to add structured data
function addStructuredData() {
    // Organization structured data
    const orgData = {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "ValexHub",
        "description": "Custom software solutions for Nigerian businesses. We build tailored digital tools that help recover lost revenue and scale profitably.",
        "url": window.location.origin,
        "logo": window.location.origin + "/valexhub-logo.png",
        "sameAs": [
            "https://twitter.com/valexhub",
            "https://linkedin.com/company/valexhub"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "Customer Service",
            "areaServed": "NG",
            "availableLanguage": "English"
        },
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Lagos",
            "addressCountry": "Nigeria"
        },
        "founder": {
            "@type": "Person",
            "name": "ValexHub Team"
        },
        "offers": [
            {
                "@type": "Service",
                "name": "Custom Software Development",
                "description": "Bespoke software solutions tailored to your business workflow and requirements"
            },
            {
                "@type": "Service",
                "name": "School Management System",
                "description": "Complete student, fees, attendance and reporting tools for private schools"
            },
            {
                "@type": "Service",
                "name": "Hotel Booking System",
                "description": "Room management, reservations, POS and guest experience platform"
            },
            {
                "@type": "Service",
                "name": "Pharmacy POS System",
                "description": "Stock control, expiry alerts, sales and supplier management software"
            }
        ]
    }

    // Website structured data
    const websiteData = {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "ValexHub",
        "url": window.location.origin,
        "description": "Custom software solutions for Nigerian SMEs - School management, hotel booking, pharmacy POS, and more",
        "publisher": {
            "@type": "Organization",
            "name": "ValexHub"
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": window.location.origin + "?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }

    // Create and inject script elements
    const orgScript = document.createElement('script')
    orgScript.type = 'application/ld+json'
    orgScript.textContent = JSON.stringify(orgData)
    document.head.appendChild(orgScript)

    const websiteScript = document.createElement('script')
    websiteScript.type = 'application/ld+json'
    websiteScript.textContent = JSON.stringify(websiteData)
    document.head.appendChild(websiteScript)
}

window.demoButtonClickMessage = function(event){
    event.preventDefault(); new FilamentNotification().title('Modify this button in your theme folder').icon('heroicon-o-pencil-square').iconColor('info').send()
}