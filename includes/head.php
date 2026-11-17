<?php
/**
 * includes/head.php
 * ─────────────────────────────────────────────────────────────
 * Shared <head> partial. Include at the very top of every page.
 *
 * Variables to set BEFORE including this file:
 *   $title      (string) — browser tab title, e.g. "Accessories & Gadgets | CreativeKit3A"
 *   $meta_desc  (string) — meta description for SEO (optional)
 *   $extra_css  (array)  — additional stylesheet hrefs (optional)
 *                          e.g. ['/product/car-charger-adapters.css']
 *
 * Usage example:
 *   <?php
 *   $title     = 'My Page | CreativeKit3A';
 *   $meta_desc = 'Browse our products...';
 *   $extra_css = ['/product/car-charger-adapters.css'];
 *   include __DIR__ . '/includes/head.php';
 *   ?>
 * ─────────────────────────────────────────────────────────────
 */

// Defaults
$title     = $title     ?? 'CreativeKit3A | Corporate Giveaways Philippines';
$meta_desc = $meta_desc ?? 'Customized corporate giveaways, branded accessories, and promotional items in the Philippines. CreativeKit3A — your trusted partner.';
$extra_css = $extra_css ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
  <title><?= htmlspecialchars($title) ?></title>

  <!-- Font Awesome -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

  <!-- Shared site stylesheets -->
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/quote-modal.css">

  <?php foreach ($extra_css as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
  <?php endforeach; ?>
</head>
