// toggle sidebar
 const wrapper = document.getElementById("wrapper");
    document.querySelectorAll("#sidebarToggle, #sidebarToggleTop").forEach(btn => {
        btn.addEventListener("click", () => {
            wrapper.classList.toggle("toggled");
        });
    });