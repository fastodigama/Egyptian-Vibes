window.onload = pageReady;

function pageReady() {
    

    function galleryImgs(){

    
    // Get the main image
    const mainImg = document.getElementById("mainImg");
    // Get all the thumbnails
    const thumbs = document.querySelectorAll("#gallery img");
    // Loop through thumbnails one by one
    for(let i = 0 ; i< thumbs.length; i++){
        thumbs[i].addEventListener("click", function (){
            // When a thumbnail is clicked, update the main image
            mainImg.src = this.src;
        });
    }
    }

   
    galleryImgs();


    


}

    //navbar toggle
    const sidebar = document.getElementById("sidebar");
    function showSidebar() {
        sidebar.style.display = "flex";
    }
    function hideSidebar() {
        sidebar.style.display = "none";
    }
    