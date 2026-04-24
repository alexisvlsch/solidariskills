function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  const container = document.querySelector('.container');
  
  sidebar.classList.toggle('collapsed');
  
  if (sidebar.classList.contains('collapsed')) {
    container.style.marginLeft = '0';
  } else {
    container.style.marginLeft = '';
  }
  
  const isCollapsed = sidebar.classList.contains('collapsed');
  localStorage.setItem('sidebarCollapsed', isCollapsed);
}

document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.querySelector('.sidebar');
  const container = document.querySelector('.container');
  const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  
  if (isCollapsed) {
    sidebar.classList.add('collapsed');
    container.style.marginLeft = '0';
  }
});