//search for event to occur within document
document.addEventListener("DOMContentLoaded", function () {

    //const that selects all button elements with URLs
    const buttons = document.querySelectorAll("button[data-url]");


    //listen for click on each button
    buttons.forEach(button => {
        button.addEventListener("click", function () {
            
            //I am a little too stubborn to learn how to properly
            //send data to the site and back to eliminate errors
            //in the console log. The buttons work as is, but the
            //errors may be present in console. - E

            //get URL attribute from the button clicked
            const targetUrl = button.getAttribute("data-url");

            //if there is a URL present, change webpage to the URL
            if(targetUrl) {
                window.location.href = targetUrl;
            }
        });
    });
});