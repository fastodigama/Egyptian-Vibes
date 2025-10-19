window.onload = pageReady;

function pageReady() {

    const mainImg = document.getElementById("mainImg");
    const thumbs = document.querySelectorAll("#gallery img");

    // Image gallery click to change main image (desktop)
    function galleryImgs() {
        for (let i = 0; i < thumbs.length; i++) {
            thumbs[i].addEventListener("click", function () {
                mainImg.src = this.src;
            });
        }
    }

    galleryImgs();

    // Mobile slider logic
    function mobileSlider() {
        const nextButton = document.getElementById("next");
        const previousButton = document.getElementById("previous");

        if (!thumbs.length || !nextButton || !previousButton) {
            console.log("No slides or buttons found — slider disabled");
            return;
        }

        let current = 0;
        const total = thumbs.length;

        // Initialize main image
        mainImg.src = thumbs[current].src;

        nextButton.addEventListener("click", function () {
            current = (current + 1) % total;
            mainImg.src = thumbs[current].src;
        });

        previousButton.addEventListener("click", function () {
            current = (current - 1 + total) % total;
            mainImg.src = thumbs[current].src;
        });
    }

    // Only run mobile slider if width <= 768px
    if (window.innerWidth <= 768) {
        console.log("Mobile slider activated");
        mobileSlider();
    }
}

// --- navbar toggle ---
const sidebar = document.getElementById("sidebar");

function showSidebar() {
    sidebar.style.display = "flex";
}

function hideSidebar() {
    sidebar.style.display = "none";
}
