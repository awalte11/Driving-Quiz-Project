<h1>Admin tools</h1>
<h3>When in doubt, do not touch.</h32>
<?php
    //require dirname(__FILE__) . '/functions.php'; // general functions

    echo '<h2>Users with MVA access </h2>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'mta_flag_table';
    $mva_ids = $wpdb->get_results( 
    	"
    	SELECT * 
    	FROM ". $table_name
    );
    //$mva_users = array();
    $out = '';
    if (count($mva_ids) >= 1)
    {
        $out = $out .'<!-- wp:table --> <table class="wp-block-table"><tbody>';
        foreach ($mva_ids as $mva_id)
        {
           
            $mva_user = get_user_by('id', $mva_id->user_id);
            
            $out = $out . '<tr><td>' . $mva_user ->display_name . '</td> <td>' . $mva_user ->user_email  . '</td></tr>'; 
            
        }
        
        $out = $out . '</tbody></table> <!-- /wp:table -->';
    }
    else 
    {
        $out = 'No users currently have government view access';
    }
    echo $out;
    echo "<h3>Set new MVA user</h3>";
    echo mva_adder();
    
    
    echo "<h3>Remove MVA user</h3>";
    echo mva_remover();
    
    function mva_add_form_code() {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Enter New MVA Account\'s Email Address <br />';
        echo '<input type="email" name="mva-email" value="' . ( isset( $_POST["mva-email"] ) ? esc_attr( $_POST["mva-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="mva-add-submitted" value="Send"/></p>';
        echo '</form>';
        
    }
    
    function do_mva_add()
    {
         if ( isset( $_POST['mva-add-submitted'] ) ) {
    
            // sanitize form values
            $mva_email   = sanitize_email( $_POST["mva-email"] );
                
            $new_mva = get_user_by('email' , $mva_email);
            if ($new_mva == false)
            {
                echo "Invalid email address";
            }
            else 
            {
                create_mta_flag($new_mva->ID);
                echo "Added to MVA list";
            }
            
        }
    }
    
    function mva_adder()
    {
        
        ob_start();
        do_mva_add();
        mva_add_form_code();

        return ob_get_clean();
    }
    
    
    
    function mva_remove_form_code() {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Enter MVA Account to deactivate\'s Email Address <br />';
        echo '<input type="email" name="mva-email" value="' . ( isset( $_POST["mva-email"] ) ? esc_attr( $_POST["mva-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="mva-remove-submitted" value="Send"/></p>';
        echo '</form>';
        
    }
    
    function do_mva_remove()
    {
         if ( isset( $_POST['mva-remove-submitted'] ) ) {
    
            // sanitize form values
            $mva_email   = sanitize_email( $_POST["mva-email"] );
                
            $new_mva = get_user_by('email' , $mva_email);
            if ($new_mva == false)
            {
                echo "Invalid email address";
            }
            else 
            {
                remove_mta_flag($new_mva->ID);
                echo "Removed from MVA list";
            }
            
        }
    }
    
    function mva_remover()
    {
        
        ob_start();
        do_mva_remove();
        mva_remove_form_code();

        return ob_get_clean();
    }

?>