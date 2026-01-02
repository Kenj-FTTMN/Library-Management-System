<?php
/**
 * Breadcrumbs Component
 * Usage: include this file and set $breadcrumbs array before including
 * Example: 
 * $breadcrumbs = [
 *     ['title' => 'Home', 'url' => 'index.php'],
 *     ['title' => 'About', 'url' => 'about.php'],
 *     ['title' => 'Current Page', 'url' => '']
 * ];
 */
if (!isset($breadcrumbs)) {
    $breadcrumbs = [];
}
?>

<?php if (!empty($breadcrumbs)): ?>
<div class="page-title light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0"><?php echo end($breadcrumbs)['title']; ?></h1>
    <nav class="breadcrumbs">
      <ol>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
          <?php if ($index === count($breadcrumbs) - 1): ?>
            <li class="current"><?php echo $crumb['title']; ?></li>
          <?php else: ?>
            <li><a href="<?php echo $crumb['url']; ?>"><?php echo $crumb['title']; ?></a></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ol>
    </nav>
  </div>
</div>
<?php endif; ?>

