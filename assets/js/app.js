// toggle sidebar
document.addEventListener("DOMContentLoaded", function () {
    const wrapper = document.getElementById("wrapper");
    if (wrapper) {
        document.querySelectorAll("#sidebarToggle, #sidebarToggleTop").forEach(btn => {
            if (btn) {
                btn.addEventListener("click", () => {
                    wrapper.classList.toggle("toggled");
                });
            }
        });
    }
});




// custom script to toggle sidebar visibility
// set as default collapsed
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar-wrapper");
    const toggleBtn = document.getElementById("sidebarToggle");

    if (sidebar && toggleBtn) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
        });
    }
});



