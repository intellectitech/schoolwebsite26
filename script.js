document.addEventListener('DOMContentLoaded', () => {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const closeBtn = document.getElementById('close-btn');
    const sidebarMenu = document.getElementById('sidebar-menu');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    // Function to open the sidebar
    const openSidebar = () => {
        sidebarMenu.classList.add('active');
        sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevents background scrolling
    };

    // Function to close the sidebar
    const closeSidebar = () => {
        sidebarMenu.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Restores background scrolling
    };

    // Event Listeners
    hamburgerBtn.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
});
