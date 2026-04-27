# Maxstamp Subscriber List Plugin v3.0

**Author:** Shafiq Hossain  
**Email:** md.shafiq.hossain@google.com  
**Website:** https://www.isoftbd.com  

---

## What's New in v3.0

- **Separate URL routing** – no shortcodes; every page has its own clean URL
- **Public subscribe page** with interactive pricing cards at `/subscribe`
- **Settings page** with per-plan pricing, currency, duration, and notification controls
- **Multi-subscription support** – one subscriber can have many subscriptions
- **Subscription statuses** – Active / Suspended / Cancelled / Expired (auto-expires daily)
- **WordPress user auto-creation** – a WP account is created (and credentials emailed) on first subscription
- **Dashboard** with KPI counts by status and plan
- **Quick status change** buttons on list pages

---

## File Structure

```
maxstamp-subscriber-plugin/
├── maxstamp-subscriber.php          ← Main plugin file
├── create_tables.sql                ← SQL reference
├── README.md
│
├── include/
│   ├── maxstamp-admin.php           ← All admin page handlers
│   └── maxstamp-public.php          ← Public form POST handler
│
├── library/
│   ├── Helper.php                   ← Utilities, WP user creation, flash messages
│   ├── Subscriber.php               ← Subscriber CRUD model
│   ├── Subscription.php             ← Subscription CRUD model
│   └── Settings.php                 ← Plugin settings & pricing
│
├── pages/admin/
│   ├── dashboard.php
│   ├── subscriber-list.php
│   ├── subscriber-form.php          ← Add + Edit (shared)
│   ├── subscriber-view.php
│   ├── subscription-list.php
│   ├── subscription-form.php        ← Add + Edit (shared)
│   └── settings.php
│
├── templates/
│   ├── wrapper.php                  ← Injects into theme (get_header/get_footer)
│   ├── subscribe.php                ← Public sign-up page with pricing cards
│   ├── subscribe-success.php
│   ├── subscriber-list.php          ← Public list
│   └── subscriber-view.php          ← Public profile view
│
└── style/
    ├── maxstamp_admin.css
    ├── maxstamp_admin.js
    └── maxstamp_public.css
```

---

## Public URLs

| URL | Description |
|---|---|
| `/subscribe` | Public sign-up page with pricing cards |
| `/subscribe?plan=Gold` | Pre-select a plan |
| `/subscribe/success` | Thank-you page after sign-up |
| `/subscribers` | Public subscriber list (can be disabled) |
| `/subscribers/{id}` | Individual subscriber profile |

---

## Admin Menu

| Menu Item | Description |
|---|---|
| Dashboard | KPI counts, quick links |
| All Subscribers | Searchable, filterable list |
| Add Subscriber | Create subscriber + initial subscription |
| All Subscriptions | Full subscription list with status filter |
| Add Subscription | Add extra subscription to existing subscriber |
| Settings | Pricing, currency, notifications, duration |

---

## Database Tables

### `{prefix}maxstamp_subscribers`
One row per person.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| wp_user_id | BIGINT | Linked WP user (auto-created) |
| name | VARCHAR(100) | |
| email | VARCHAR(100) UNIQUE | |
| phone | VARCHAR(30) | |
| extra | VARCHAR(255) | Notes |
| registered_at | DATETIME | Auto-set |

### `{prefix}maxstamp_subscriptions`
Many subscriptions per subscriber.

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| subscriber_id | BIGINT | FK → subscribers |
| subscription_type | VARCHAR | Free/Popular/Gold/Silver/Platinum |
| status | ENUM | Active/Suspended/Cancelled/Expired |
| price_paid | DECIMAL(10,2) | Price at time of purchase |
| registered_at | DATETIME | Record creation time |
| start_date | DATE | |
| expire_date | DATE | Defaults to start + 365 days |
| notes | VARCHAR(255) | |

---

## Installation

1. Upload folder to `/wp-content/plugins/`
2. Activate in **Plugins → Installed Plugins**
3. Tables are created automatically; permalink rules are flushed
4. Go to **Subscribers → Settings** to configure pricing
5. Visit `/subscribe` to test the public form

> **Important:** After activation, go to **Settings → Permalinks** in WP admin and click **Save Changes** once to ensure the custom rewrite rules are registered.

---

## License

GPL2 — https://www.gnu.org/licenses/gpl-2.0.html
