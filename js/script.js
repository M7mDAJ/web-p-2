let body = document.body;

let profile = document.querySelector('.header .flex .profile');
let searchForm = document.querySelector('.header .flex .search-form');
let sideBar = document.querySelector('.side-bar');

const userBtn = document.querySelector('#user-btn');
const searchBtn = document.querySelector('#search-btn');
const menuBtn = document.querySelector('#menu-btn');
const closeSidebarBtn = document.querySelector('.side-bar .close-side-bar');
const toggleBtn = document.querySelector('#toggle-btn');

// Toggle user profile
if (userBtn) {
   userBtn.onclick = () => {
      profile.classList.toggle('active');
      searchForm.classList.remove('active');
   };
}

// Toggle search form
if (searchBtn) {
   searchBtn.onclick = () => {
      searchForm.classList.toggle('active');
      profile.classList.remove('active');
   };
}

// Toggle sidebar
if (menuBtn) {
   menuBtn.onclick = () => {
      sideBar.classList.toggle('active');
      body.classList.toggle('active');
   };
}

// Close sidebar
if (closeSidebarBtn) {
   closeSidebarBtn.onclick = () => {
      sideBar.classList.remove('active');
      body.classList.remove('active');
   };
}

// Limit number input length
document.querySelectorAll('input[type="number"]').forEach(input => {
   input.addEventListener('input', () => {
      const maxLength = input.getAttribute('maxlength');
      if (maxLength && input.value.length > maxLength) {
         input.value = input.value.slice(0, maxLength);
      }
   });
});

// Close popups when clicking outside
window.addEventListener('click', (e) => {
   if (!profile.contains(e.target) && !userBtn.contains(e.target)) {
      profile.classList.remove('active');
   }
   if (!searchForm.contains(e.target) && !searchBtn.contains(e.target)) {
      searchForm.classList.remove('active');
   }
});

// Smart scroll handler (throttle)
let scrollTimeout;
window.addEventListener('scroll', () => {
   clearTimeout(scrollTimeout);
   scrollTimeout = setTimeout(() => {
      profile.classList.remove('active');
      searchForm.classList.remove('active');

      if (window.innerWidth < 1200) {
         sideBar.classList.remove('active');
         body.classList.remove('active');
      }
   }, 100);
});

// Dark mode toggle
let darkMode = localStorage.getItem('dark-mode');

const setDarkMode = (enable) => {
   toggleBtn.classList.replace(enable ? 'fa-sun' : 'fa-moon', enable ? 'fa-moon' : 'fa-sun');
   body.classList.toggle('dark', enable);
   localStorage.setItem('dark-mode', enable ? 'enabled' : 'disabled');
};

if (darkMode === 'enabled') setDarkMode(true);

toggleBtn?.addEventListener('click', () => {
   const isDark = localStorage.getItem('dark-mode') === 'enabled';
   setDarkMode(!isDark);
});
