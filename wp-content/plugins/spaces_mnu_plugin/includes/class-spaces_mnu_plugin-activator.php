<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Spaces_mnu_plugin
 * @subpackage Spaces_mnu_plugin/includes
 */

class Spaces_mnu_plugin_Activator
{
    /**
     * Method called during plugin activation.
     *
     * @since 1.0.0
     */
    public static function activate()
    {
        // Define constant to prevent action hooks during activation.
        if (!defined('SPACES_MNU_PLUGIN_ACTIVATING')) {
            define('SPACES_MNU_PLUGIN_ACTIVATING', true);
        }
        self::create_days_table();
        self::create_bookings_table();
        // self::create_cancellations_table();

        flush_rewrite_rules();
    }
    private static function create_days_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_days = $wpdb->prefix . 'spaces_days';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_days'") != $table_days) {
            $sql_days = "CREATE TABLE $table_days (
            ID BIGINT(20) NOT NULL AUTO_INCREMENT,
            ResourceID BIGINT(20) NOT NULL,
            Day DATE NOT NULL,
            SlotsAvailable JSON NOT NULL,
            CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
            ModifiedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (ID),
            UNIQUE KEY (ResourceID, Day)
        ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql_days);
        }
    }
    private static function create_bookings_table()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_bookings = $wpdb->prefix . 'spaces_bookings';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_bookings'") != $table_bookings) {
            $sql_bookings = "CREATE TABLE $table_bookings (
        ID BIGINT(20) NOT NULL AUTO_INCREMENT,
        DayID BIGINT(20) NOT NULL,
        BookingDate DATE NOT NULL,
        SlotStart TIME NOT NULL,
        SlotEnd TIME NOT NULL,
        ResourceID BIGINT(20) NOT NULL,
        ResourceName TEXT,
        EventID BIGINT(20) NOT NULL,
        RequestedBy BIGINT(20) NOT NULL,
        Status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
        CancelledBy BIGINT(20),
        CancelledAt DATETIME,
        RequestReason TEXT,
        CancelComment TEXT,
        Priority BOOLEAN DEFAULT FALSE,
        CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        ModifiedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        IsDeleted BOOLEAN DEFAULT FALSE,
        PRIMARY KEY (ID),
        INDEX idx_day_id (DayID),
        INDEX idx_resource_id (ResourceID),
        INDEX idx_event_id (EventID),
        INDEX idx_requested_by (RequestedBy),
        INDEX idx_status (Status),
        FOREIGN KEY (DayID) REFERENCES {$wpdb->prefix}spaces_days(ID) ON DELETE CASCADE
    ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql_bookings);
        }
    }
}
