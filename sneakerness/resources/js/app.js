import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector(".navbar");
const closeBtn = document.querySelector(".close-menu");
const overlay = document.querySelector(".overlay");

function openMenu() {
    navMenu.classList.add("active");
    overlay.style.display = "block";
    document.documentElement.classList.add("menu-open");
    document.body.classList.add("menu-open");
}

function closeMenu() {
    navMenu.classList.remove("active");
    overlay.style.display = "none";
    document.documentElement.classList.remove("menu-open");
    document.body.classList.remove("menu-open");
}

if (hamburger && navMenu && closeBtn && overlay) {
    hamburger.addEventListener("click", openMenu);
    closeBtn.addEventListener("click", closeMenu);
    overlay.addEventListener("click", closeMenu);
    overlay.addEventListener("touchmove", (e) => e.preventDefault(), { passive: false });
}
