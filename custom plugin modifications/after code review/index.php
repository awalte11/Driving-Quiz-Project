<?php
/*
 * Plugin Name: HD Quiz Custom
 * Description: HD Quiz allows you to easily add an unlimited amount of Quizzes to your site.
 * Plugin URI: https://harmonicdesign.ca/hd-quiz/
 * Author: Harmonic Design
 * Author URI: https://harmonicdesign.ca
 * Version: 1.6.1
*/

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

if (!defined('HDQ_PLUGIN_VERSION')) {
    define('HDQ_PLUGIN_VERSION', '1.6.1');
}

// custom quiz image sizes
// add_image_size('hd_qu_size', 600, 400, true); // featured image - removed since version 1.5.0
add_image_size('hd_qu_size2', 400, 400, true); // image-as-answer

/* Include the basic required files
------------------------------------------------------- */
require dirname(__FILE__) . '/includes/post_type.php'; // custom post types
require dirname(__FILE__) . '/includes/meta.php'; // custom meta
require dirname(__FILE__) . '/includes/functions.php'; // general functions

// function to check if HD Quiz is active
function hdq_exists()
{
    return;
}

/* Enqueue admin scripts to relevant pages
------------------------------------------------------- */
function hdq_add_admin_scripts($hook)
{
    global $post;
    // Only enqueue if we're on the
    // add/edit questions, quizzes, or settings page
    if ($hook == 'post-new.php' || $hook == 'post.php' || $hook == 'term.php' || $hook == 'edit-tags.php' || $hook == "hd-quiz_page_hdq_options" || $hook == "toplevel_page_hdq_quizzes") {
        function hdq_print_scripts()
        {
            wp_enqueue_style(
                'hdq_admin_style',
                plugin_dir_url(__FILE__) . './includes/css/hdq_admin_style.css?v=' . HDQ_PLUGIN_VERSION
            );
            wp_enqueue_script(
                'hdq_admin_script',
                plugins_url('./includes/js/hdq_admin.js?v=' . HDQ_PLUGIN_VERSION, __FILE__),
                array('jquery', 'jquery-ui-sortable'),
                '1.0',
                true
            );
        }
        if ($hook == "hd-quiz_page_hdq_options" || $hook == "term.php" || $hook == 'edit-tags.php' || $hook == "toplevel_page_hdq_quizzes") {
            hdq_print_scripts();
        } else {
            if ($post->post_type === 'post_type_questionna') {
                hdq_print_scripts();
            }
        }
    }
}
add_action('admin_enqueue_scripts', 'hdq_add_admin_scripts', 10, 1);

/* Add shortcode
------------------------------------------------------- */
function hdq_add_shortcode($atts)
{
    // Attributes
    extract(
        shortcode_atts(
            array(
                'quiz' => '',
            ),
            $atts
        )
    );

    // Code
    ob_start();
    include plugin_dir_path(__FILE__) . './includes/template.php';
    return ob_get_clean();
}
add_shortcode('HDquiz', 'hdq_add_shortcode');

