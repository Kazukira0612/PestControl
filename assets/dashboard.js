function showView(viewName) {
  // Hide all views
  document.querySelectorAll('.view').forEach(view => {
    view.classList.remove('active');
  });
  
  // Remove active class from all nav items
  document.querySelectorAll('.nav-item').forEach(item => {
    item.classList.remove('active');
  });
  
  // Show selected view
  const viewMap = {
    'dashboard': 'viewDashboard',
    'reports': 'viewReports',
    'applicators': 'viewApplicators',
    'customers': 'viewCustomers'
  };
  
  const viewId = viewMap[viewName];
  if (viewId) {
    document.getElementById(viewId).classList.add('active');
  }
  
  // Set active nav item
  event.target.classList.add('active');
}