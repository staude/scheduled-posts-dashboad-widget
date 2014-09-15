<?php

/*  Copyright 2014  Frank Staude  <frank@staude.net>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class scheduled_posts_dashboard_widget {

    /**
     * 
     */
    function __construct() {
        add_action( 'wp_dashboard_setup', array( 'scheduled_posts_dashboard_widget', 'registerWidget' ) );
        add_action( 'plugins_loaded',     array( 'scheduled_posts_dashboard_widget', 'load_translations' ) );

        if ( version_compare( get_bloginfo( 'version' ), '3.8') >= 0 ) {
            // Dashboard at the glance, since WordPress 3.8
            add_action( 'dashboard_glance_items', array( 'scheduled_posts_dashboard_widget', 'dashboard_glance_items' ) );
        }
    }

    /**
     * 
     */
    static public function load_translations() {
        load_plugin_textdomain( 'scheduled-posts-dashboard-widget', false, dirname( plugin_basename( __FILE__ )) . '/languages/'  );
    }
    
    /**
     * 
     */
    static public function registerWidget () {
        wp_enqueue_script( 'tablesorter', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'tablesorterpager', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.pager.js', array( 'tablesorter' ) );
        wp_enqueue_style( 'tablesorter', plugin_dir_url( __FILE__) . 'css/jquery.tablesorter.pager.css' );
        wp_enqueue_style( 'scheduled-posts-tablesorter', plugin_dir_url( __FILE__) . 'css/scheduled-posts.css' );

        wp_add_dashboard_widget( 'scheduled_posts_dashboard_widget',
                                 apply_filters ( 'scheduled_posts_dashboard_widget_title', __( 'Scheduled Posts', 'scheduled-posts-dashboard-widget' ) ),
                                 array( 'scheduled_posts_dashboard_widget',
                                        'scheduled_posts_widget' )
                               );
        
    }
    
    /**
     * 
     */
    static public function dashboard_glance_items() {
        $query = new WP_Query( array(
                 'post_type' => 'post', 
                 'post_status' => 'future',
                 'posts_per_page' => 9999,
                 'orderby' => 'date',
                 'order' => 'ASC'
         ) );

        echo '<li class="page-count scheduledposts">';
        echo '<a  href="edit.php?post_status=future">' . sprintf(__('%s Scheduled posts', 'scheduled-posts-dashboard-widget'), $query->post_count ) . '</a>';
        echo '</li>';
    }
    
    /**
     * 
     */
    static public function scheduled_posts_widget() {
        $query = new WP_Query( array(
			'post_type' => 'post', 
			'post_status' => 'future',
			'posts_per_page' => 9999,
			'orderby' => 'date',
			'order' => 'ASC'
		) );
        $posts = $query->posts;
        
        if ( $posts && is_array( $posts ) ) {
            $list = array();

            ?>
<script>
    jQuery(document).ready(function() {
		jQuery.tablesorter.addParser({
		     // set a unique id
		     id: 'date',
		     is: function(s) {
		       return false;
		     },
		     format: function(s) {
		       // format your data for normalization
		       var search = /([0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9])/i;
		       var matches = search.exec(s);
		       return matches[0];
		     },
		     type: 'numeric'
		   }); // end $.tablesorter.addParser({        
        
        
        jQuery("#dashboard_scheduled_posts")
        .tablesorter({widthFixed: true, sortList: [[1,0]], widgets: ['zebra']  })
        .tablesorterPager( { container: jQuery( "#dashboard_scheduled_posts_pager" ), positionFixed: false } );
    });
</script>
<table class="wp-list-table widefat fixed tablesorter" colspan="0" id="dashboard_scheduled_posts"><thead>
        <tr>
            <th id="title" class="manage-column column-title sortable desc" style="" scope="col"><?php _e('Title', 'scheduled-posts-dashboard-widget'); ?></th>
            <th id="date" class="manage-column column-title sortable desc" style="" scope="col"><?php _e('Date', 'scheduled-posts-dashboard-widget'); ?></th>
        </tr>
       
    </thead><tbody>
<?php
            foreach ( $posts as $post ) {
                $url = get_edit_post_link( $post->ID );
                $title = _draft_or_post_title( $post->ID );
                $time = get_the_time( get_option( 'date_format' ), $post );
                $sorttime = get_the_time( 'Ymd', $post );
    echo "<tr><td><a title='" . sprintf( __( 'Edit "%s"', 'scheduled-posts-dashboard-widget' ), esc_attr( $title ) ) . "' href='" . $url . "'>{$title}</a></td><td><div style=\"display: none\">{$sorttime}</div>$time</td></tr>";
                }
?>
    </tbody>
</table>

<div id="dashboard_scheduled_posts_pager" class="pager" >
	<form>
            <div class="dashicons dashicons-arrow-left-alt first scheduledpost" data-code="f340"></div>
            <div class="dashicons dashicons-arrow-left-alt2 prev scheduledpost" data-code="f341"></div>
            <input type="text" class="pagedisplay scheduledpost"/>
            <div class="dashicons dashicons-arrow-right-alt2 next scheduledpost" data-code="f345"></div>
            <div class="dashicons dashicons-arrow-right-alt last scheduledpost" data-code="f344"></div>
            <select class="pagesize scheduledpost">
		<option value="5">5</option>
		<option selected="selected"  value="10">10</option>
		<option value="25">25</option>
		<option  value="50">50</option>
            </select>
	</form>
</div>

	<p class="textright"><a href="edit.php?post_status=future" class="button"><?php _e( 'View all', 'scheduled-posts-dashboard-widget' ); ?></a></p>
<?php
		} else {
			_e( 'There are no scheduled posts at the moment', 'scheduled-posts-dashboard-widget' );
		}
	}
    
}