/* Disable Canonical redirection for paginated quizzes
------------------------------------------------------- */
function hdq_disable_redirect_canonical($redirect_url)
{
    global $post;
    if (has_shortcode($post->post_content, 'HDquiz')) {
        $redirect_url = false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'hdq_disable_redirect_canonical');

/* Create HD Quiz Settings page
------------------------------------------------------- */
function hdq_create_settings_page()
{
    if (hdq_user_permission()) {
        function hdq_register_quizzes_page()
        {
            add_menu_page("HD Quiz", "HD Quiz", 'publish_posts', "hdq_quizzes", 'hdq_register_quizzes_page_callback', "dashicons-clipboard", 5);
        }
        add_action('admin_menu', 'hdq_register_quizzes_page');

        function hdq_register_settings_page()
        {
            add_submenu_page('hdq_quizzes', 'Quizzes', 'Quizzes', 'publish_posts', 'hdq_quizzes', 'hdq_register_quizzes_page_callback');
            add_submenu_page('hdq_quizzes', 'HD Quiz About', 'About / Options', 'publish_posts', 'hdq_options', 'hdq_register_settings_page_callback');
            add_submenu_page('hdq_quizzes', 'Driving Quiz Admin', 'Admin', 'publish_posts', 'hdq_admin', 'hdq_custom_register_admin_page_callback');//adds driving quiz specific admin page - ARW
        }
        add_action('admin_menu', 'hdq_register_settings_page', 11);
    }

    $hdq_version = sanitize_text_field(get_option("HDQ_PLUGIN_VERSION"));
    if (HDQ_PLUGIN_VERSION != $hdq_version) {
        update_option("HDQ_PLUGIN_VERSION", HDQ_PLUGIN_VERSION);
        function hdq_show_upgrade_message()
        {
            ?>
			<div class="notice notice-success is-dismissible">
				<p><strong>HD QUIZ</strong>. Thank you for upgrading. If you experience any issues at all, please don't hesitate to <a href = "https://wordpress.org/support/plugin/hd-quiz" target = "_blank">reach out for support</a>! I'm always glad to help when I can.</p>
			</div>
			<?php
}
        add_action('admin_notices', 'hdq_show_upgrade_message');
    }
}
add_action('init', 'hdq_create_settings_page');
//-----------------------//
//New ARW code starts here

/* 
//This block runs the scripts that create the driving quiz site tables. -ARW
//Ideally it would be standard issue run on update code. but it uses deltadb() and I haven't figured out how to make that *not* break random things when left in active code.
include_once dirname( __FILE__ ) . '/includes/create_record_table.php';
register_activation_hook( __FILE__, create_quiz_records_table() );


register_activation_hook( __FILE__, create_parent_child_record_table() );
register_activation_hook( __FILE__, create_mta_flag_table() );
*/

function hdq_register_quizzes_page_callback()
{
    require dirname(__FILE__) . '/includes/hdq_quizzes.php';
}

function hdq_register_settings_page_callback()
{
    require dirname(__FILE__) . '/includes/hdq_about_options.php';
}

function hdq_custom_register_admin_page_callback()//calls the admin page php file - ARW
{
    require dirname(__FILE__) . '/includes/driving_quiz_admin_page.php';
}


function custom_quiz_shortcodes_init()// adds all the shortcodes used in the site - ARW
{
    function show_user_scores_sc($atts = []) //this shows the quiz scores for the user with the ID put in, or the current user otherwise
    {
        $atts = array_change_key_case((array)$atts, CASE_LOWER);
        
        $quiz_atts = shortcode_atts([
                                     'user_id' => get_current_user_id(),
                                 ], $atts);
 
 
        
        $shortcode = quiz_result_shortcode_maker($quiz_atts['user_id']);
        
        return $quiz_atts['user_id'] . "<br>" . do_shortcode( $shortcode);
    }
    add_shortcode('show_user_scores', 'show_user_scores_sc');
    
    function quiz_result_shortcode_maker($user_id )//this taks a user id and returns the function for the wp data access table for the shortcode to run.
    {
        
        //the wpda documentation *says* this should work and make the pretty version of the table. it does not work. it is recorded here in case future versions make it work.
        global $wpdb;
        $table_name = $wpdb->prefix . 'quiz_records_table';
        //$shortcode = '[wpdadiehard project_id="1" page_id="2" filter_field_name="user_id" filter_field_value="' . $user_id .'" ]';
        
        //THis one works but is all kinds of ugly
        $shortcode = '[wpdadiehard table_name="' . $table_name . '" filter_field_name="user_id" filter_field_value="' . $user_id.'" ]';
        return $shortcode;
    }
    
    function mta_view_display_sc()//displays the the government access view. Also blocks people without the flag from seeing it as a failsafe.
    {
        $user_id = get_current_user_id();
        $commands= "";
        global $wpdb;
        $table_name = $wpdb->prefix . 'mta_flag_table';
        $mva_flag = $wpdb->get_results( //look up current user ID in ma flag table
        	"
        	SELECT * 
        	FROM ". $table_name ."
        	WHERE user_id = ". $user_id
        );

        if (count($mva_flag ) >= 1) //if this has any members, then the flag matches the user
        {
           $commands = $commands . "MVA View \n See All User Scores \n";
           $commands = $commands . show_all_user_scores();
            
        }
        else $commands= $commands . "You should not be seeing this.";
    
        return $commands;
        
    }
    add_shortcode('mta_view_display', 'mta_view_display_sc');
    
    function show_all_user_scores()//Leftover testing code, useful for checking everything one.
    {
        $blogusers = get_users( );
        // Array of WP_User objects.
        $commands= "";
        foreach ( $blogusers as $user ) {
        	$commands = $commands . "<br>" . $user->display_name . do_shortcode(quiz_result_shortcode_maker($user->ID));
        
        
        }
        return $commands;
    }
    add_shortcode('show_all_user_scores', 'show_all_user_scores');
    
    function parent_view()//Does the parent view.
    {
        $user_id = get_current_user_id();//Gets current user - parent's - id
        global $wpdb;
        $table_name = $wpdb->prefix . 'parent_child_record_table';
        $children = $wpdb->get_results( //funds child IDs using child lookup table
        	"
        	SELECT child_user_id 
        	FROM ". $table_name ."
        	WHERE parent_user_id = ". $user_id
        );
        $commands = "";
        if (count($children) == 0)//checks for zero-kid case first. The foreeach works fine in this case, it's just more effiecent to do it this way
        {
            $commands = $commands. "No child records found.";
            
        }
        else {
            foreach ( $children as $child ) //does a foreach to display all kids 
            {
            	$commands = $commands . "<br>". do_shortcode(quiz_result_shortcode_maker($child->child_user_id));
            }
        }
        return $commands;
        
    }
    add_shortcode('parent_view', 'parent_view');
    
    
    //start of block that creates the child giving parent access form
    function c2p_form_code() {//this creates the  form. 
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Parent Email Address (required) <br />';
        echo '<input type="email" name="parent-email" value="' . ( isset( $_POST["parent-email"] ) ? esc_attr( $_POST["parent-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="c2p-submitted" value="Send"/></p>';
        echo '</form>';
        
    }
    
    function do_c2p()//this does the work of submission.
    {
         if ( isset( $_POST['c2p-submitted'] ) ) {
    
            // sanitize form values
            $parent_email   = sanitize_email( $_POST["parent-email"] );
            $child_id = get_current_user_id();
             
                
            $parent = get_user_by('email' , $parent_email);
            if ($parent == false)
            {
                echo "Parent not found";
            }
            else 
            {
                create_parent_child_link($parent->ID, $child_id);
                echo "Link created";
            }
            
        }
    }
    
    function child2parent_link_input_SC()//this ties them together for the shortcode to use.
    {
        
         ob_start();
        do_c2p();
        c2p_form_code();

        return ob_get_clean();
    }
    add_shortcode('child2parent_link_input', 'child2parent_link_input_SC');
    //end child parent creation link block.
    
    
    //start of block for child removing parent access
    //creates the form.
    function remove_c2p_form_code() {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Parent Email Address to Disconnect<br />';
        echo '<input type="email" name="parent-email" value="' . ( isset( $_POST["parent-email"] ) ? esc_attr( $_POST["parent-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="remove-c2p-submitted" value="Send"/></p>';
        echo '</form>';
    }
    
    //makes the form work
    function do_remove_c2p()
    {
         if ( isset( $_POST['remove-c2p-submitted'] ) ) {
    
            // sanitize form values
            $parent_email   = sanitize_email( $_POST["parent-email"] );
            $child_id = get_current_user_id();
             
                
            $parent = get_user_by('email' , $parent_email);
            if ($parent == false)
            {
                echo "Parent not found";
            }
            else 
            {
                remove_parent_child_link($parent->ID, $child_id);
                echo "Link removed";
            }
            
        }
    }
    
    //ties the above two together for the shortcode.
    function remove_child2parent_link_input_SC()
    {
        
         ob_start();
        do_remove_c2p();
        remove_c2p_form_code();

        return ob_get_clean();
    }
    add_shortcode('remove_child2parent_link_input', 'remove_child2parent_link_input_SC');
    //end block
    
    
    
    //start of block for showing parents with access to current user's data
    function getParentsWithAccess()//retrieves the parent user object array
    {
        $child_id = get_current_user_id();
        global $wpdb;
        $table_name = $wpdb->prefix . 'parent_child_record_table';
        $parent_IDs = $wpdb->get_results( //get the parent ID's.
        	"
        	SELECT parent_user_id 
        	FROM ". $table_name ."
        	WHERE child_user_id = ". $child_id
        );
        
        
        $parents = array();
        foreach ($parent_IDs as $parent_ID)//get the user objects. no special handling of the zero-parents case, unneeded, and better to let the callers handle it.
        {
           
            $parent = get_user_by('id', $parent_ID->parent_user_id);
            
            
            $parents[] = $parent;//adds to back of array as a reminder because WTF PHP why you append to arrays using = ?
        }
        return $parents;
    }
    function displayParentsWithAccess_SC()//displays the parents
    {
        $parents = getParentsWithAccess();
        
        //TODO if have time, collapsible version
        $out = '';
        if (count($parents) >= 1)
        {
            
            $out = $out .'<!-- wp:table --> <table class="wp-block-table"><tbody>';//creates wordpress table. i was tired of dealing with wpda not following its own documentation when trying to display specific parts of a table and displaying the whole row is right out when dealing with the WP user table, so i forced a stock wordpress table instead
           
            
            foreach ($parents as $parent )//make the table guts
            {
                $out = $out . '<tr>';
                $display = '';
                $display = $display . '<td>' . $parent->display_name . '</td> <td>' . $parent->user_email  . '</td>'; 
                
                $out = $out . $display;
                $out = $out . '</tr>';
            }
            
           
            
            $out = $out . '</tbody></table> <!-- /wp:table -->' . remove_child2parent_link_input_SC();//close table and append the remove link option.
          
        }
        else
        {
            $out = $out . 'You have not given anyone parent access.';
        }
        return $out;
    }
    add_shortcode('displayParentsWithAccess', 'displayParentsWithAccess_SC' );
    //end display parent block
    
    //start of block for parent giving up access
    //the form
    function remove_p2c_form_code() {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Child Email Address to Disconnect<br />';
        echo '<input type="email" name="child-email" value="' . ( isset( $_POST["child-email"] ) ? esc_attr( $_POST["child-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="remove-p2c-submitted" value="Send"/></p>';
        echo '</form>';
        
    }
    
    //does the work.
    function do_remove_p2c()
    {
         if ( isset( $_POST['remove-p2c-submitted'] ) ) {
    
            // sanitize form values
            $child_email   = sanitize_email( $_POST["child-email"] );
            $parent_id = get_current_user_id();
             
                
            $child = get_user_by('email' , $child_email);
            if ($child == false)
            {
                echo "Child not found";
            }
            else 
            {
                remove_parent_child_link($child->ID, $parent_id);
                echo "Link removed";
            }
            
        }
    }
    
    //turns the above into a shortcode.
    function remove_parent2child_link_input_SC()
    {
        
         ob_start();
        do_remove_p2c();
        remove_p2c_form_code();

        return ob_get_clean();
    }
    add_shortcode('remove_parent2child_link_input', 'remove_parent2child_link_input_SC');
    //end block
    
    //start block for displaying who the user has parent view access to
    //gets the kids.
    function getChildren()
    {
        $parent_id = get_current_user_id();
        global $wpdb;
        $table_name = $wpdb->prefix . 'parent_child_record_table';
        $child_IDs = $wpdb->get_results( 
        	"
        	SELECT child_user_id 
        	FROM ". $table_name ."
        	WHERE parent_user_id = ". $parent_id
        );
        
        $children = array();
        foreach ($child_IDs as $child_ID)
        {
            $children[] = get_user_by('id', $child_ID->child_user_id);
        }
        return $children;
    }
    
    //shows the kids.
    function displayChildAccesses_SC()
    {
        $children = getChildren();
        
        
        $out = '';
        if (count($children) >= 1)
        {
            //using code-added standard WP table for same reasons as before
            $out = $out .'<!-- wp:table --> <table class="wp-block-table"><tbody>';
            
            foreach ($children as $child )
            {
                $out = $out . '<tr>';
                $display = '';
                $display = $display . '<td>' . $child->display_name . '</td> <td>' . $child->user_email  . '</td>'; 
                
                 $out = $out . $display;
                $out = $out . '</tr>';
            }
            
            $out = $out . '</tbody></table> <!-- /wp:table -->' . remove_parent2child_link_input_SC();//calls the remover, there's no 
            
        }
        else
        {
            $out = $out . 'No one has given you parent view access';
        }
        return $out;
    }
    add_shortcode('displayChildAccesses', 'displayChildAccesses_SC' );
    

}
add_action('init', 'custom_quiz_shortcodes_init');//runs the above functions on init
