document.addEventListener("DOMContentLoaded", function () {

    const toggle =
        document.getElementById("sidebarToggle");

    const sidebar =
        document.getElementById("sidebar");

    if (!toggle || !sidebar) {
        return;
    }

    toggle.addEventListener("click", function () {
        sidebar.classList.toggle("is-open");
    });

});