// Navbar scroll effect
window.addEventListener("scroll", function () {
    const navbar = document.getElementById("navbar");
    if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
});

// Mobile menu toggle
const mobileToggle = document.getElementById("mobileToggle");
const navMenu = document.getElementById("navMenu");

mobileToggle.addEventListener("click", function () {
    navMenu.classList.toggle("active");
    const icon = this.querySelector("i");
    if (navMenu.classList.contains("active")) {
        icon.classList.remove("fa-bars");
        icon.classList.add("fa-times");
    } else {
        icon.classList.remove("fa-times");
        icon.classList.add("fa-bars");
    }
});

// FAQ accordion
const faqItems = document.querySelectorAll(".faq-item");

if (faqItems) {
    faqItems.forEach((item) => {
        const question = item.querySelector(".faq-question");

        question.addEventListener("click", () => {
            const isActive = item.classList.contains("active");

            // Close all FAQ items
            faqItems.forEach((faq) => {
                faq.classList.remove("active");
            });

            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add("active");
            }
        });
    });
}

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -100px 0px",
};

const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
        }
    });
}, observerOptions);

document.querySelectorAll(".pricing-card, .faq-item").forEach((el) => {
    el.style.opacity = "0";
    el.style.transform = "translateY(30px)";
    el.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(el);
});

// Staggered animation for pricing cards
document.querySelectorAll(".pricing-card").forEach((card, index) => {
    card.style.transitionDelay = `${index * 0.15}s`;
});

// Staggered animation for FAQ items
document.querySelectorAll(".faq-item").forEach((item, index) => {
    item.style.transitionDelay = `${index * 0.1}s`;
});
