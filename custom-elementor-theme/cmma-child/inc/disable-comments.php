<?php
if (!class_exists('CmmaDisableComments')) {
    class CmmaDisableComments {

        public function __construct() {
            add_action('pre_comment_on_post', [$this, 'disable_comment_submission']);
            add_action('admin_init', [$this, 'disable_comment_admin_features']);
            add_filter('comments_open', '__return_false', 20, 2);
            add_filter('pings_open', '__return_false', 20, 2);
            add_filter('comments_array', '__return_empty_array', 10, 2);
            add_action('admin_menu', [$this, 'remove_comments_menu']);
            add_action('init', [$this, 'remove_comments_from_admin_bar']);
        }

        // Disable comment submission and show error message
        public function disable_comment_submission() {
            wp_die(__('Comments are disabled on this site.', 'your-textdomain'), __('Comments Disabled', 'your-textdomain'), [
                'back_link' => true,
            ]);
        }

        // Disable all comment-related features in admin
        public function disable_comment_admin_features() {
            global $pagenow;

            // Redirect users trying to access the comments admin page
            if ($pagenow === 'edit-comments.php') {
                wp_redirect(admin_url());
                exit;
            }

            // Remove comments metabox from dashboard
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

            // Disable support for comments and trackbacks for all post types
            foreach (get_post_types() as $post_type) {
                if (post_type_supports($post_type, 'comments')) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
        }

        // Remove comments page from admin menu
        public function remove_comments_menu() {
            remove_menu_page('edit-comments.php');
        }

        // Remove comments link from the admin bar
        public function remove_comments_from_admin_bar() {
            if (is_admin_bar_showing()) {
                remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
            }
        }
    }

    // Initialize the class
    new CmmaDisableComments();
}
