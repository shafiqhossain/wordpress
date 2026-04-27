-- ================================================================
-- Maxstamp Subscriber List Plugin v3.0  –  Database Schema
-- Author:  Shafiq Hossain  <md.shafiq.hossain@google.com>
-- Website: https://www.isoftbd.com
-- ================================================================
-- Replace wp_ with your WordPress table prefix.
-- The plugin runs this automatically via dbDelta() on activation.
-- ================================================================

-- One record per person
CREATE TABLE IF NOT EXISTS `wp_maxstamp_subscribers` (
    `id`            BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `wp_user_id`    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0
                        COMMENT 'Linked WordPress user ID (0 = not linked)',
    `name`          VARCHAR(100) NOT NULL,
    `email`         VARCHAR(100) NOT NULL,
    `phone`         VARCHAR(30)  NOT NULL DEFAULT '',
    `extra`         VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Optional notes',
    `registered_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_email`   (`email`),
    KEY       `idx_wp_user` (`wp_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='One row per subscriber (person)';


-- Many subscriptions per subscriber
CREATE TABLE IF NOT EXISTS `wp_maxstamp_subscriptions` (
    `id`                BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `subscriber_id`     BIGINT(20) UNSIGNED NOT NULL
                            COMMENT 'FK → wp_maxstamp_subscribers.id',
    `subscription_type` VARCHAR(50)  NOT NULL DEFAULT 'Free'
                            COMMENT 'Free | Popular | Gold | Silver | Platinum',
    `status`            ENUM('Active','Suspended','Cancelled','Expired')
                            NOT NULL DEFAULT 'Active',
    `price_paid`        DECIMAL(10,2) NOT NULL DEFAULT 0.00
                            COMMENT 'Amount paid at time of subscription',
    `registered_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
                            COMMENT 'When this subscription record was created',
    `start_date`        DATE     NOT NULL
                            COMMENT 'Subscription start date',
    `expire_date`       DATE     NOT NULL
                            COMMENT 'Subscription expiry date (start + 365 days by default)',
    `notes`             VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `idx_subscriber`   (`subscriber_id`),
    KEY `idx_status`       (`status`),
    KEY `idx_type`         (`subscription_type`),
    KEY `idx_expire`       (`expire_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='One subscriber can have many subscriptions';


-- ── Relationship overview ─────────────────────────────────────────────────────
--
--  wp_users  (WordPress core)
--      │  1
--      │  ┆  wp_user_id
--      │  N
--  wp_maxstamp_subscribers
--      │  1
--      │  ┆  subscriber_id
--      │  N
--  wp_maxstamp_subscriptions
--
-- ── Sample data ───────────────────────────────────────────────────────────────
-- INSERT INTO `wp_maxstamp_subscribers` (wp_user_id, name, email, phone) VALUES
--     (1, 'Shafiq Hossain', 'shafiq@isoftbd.com', '+880 1700 000001'),
--     (0, 'Alice Rahman',   'alice@example.com',  '+880 1700 000002');
--
-- INSERT INTO `wp_maxstamp_subscriptions`
--     (subscriber_id, subscription_type, status, price_paid, start_date, expire_date) VALUES
--     (1, 'Platinum', 'Active',    49.99, '2025-01-01', '2026-01-01'),
--     (1, 'Gold',     'Expired',   29.99, '2024-01-01', '2025-01-01'),
--     (2, 'Free',     'Active',     0.00, '2025-06-01', '2026-06-01');
