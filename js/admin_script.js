// Get a reference to the <body> element
let body = document.body;

// Select the user profile element inside the header
let profile = document.querySelector('.header .flex .profile');

// When the user button is clicked:
document.querySelector('#user-btn').onclick = () =>{
   // Toggle the visibility of the profile menu
   profile.classList.toggle('active');

   // Ensure the search form is hidden
   searchForm.classList.remove('active');
}

// Select the search form element inside the header
let searchForm = document.querySelector('.header .flex .search-form');

// When the search button is clicked:
document.querySelector('#search-btn').onclick = () =>{
   // Toggle the visibility of the search form
   searchForm.classList.toggle('active');

   // Ensure the profile menu is hidden
   profile.classList.remove('active');
}

// Select the sidebar element
let sideBar = document.querySelector('.side-bar');

// When the menu button is clicked:
document.querySelector('#menu-btn').onclick = () =>{
   // Toggle the visibility of the sidebar
   sideBar.classList.toggle('active');

   // Toggle the 'active' class on the body (used for overlays or scroll lock)
   body.classList.toggle('active');
}

// When the close button on the sidebar is clicked:
document.querySelector('.side-bar .close-side-bar').onclick = () =>{
   // Hide the sidebar
   sideBar.classList.remove('active');

   // Remove the 'active' class from the body
   body.classList.remove('active');
}

// When the window is scrolled:
window.onscroll = () =>{
   // Hide the profile menu
   profile.classList.remove('active');

   // Hide the search form
   searchForm.classList.remove('active');

   // If the screen width is less than 1200px (mobile/tablet)
   if(window.innerWidth < 1200){
      // Hide the sidebar
      sideBar.classList.remove('active');

      // Remove the 'active' class from the body
      body.classList.remove('active');
   }
}

// Select the dark/light mode toggle button
let toggleBtn = document.querySelector('#toggle-btn');

// Get the saved dark mode preference from localStorage
let darkMode = localStorage.getItem('dark-mode');

// Function to enable dark mode
const enabelDarkMode = () =>{
   // Replace the sun icon with a moon icon
   toggleBtn.classList.replace('fa-sun', 'fa-moon');

   // Add the 'dark' class to the body
   body.classList.add('dark');

   // Save the dark mode state in localStorage
   localStorage.setItem('dark-mode', 'enabled');
}

// Function to disable dark mode
const disableDarkMode = () =>{
   // Replace the moon icon with a sun icon
   toggleBtn.classList.replace('fa-moon', 'fa-sun');

   // Remove the 'dark' class from the body
   body.classList.remove('dark');

   // Save the light mode state in localStorage
   localStorage.setItem('dark-mode', 'disabled');
}

// When the toggle button is clicked:
toggleBtn.onclick = (e) =>{
   // Get the current dark mode setting
   let darkMode = localStorage.getItem('dark-mode');

   // Enable or disable dark mode based on the current setting
   if(darkMode === 'disabled'){
      enabelDarkMode();
   }else{
      disableDarkMode();
   }
}

// On page load, enable dark mode if it was previously enabled
if(darkMode === 'enabled'){
   enabelDarkMode();
}
