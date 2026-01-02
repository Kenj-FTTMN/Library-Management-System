<?php
// Include config if not already included
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}

// Get current page
$current_page = get_current_page();
$page_config = get_page_config($current_page);

// Set active menu items (pass by reference to modify)
if (isset($nav_menu)) {
    set_active_menu($nav_menu, $current_page);
}
?>

<header id="header" class="header d-flex align-items-center sticky-top">
  <div class="header-container container-fluid container-xl position-relative d-flex align-items-center justify-content-end">

    <a href="index.php" class="logo d-flex align-items-center me-auto">
      <!-- Uncomment the line below if you also wish to use an image logo -->
      <!-- <img src="<?php echo ASSETS_PATH; ?>/img/logo.webp" alt=""> -->
      <h1 class="sitename"><?php echo SITE_NAME; ?></h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <?php foreach ($nav_menu as $item): ?>
          <li class="<?php echo isset($item['dropdown']) ? 'dropdown' : ''; ?>">
            <a href="<?php echo $item['url']; ?>" class="<?php echo $item['active'] ? 'active' : ''; ?>">
              <span><?php echo $item['title']; ?></span>
              <?php if (isset($item['dropdown'])): ?>
                <i class="bi bi-chevron-down toggle-dropdown"></i>
              <?php endif; ?>
            </a>
            <?php if (isset($item['dropdown'])): ?>
              <ul>
                <?php foreach ($item['dropdown'] as $subitem): ?>
                  <li><a href="<?php echo $subitem['url']; ?>" class="<?php echo isset($subitem['active']) && $subitem['active'] ? 'active' : ''; ?>"><?php echo $subitem['title']; ?></a></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

  </div>
</header>

