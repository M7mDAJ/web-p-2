let body = document.body;

const profile = document.querySelector('.header .flex .profile');
const searchForm = document.querySelector('.header .flex .search-form');
const sideBar = document.querySelector('.side-bar');
const userBtn = document.querySelector('#user-btn');
const searchBtn = document.querySelector('#search-btn');
const menuBtn = document.querySelector('#menu-btn');
const closeSidebarBtn = document.querySelector('.side-bar .close-side-bar');
const toggleBtn = document.querySelector('#toggle-btn');

// Toggle user profile
userBtn?.addEventListener('click', () => {
   profile?.classList.toggle('active');
   searchForm?.classList.remove('active');
});

// Toggle search form
searchBtn?.addEventListener('click', () => {
   searchForm?.classList.toggle('active');
   profile?.classList.remove('active');
});

// Toggle sidebar
menuBtn?.addEventListener('click', () => {
   sideBar?.classList.toggle('active');
   body.classList.toggle('active');
});

// Close sidebar
closeSidebarBtn?.addEventListener('click', () => {
   sideBar?.classList.remove('active');
   body.classList.remove('active');
});

// Scroll handler with basic debounce
let scrollTimeout;
window.addEventListener('scroll', () => {
   clearTimeout(scrollTimeout);
   scrollTimeout = setTimeout(() => {
      profile?.classList.remove('active');
      searchForm?.classList.remove('active');

      if (window.innerWidth < 1200) {
         sideBar?.classList.remove('active');
         body.classList.remove('active');
      }
   }, 100);
});

// Dark mode
const enableDarkMode = () => {
   toggleBtn?.classList.replace('fa-sun', 'fa-moon');
   body.classList.add('dark');
   localStorage.setItem('dark-mode', 'enabled');
};

const disableDarkMode = () => {
   toggleBtn?.classList.replace('fa-moon', 'fa-sun');
   body.classList.remove('dark');
   localStorage.setItem('dark-mode', 'disabled');
};

if (localStorage.getItem('dark-mode') === 'enabled') {
   enableDarkMode();
}

toggleBtn?.addEventListener('click', () => {
   const isEnabled = localStorage.getItem('dark-mode') === 'enabled';
   isEnabled ? disableDarkMode() : enableDarkMode();
});
