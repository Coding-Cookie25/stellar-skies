document.addEventListener("DOMContentLoaded", function () {
    const video = document.getElementById("background-video");
    const overlay = document.querySelector(".transition-overlay");
    const content = document.getElementById("content");
    const links = document.querySelectorAll("nav a");

    // Check if the video exists before adding event listener
    if (video) {
        video.addEventListener("ended", function () {
            console.log("Video ended - Applying transition");
            overlay.classList.add("show-overlay");

            setTimeout(() => {
                window.location.href = "index (2).html"; // Ensure correct filename
            }, 1000);
        });
    }

    // Add fade-in effect to text content
    if (content) {
        setTimeout(() => {
            content.classList.add("text-visible");
        }, 300);
    }

    // Smooth navigation transition effect
    links.forEach(link => {
        link.addEventListener("click", function (event) {
            const href = link.getAttribute("href");

            if (href && !href.startsWith("#")) {
                event.preventDefault();
                overlay.classList.add("show-overlay");

                setTimeout(() => {
                    window.location.href = href;
                }, 800);
            }
        });
    });
});

