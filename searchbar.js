document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");

    // Add event listeners for button click and Enter key press
    searchButton.addEventListener("click", function () {
        performSearch();
    });

    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            performSearch();
        }
    });

    function performSearch() {
        const query = searchInput.value.trim().toLowerCase();

        // Define pages and their URLs
        const pages = {
            "home": "index (2).html",
            "about us": "about.html",
            "destinations": "destinations.html",
            "book now": "trip details.html",
            "login": "login.html",
            "sign in": "register.html",
            "terms": "terms.html",
            "privacy policy": "privacy.html",
            "contact": "contact.html"
        };

        // Check if the query matches any page
        for (const key in pages) {
            if (query.includes(key)) {
                window.location.href = pages[key];
                return;
            }
        }

        // Show an alert if no match is found
        alert("No matching page found. Please try a different search term!");
    }
});