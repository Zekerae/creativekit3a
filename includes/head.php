<?php
/**
 * includes/head.php — Shared <head> partial
 * Dynamically loads global design tokens and per-page stylesheets.
 */

require_once __DIR__ . '/../config/app.php';   // BASE_URL + url() helper

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$title     = $title     ?? $page_title ?? 'CreativeKit3A | Corporate Giveaways Philippines';
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

  <!-- Google Fonts & Font Awesome 6 -->
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">

  <!-- Shared base: admin utilities (alerts, badges, modals, tooltips) -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/global.css">
  <!-- CreativeKit3A Main UI: top bar, header, mega menu, hero, cards, footer, auth modals -->
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/quote-modal.css">

  <!-- Dedicated Per-Page Stylesheet(s) -->
  <?php foreach ($extra_css as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars(url($css)) ?>">
  <?php endforeach; ?>
  <!-- Base path for JS-built URLs (site may live in a subfolder, e.g. /creativekit3a) -->
  <script>window.BASE_URL = <?= json_encode(BASE_URL) ?>;</script>
</head>
