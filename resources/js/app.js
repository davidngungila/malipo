// Mobile menu toggle - Simplified version
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (!menuToggle || !sidebar) {
        console.error('Menu toggle or sidebar not found');
        return;
    }
    
    console.log('Menu toggle found, setting up event listener');
    
    // Simple menu toggle click handler
    menuToggle.addEventListener('click', function(e) {
        console.log('Menu toggle clicked');
        e.preventDefault();
        e.stopPropagation();
        
        // Debug current state
        console.log('Current sidebar classes:', sidebar.className);
        console.log('Current sidebar styles:', window.getComputedStyle(sidebar).transform);
        
        // Toggle sidebar visibility
        sidebar.classList.toggle('-translate-x-full');
        
        // Debug new state
        console.log('New sidebar classes:', sidebar.className);
        console.log('New sidebar styles:', window.getComputedStyle(sidebar).transform);
        
        // Handle overlay for mobile
        const isMobile = window.innerWidth < 1025;
        const isSidebarOpen = !sidebar.classList.contains('-translate-x-full');
        
        console.log('Is mobile:', isMobile, 'Is sidebar open:', isSidebarOpen);
        
        if (isMobile && isSidebarOpen) {
            // Create overlay when sidebar opens on mobile
            createOverlay();
        } else {
            // Remove overlay when sidebar closes or on desktop
            removeOverlay();
        }
    });
    
    // Close sidebar when clicking outside (mobile only)
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnMenuToggle = menuToggle.contains(event.target);
        const isMobile = window.innerWidth < 1025;
        
        if (!isClickInsideSidebar && !isClickOnMenuToggle && isMobile) {
            sidebar.classList.add('-translate-x-full');
            removeOverlay();
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth < 1025;
        
        if (!isMobile) {
            // Desktop - always show sidebar
            sidebar.classList.remove('-translate-x-full');
            removeOverlay();
        } else {
            // Mobile - hide sidebar
            sidebar.classList.add('-translate-x-full');
            removeOverlay();
        }
    });
    
    // Initialize sidebar state
    const isMobile = window.innerWidth < 1025;
    if (isMobile) {
        sidebar.classList.add('-translate-x-full');
    } else {
        sidebar.classList.remove('-translate-x-full');
    }
    
    // Overlay functions
    function createOverlay() {
        removeOverlay(); // Remove any existing overlay first
        
        const overlay = document.createElement('div');
        overlay.id = 'sidebar-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
        overlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            removeOverlay();
        });
        document.body.appendChild(overlay);
    }
    
    function removeOverlay() {
        const overlay = document.getElementById('sidebar-overlay');
        if (overlay) {
            overlay.remove();
        }
    }
    
    // Make functions globally available
    window.createOverlay = createOverlay;
    window.removeOverlay = removeOverlay;
});
    
    // Delete course confirmation
    const deleteButtons = document.querySelectorAll('[title="Delete Course"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this course?')) {
                // Add delete logic here
                console.log('Course deleted');
            }
        });
    });
    
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
