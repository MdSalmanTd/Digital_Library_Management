
const menu = document.getElementById("mainMenu");
const moreMenu = document.getElementById("moreMenu");
const moreBtn = document.getElementById("moreBtn");

function adjustMenu() {
    // return hidden items back to main menu first
    while (moreMenu.children.length > 0) {
        menu.appendChild(moreMenu.firstElementChild);
    }

    // If overflowing → move items to dropdown
    while (menu.scrollWidth > menu.clientWidth) {
        moreMenu.prepend(menu.lastElementChild);
    }

    // Show "..." only if something is hidden
    moreBtn.style.display = moreMenu.children.length ? "block" : "none";

    // Close dropdown when resizing
    moreMenu.style.display = "none";
}

moreBtn.addEventListener("click", () => {
    moreMenu.style.display = 
        moreMenu.style.display === "block" ? "none" : "block";
});

window.addEventListener("resize", adjustMenu);
window.addEventListener("load", adjustMenu);




