<?php
// Include config if not already included
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}

// Include auth if not already included
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../config/auth.php';
}

// Get current page
$current_page = get_current_page();
$page_config = get_page_config($current_page);

// Set active menu items (pass by reference to modify)
if (isset($nav_menu)) {
    set_active_menu($nav_menu, $current_page);
}

// Get user data if logged in
$user_data = isLoggedIn() ? getUserData() : [];
?>

<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-end">

    <?php
    // Determine home URL based on role
    $home_url = 'index.php';
    if (isLoggedIn()) {
        $role = getCurrentRole();
        if ($role === 'admin') {
            $home_url = 'index.php?page=admin-dashboard';
        } elseif ($role === 'faculty') {
            $home_url = 'index.php?page=faculty-dashboard';
        } elseif ($role === 'student') {
            $home_url = 'index.php?page=student-dashboard';
        }
    }
    ?>
    <a href="<?php echo $home_url; ?>" class="logo d-flex align-items-center me-auto">
      <!-- Uncomment the line below if you also wish to use an image logo -->
      <!-- <img src="<?php echo ASSETS_PATH; ?>/img/logo.webp" alt=""> -->
      <h1 class="sitename"><?php echo SITE_NAME; ?></h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <?php if (isLoggedIn()): ?>
          <?php foreach ($nav_menu as $item): ?>
            <?php 
            // Check if item should be shown based on role
            $show_item = true;
            if (isset($item['require_role'])) {
                $show_item = hasRole($item['require_role']);
            }
            if (isset($item['require_admin']) && $item['require_admin']) {
                $show_item = isAdmin();
            }
            
            // Update home URL for role-specific dashboard
            $item_url = $item['url'];
            if ($item['title'] === 'Home' && isLoggedIn()) {
                $role = getCurrentRole();
                if ($role === 'admin') {
                    $item_url = 'index.php?page=admin-dashboard';
                } elseif ($role === 'faculty') {
                    $item_url = 'index.php?page=faculty-dashboard';
                } elseif ($role === 'student') {
                    $item_url = 'index.php?page=student-dashboard';
                }
            }
            ?>
            <?php if ($show_item): ?>
              <li class="<?php echo isset($item['dropdown']) ? 'dropdown' : ''; ?>">
                <a href="<?php echo $item_url; ?>" class="<?php echo $item['active'] ? 'active' : ''; ?>">
                  <span><?php echo $item['title']; ?></span>
                  <?php if (isset($item['dropdown'])): ?>
                    <i class="bi bi-chevron-down toggle-dropdown"></i>
                  <?php endif; ?>
                </a>
                <?php if (isset($item['dropdown'])): ?>
                  <ul>
                    <?php foreach ($item['dropdown'] as $subitem): ?>
                      <?php 
                      $show_subitem = true;
                      if (isset($subitem['require_role'])) {
                          $show_subitem = hasRole($subitem['require_role']);
                      }
                      if (isset($subitem['require_admin']) && $subitem['require_admin']) {
                          $show_subitem = isAdmin();
                      }
                      ?>
                      <?php if ($show_subitem): ?>
                        <li><a href="<?php echo $subitem['url']; ?>" class="<?php echo isset($subitem['active']) && $subitem['active'] ? 'active' : ''; ?>"><?php echo $subitem['title']; ?></a></li>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
          <li class="dropdown">
            <a href="#" class="d-flex align-items-center">
              <i class="bi bi-person-circle me-2"></i>
              <span><?php echo htmlspecialchars(($user_data['first_name'] ?? '') . ' ' . ($user_data['last_name'] ?? 'User')); ?></span>
              <i class="bi bi-chevron-down toggle-dropdown ms-2"></i>
            </a>
            <ul>
              <li><a href="#"><i class="bi bi-person me-2"></i> <?php echo ucfirst(getCurrentRole()); ?></a></li>
              <li><a href="index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li><a href="index.php?page=login">Login</a></li>
        <?php endif; ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

  </div>
</header>

