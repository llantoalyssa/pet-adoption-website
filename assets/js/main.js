// ===== Smooth Scrolling for Navigation =====
document.querySelectorAll('header nav ul li a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href').substring(1);
        const targetSection = document.getElementById(targetId);
        if(targetSection) {
            window.scrollTo({
                top: targetSection.offsetTop - 70, // adjust for sticky header
                behavior: 'smooth'
            });
        }
    });
});

// ===== Adopt Me Button Alert =====
const adoptButtons = document.querySelectorAll('.adopt-btn');

adoptButtons.forEach(button => {
    button.addEventListener('click', () => {
        alert("Thank you for your interest! Please contact us via the form to complete the adoption process.");
    });
});


