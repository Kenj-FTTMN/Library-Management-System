/**
 * Library Management System - Main JavaScript File
 * Modular JavaScript functions for the library system
 */

(function() {
    'use strict';

    /**
     * Counter Animation
     * Animates numbers counting up to target value
     */
    const CounterAnimation = {
        init: function() {
            const counters = document.querySelectorAll('.counter');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                if (isNaN(target)) return;
                
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;
                
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };
                
                // Start animation when card is visible
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            observer.unobserve(entry.target);
                        }
                    });
                });
                
                observer.observe(counter.closest('.dashboard-card') || counter);
            });
        }
    };

    /**
     * Form Validation Helper
     */
    const FormValidation = {
        init: function() {
            // Add custom validation if needed
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }
    };

    /**
     * Initialize all modules when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        CounterAnimation.init();
        FormValidation.init();
    });

    // Export for global access if needed
    window.LibrarySystem = {
        CounterAnimation: CounterAnimation,
        FormValidation: FormValidation
    };

})();

