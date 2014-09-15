<?php
/*
Plugin Name: Scheduled Posts Dashboard Widget
Plugin URI: http://staude.net/wordpress/plugins/ScheduledPostsDashboardWidget
Description: Implements a Widget to show the Wordpress scheduled posts in the Dashboard
Text Domain: scheduled-posts-dashboard-widget
Domain Path: languages
Author: Frank Staude
Version: 0.3
Author URI: http://www.staude.net/
Compatibility: WordPress 4.0
License: GPLv2
*/

if (!class_exists( 'scheduled_posts_dashboard_widget' ) ) {

    include_once dirname( __FILE__ ) .'/class-scheduled-posts-dashboard-widget.php';

    $scheduled_posts_dashboard_widget = new scheduled_posts_dashboard_widget();

}