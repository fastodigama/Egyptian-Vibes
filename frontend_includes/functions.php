<?php

// This function checks if the current page matches the given filename.
// If it does, it returns the string "active" to apply the CSS class for highlighting the active menu item.
// Used in navigation links to dynamically style the current page's link.
function isActive($page) {
  return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
}


?>