
const chatBtn = document.getElementById("chatBtn");
const closeChat = document.getElementById("closeChat");
const chatBox = document.getElementById("chatBox");

chatBtn.addEventListener("click",function(){

    chatBox.style.display = "flex";

})

closeChat.addEventListener("click", function(){

    chatBox.style.display = "none";

})