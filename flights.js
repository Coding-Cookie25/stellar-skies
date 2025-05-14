<script>
    function openFullScreen(img) {
        document.getElementById("fullscreenImage").src = img.src;
        document.getElementById("fullscreenViewer").style.display = "flex";
    }

    function closeFullScreen() {
        document.getElementById("fullscreenViewer").style.display = "none";
    }
</script>
