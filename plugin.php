<?php
/*
Plugin Name: Require Protocol
Plugin URI: https://github.com/julabo/yourls_require_protocol
Description: Advanced validation for original URLs in YOURLS. Enforces a protocol, optionally allows only HTTPS, and can automatically fix protocols.
Version: 1.0.2
Author: Jan Leehr
Author URI: https://julabo.com
*/

/**
 * CONFIGURATION
 * ======================================
 */

// Allow only HTTPS? (true/false)
if (!defined('REQP_REQUIRE_HTTPS'))   define('REQP_REQUIRE_HTTPS', false);

// Automatically upgrade http:// → https:// ? (true/false)
if (!defined('REQP_UPGRADE_TO_HTTPS')) define('REQP_UPGRADE_TO_HTTPS', false);

// If no protocol is present: automatically prepend http:// ? (true/false)
if (!defined('REQP_AUTO_ADD_HTTP'))   define('REQP_AUTO_ADD_HTTP', false);

// If no protocol is present: automatically prepend https:// ? (true/false)
// When both REQP_AUTO_ADD_HTTPS and REQP_AUTO_ADD_HTTP are true, https:// takes precedence
if (!defined('REQP_AUTO_ADD_HTTPS'))  define('REQP_AUTO_ADD_HTTPS', false);




/**
 * Validate original URL
 * ======================================
 */

yourls_add_filter('shunt_add_new_link', 'reqp_validate_original_url');
// Normalize URL during sanitization (so the rest of YOURLS uses the final URL)
yourls_add_filter('sanitize_url', 'reqp_filter_sanitize_url', 10, 2);

function reqp_validate_original_url($false, $url, $keyword = '', $title = '') {

    $url = trim($url);

    // If no protocol and we don't auto-add, error early for clarity
    if (!preg_match('#^[a-z]+://#i', $url)) {
        if (!(REQP_AUTO_ADD_HTTP || REQP_AUTO_ADD_HTTPS)) {
            return reqp_error("Please enter a URL WITH a protocol (http:// or https://).");
        }
        // else, it will be added during sanitize phase
    }

    // Compute what the URL would look like after our normalization
    $normalized = reqp_normalize_url($url);

    // Enforce HTTPS only: if after normalization it's not https://, return an error
    if (REQP_REQUIRE_HTTPS && !preg_match('#^https://#i', $normalized)) {
        return reqp_error("Only URLs with https:// are allowed.");
    }

    return false; // allow YOURLS to continue
}

/**
 * Hook into YOURLS sanitization to actually normalize the URL used downstream
 */
function reqp_filter_sanitize_url($url, $unsafe_url) {
    // Do not normalize relative URLs
    $candidate = trim((string)$unsafe_url);
    if ($candidate !== '' && preg_match('/^[a-z0-9][a-z0-9_-]{0,198}$/i', $candidate)) {
        return $url;
    }

    return reqp_normalize_url($url);
}

/**
 * Normalize URL according to plugin settings (no side effects)
 */
function reqp_normalize_url($url) {
    $normalized = $url;

    // Add http:// if missing and configured to do so
    if (!preg_match('#^[a-z]+://#i', $normalized)) {
        if (REQP_AUTO_ADD_HTTPS) {
            // Prefer https if both auto-add flags are enabled
            $normalized = 'https://' . $normalized;
        } elseif (REQP_AUTO_ADD_HTTP) {
            $normalized = 'http://' . $normalized;
        }
    }

    // Upgrade to https:// if configured
    if (REQP_UPGRADE_TO_HTTPS && preg_match('#^http://#i', $normalized)) {
        $normalized = preg_replace('#^http://#i', 'https://', $normalized);
    }

    return $normalized;
}


/**
 * Uniform error builder
 */
function reqp_error($msg) {
    return array(
        'status'   => 'fail',
        'code'     => 'error:invalid_protocol',
        'message'  => $msg,
        'errorCode'=> 'invalid_protocol',
    );
}
