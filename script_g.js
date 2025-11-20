document.addEventListener("DOMContentLoaded", function () {


    const buttons = document.querySelectorAll(".button, .buttonhere");
    buttons.forEach(button => {
        button.addEventListener("mousedown", function () {
            this.style.transform = "scale(0.95)";
        });
        button.addEventListener("mouseup", function () {
            this.style.transform = "scale(1)";
        });
        button.addEventListener("mouseleave", function () {
            this.style.transform = "scale(1)";
        });
    });
  
    document.querySelectorAll(".features, .howitworks, .contact").forEach(link => {
        link.addEventListener("click", function (event) {
            event.preventDefault();
            const sectionID = this.textContent.toLowerCase().replace(/\s+/g, "-");
            const section = document.getElementById(sectionID);
            if (section) {
                window.scrollTo({
                    top: section.offsetTop - 50,
                    behavior: "smooth"
                });
            }
        });
    });
  });
  
  const chatbotButton = document.getElementById('chatbotButton');
  const chatbotContainer = document.getElementById('chatbotContainer');
  
  // chatbot visibility
  chatbotButton.addEventListener('click', function(event) {
      event.preventDefault(); 
      if (chatbotContainer.style.display === 'none') {
          chatbotContainer.style.display = 'block'; // Show the chatbot
      } else {
          chatbotContainer.style.display = 'none'; // Hide the chatbot
      }
  });
  