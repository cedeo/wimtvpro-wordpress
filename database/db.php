<?php
/**
 * Written by walter at 11/11/13
 */
global $wpdb;

function initDatabase() {
    if (!empty ($wpdb->charset))
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    if (!empty ($wpdb->collate))
        $charset_collate .= " COLLATE {$wpdb->collate}";
}