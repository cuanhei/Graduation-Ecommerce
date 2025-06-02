function previewPhoto(event,ID)
{
       var input = event.target;
       var preview = document.getElementById(ID);
       
       var reader = new FileReader();
       reader.onload = function() {
           preview.src = reader.result;
       }
       reader.readAsDataURL(input.files[0]);
}

function triggerFileInput(inputID) {
    document.getElementById(inputID).click();
}

function showContainer(containerID){
    document.getElementById(containerID).style.display = "flex";
}

function hideContainer(containerID){
    document.getElementById(containerID).style.display = "none";
}

function toggleContainer(containerID) {
    var container = document.getElementById(containerID);
    if (container.style.display === "none" || container.style.display === "") {
        container.style.display = "flex";
    } else {
        container.style.display = "none";
    }
}


function enableInputBeforeSubmit(id) {
    const input = document.getElementById(id);
    input.disabled = false;  // Temporarily enable the input field
}
