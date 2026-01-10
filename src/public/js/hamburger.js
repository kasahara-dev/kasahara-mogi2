document.querySelector("#hamburger").addEventListener("click", (e) => {
    e.currentTarget.classList.toggle("menu-is-open");
    document.getElementById("header-btns").classList.toggle("menu-is-open");
    document.getElementById("header-logo").classList.toggle("menu-is-open");
    document.getElementById("header").classList.toggle("menu-is-open");
    document.getElementById("main").classList.toggle("menu-is-open");
});
