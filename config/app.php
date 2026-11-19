<?php
/**
 * config/app.php — Base URL detection & URL helper
 *
 * Lets the site run from any folder, e.g.
 *   http://localhost/creativekit3a/   → BASE_URL = '/creativekit3a'
 *   http://localhost/ (project is the web root) → BASE_URL = ''
 *
 * Internal paths are written root-relative ('/assets/css/global.css',
 * '/pages/products.php', DB image paths like '/assets/images/1.png')
 * and passed through url() when printed, so they always resolve.
 */

if (!defined('BASE_URL')) {
    $ck_app_root = realpath(__DIR__ . '/..');
    $ck_doc_root = !empty($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $ck_base     = '';

    if ($ck_app_root && $ck_doc_root && strpos($ck_app_root, $ck_doc_root) === 0) {
        $ck_base = str_replace('\\', '/', substr($ck_app_root, strlen($ck_doc_root)));
        $ck_base = rtrim($ck_base, '/');
        if ($ck_base !== '' && $ck_base[0] !== '/') {
            $ck_base = '/' . $ck_base;
        }
    }

    define('BASE_URL', $ck_base);
    unset($ck_app_root, $ck_doc_root, $ck_base);
}

if (!function_exists('product_url')) {
    /**
     * Storefront link for a product: its items/variants page when it has
     * variants, otherwise its category page. Returned root-relative (wrap in url()).
     */
    function product_url(string $cat_slug, string $sub_slug, string $product_slug, int $variant_count): string {
        if ($variant_count > 0) {
            return '/product/car-charger-adapters.php?' . http_build_query([
                'cat' => $cat_slug, 'subcat' => $sub_slug, 'item' => $product_slug,
            ]);
        }
        return '/category-page.php?cat=' . urlencode($cat_slug);
    }
}

if (!function_exists('safe_return_to')) {
    /**
     * Validate a "return to" URL from a form: only same-site paths under
     * BASE_URL are allowed (prevents open redirects). Falls back to the homepage.
     */
    function safe_return_to(?string $target): string {
        $target = (string)$target;
        $prefix = BASE_URL . '/';
        if ($target !== '' && strpos($target, $prefix) === 0 && strpos($target, '//') === false
            && !preg_match('/[\r\n]/', $target) && strpos($target, '/auth/') === false) {
            return $target;
        }
        return $prefix;
    }
}

if (!function_exists('url')) {
    /**
     * Prefix a root-relative path with BASE_URL.
     * External URLs (https://…, //cdn…), anchors and empty strings are returned unchanged.
     */
    function url(?string $path): string {
        $path = (string)$path;
        if ($path === '' || $path[0] !== '/' || (isset($path[1]) && $path[1] === '/')) {
            return $path;
        }
        return BASE_URL . $path;
    }
}
