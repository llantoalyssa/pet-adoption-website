// ===== Smooth Scrolling for Navigation =====
document.querySelectorAll('header nav ul li a').forEach(link => {
    link.addEventListener('click', function(e) {
        // Only scroll if it's an internal section link
        if(this.getAttribute('href').startsWith('#')) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.getElementById(targetId);
            if(targetSection) {
                window.scrollTo({
                    top: targetSection.offsetTop - 70, // adjust for sticky header
                    behavior: 'smooth'
                });
            }
        }
    });
});

// ===== Adopt Button Behavior =====
document.querySelectorAll('.adopt-btn').forEach(button => {
    if(button.tagName.toLowerCase() === 'button') {
        // Only show alert for actual <button>, not <a> links
        button.addEventListener('click', () => {
            alert("Thank you for your interest! Please contact us via the form to complete the adoption process.");
        });
    }
});

// ===== Optional: Future Plugin Initializations =====
// Example: chat plugin can be initialized here
// initChatPlugin();
