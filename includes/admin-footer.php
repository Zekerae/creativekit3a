<?php
/**
 * includes/admin-footer.php — Closes the staff-portal shell opened by
 * includes/admin-layout.php, then loads the quote modal and scripts.
 *
 * Optional variable:
 *   $extra_js (array) — page-specific scripts, loaded after the shared ones
 */
$extra_js = $extra_js ?? [];
?>
    </div><!-- /.admin-content -->

    <footer class="admin-footer">
      <span>&copy; <?= date('Y') ?> CreativeKit3A Staff Portal</span>
      <span>Database: <strong>creativekit3a_db</strong></span>
    </footer>
  </div><!-- /.admin-main -->
</div><!-- /.admin-shell -->

<!-- Overlay used by the Request a Quote modal (Quotes → Submit New Inquiry) -->
<div class="lm-overlay" id="lm-overlay" onclick="closeAllModals()" aria-hidden="true"></div>
<?php include __DIR__ . '/quote-modal.php'; ?>

<script src="<?= htmlspecialchars(asset('/assets/js/global.js')) ?>"></script>
<script src="<?= htmlspecialchars(asset('/assets/js/quote-modal.js')) ?>"></script>
<script src="<?= htmlspecialchars(asset('/assets/js/admin.js')) ?>"></script>
<?php foreach ($extra_js as $js): ?>
  <script src="<?= htmlspecialchars(asset($js)) ?>"></script>
<?php endforeach; ?>

</body>
</html>
