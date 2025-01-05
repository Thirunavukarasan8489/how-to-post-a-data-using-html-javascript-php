document
  .getElementById("registrationForm")
  .addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    // Clear previous errors
    document.getElementById("nameErr").innerText = "";
    document.getElementById("emailErr").innerText = "";
    document.getElementById("mobileErr").innerText = "";

    // Collect form data
    const formData = new FormData(this);
    console.log("formData : ", formData);

    // Send data via AJAX
    fetch("form.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          document.getElementById("name").value = "";
          document.getElementById("email").value = "";
          document.getElementById("mobile").value = "";
        } else {
          // Display validation errors
          document.getElementById("nameErr").innerText = data.nameErr || "";
          document.getElementById("emailErr").innerText = data.emailErr || "";
          document.getElementById("mobileErr").innerText = data.mobileErr || "";
        }
      })
      .catch((error) => console.error("Error:", error));
  });
