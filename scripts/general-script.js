// This function is called when the window has fully loaded
document.addEventListener("DOMContentLoaded", pageReady);


function pageReady() {
    // Select the main image element by its ID
    const mainImg = document.getElementById("mainImg");

    // Select all thumbnail images within the gallery by their parent element ID
    const thumbs = document.querySelectorAll("#gallery img");

    // Function to handle image gallery click events for desktop
    function galleryImgs() {
        // Loop through all thumbnail images
        for (let i = 0; i < thumbs.length; i++) {
            // Add a click event listener to each thumbnail
            thumbs[i].addEventListener("click", function () {
                // When a thumbnail is clicked, change the main image source to the clicked thumbnail's source
                mainImg.src = this.src;
                // Update current index to match clicked thumbnail
                current = i;
            });
        }
    }

    // Call the function to set up the gallery image click events
    galleryImgs();

    // Function to handle slider logic (works on all screen sizes)
    function imageSlider() {
        // Select the next and previous buttons by their IDs
        const nextButton = document.getElementById("next");
        const previousButton = document.getElementById("previous");

        // Check if there are no thumbnails or buttons, then disable the slider
        if (!thumbs.length || !nextButton || !previousButton) {
            console.log("No slides or buttons found — slider disabled");
            return;
        }

        // Initialize the current image index
        let current = 0;
        // Get the total number of thumbnails
        const total = thumbs.length;

        // Initialize the main image to the first thumbnail
        mainImg.src = thumbs[current].src;

        // Add a click event listener to the next button
        nextButton.addEventListener("click", function () {
            // Move to the next image, wrapping around to the first if at the end
            current = (current + 1) % total;
            // Update the main image to the new current image
            mainImg.src = thumbs[current].src;
        });

        // Add a click event listener to the previous button
        previousButton.addEventListener("click", function () {
            // Move to the previous image, wrapping around to the last if at the beginning
            current = (current - 1 + total) % total;
            // Update the main image to the new current image
            mainImg.src = thumbs[current].src;
        });
    }

    // Call the slider function for all screen sizes
    console.log("Image slider activated");
    imageSlider();
}

// Function to show the sidebar
function showSidebar() {
    // Change the display style of the sidebar to "flex" to make it visible
    sidebar.style.display = "flex";
}

// Function to hide the sidebar
function hideSidebar() {
    // Change the display style of the sidebar to "none" to hide it
    sidebar.style.display = "none";
}