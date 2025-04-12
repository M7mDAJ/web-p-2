// Select the <body> element
let body = document.body;

// Select the user profile element inside the header
let profile = document.querySelector('.header .flex .profile');

// Toggle profile visibility when the user button is clicked
document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active'); // Show/hide profile menu
   searchForm.classList.remove('active'); // Ensure search form is hidden
}

// Select the search form inside the header
let searchForm = document.querySelector('.header .flex .search-form');

// Toggle search form visibility when the search button is clicked
document.querySelector('#search-btn').onclick = () =>{
   searchForm.classList.toggle('active'); // Show/hide search form
   profile.classList.remove('active'); // Ensure profile menu is hidden
}

// Select the sidebar element
let sideBar = document.querySelector('.side-bar');

// Toggle sidebar visibility and body state when the menu button is clicked
document.querySelector('#menu-btn').onclick = () =>{
   sideBar.classList.toggle('active'); // Show/hide sidebar
   body.classList.toggle('active'); // Toggle body state (possibly for overlay or scroll lock)
}

// Close the sidebar when the close button is clicked
document.querySelector('.side-bar .close-side-bar').onclick = () =>{
   sideBar.classList.remove('active'); // Hide sidebar
   body.classList.remove('active'); // Remove body overlay or scroll lock
}

// Enforce maxLength constraint for all number input fields
document.querySelectorAll('input[type="number"]').forEach(InputNumber => {
   InputNumber.oninput = () =>{
      // If input exceeds allowed maxLength, trim the value
      if(InputNumber.value.length > InputNumber.maxLength) 
         InputNumber.value = InputNumber.value.slice(0, InputNumber.maxLength);
   }
});

// Handle scroll events
window.onscroll = () =>{
   // Hide profile and search form on scroll
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   // For smaller screens, also hide sidebar and body active state
   if(window.innerWidth < 1200){
      sideBar.classList.remove('active');
      body.classList.remove('active');
   }
}

// Select the dark/light mode toggle button
let toggleBtn = document.querySelector('#toggle-btn');

// Retrieve the current dark mode setting from localStorage
let darkMode = localStorage.getItem('dark-mode');

// Function to enable dark mode
const enabelDarkMode = () =>{
   toggleBtn.classList.replace('fa-sun', 'fa-moon'); // Update icon
   body.classList.add('dark'); // Add dark class to body
   localStorage.setItem('dark-mode', 'enabled'); // Save preference
}

// Function to disable dark mode
const disableDarkMode = () =>{
   toggleBtn.classList.replace('fa-moon', 'fa-sun'); // Update icon
   body.classList.remove('dark'); // Remove dark class from body
   localStorage.setItem('dark-mode', 'disabled'); // Save preference
}

// Apply dark mode if previously enabled
if(darkMode === 'enabled'){
   enabelDarkMode();
}

// Toggle dark/light mode when toggle button is clicked
toggleBtn.onclick = (e) =>{
   let darkMode = localStorage.getItem('dark-mode');
   if(darkMode === 'disabled'){
      enabelDarkMode();
   }else{
      disableDarkMode();
   }
}
