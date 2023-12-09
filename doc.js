$(function () {
  $("#sidebarCollapse").on("click", function () {
    $("#sidebar, #content").toggleClass("active");
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const userElement = document.getElementById("user");
  const profileCardElement = document.getElementById("profile-card");
  const closeIconElement = document.getElementById("close-icon");

  // Add click event listener to the "User" element
  userElement.addEventListener("click", function () {
    profileCardElement.style.display = "block";
  });

  // Add click event listener to the close icon
  closeIconElement.addEventListener("click", function () {
    profileCardElement.style.display = "none";
  });
});



