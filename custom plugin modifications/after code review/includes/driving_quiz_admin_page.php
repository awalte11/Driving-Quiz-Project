<h1>Admin tools</h1>
<h3>When in doubt, do not touch.</h32>
<?php
    //this section shows who has goverment view access
    require_once dirname(__FILE__) . '/functions.php'; // general functions
    echo '<h2>Users with MVA access </h2>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'mta_flag_table';
    $mva_ids = $wpdb->get_results( //get the mta flag gable
    	"
    	SELECT * 
    	FROM ". $table_name
    );
    $out = '';
    if (count($mva_ids) >= 1)//if there are goverment view flags set
    {
        $out = $out .'<!-- wp:table --> <table class="wp-block-table"><tbody>';
        foreach ($mva_ids as $mva_id)
        {
            //direct table creation was easier than creating a view
            $mva_user = get_user_by('id', $mva_id->user_id);
            
            $out = $out . '<tr><td>' . $mva_user ->display_name . '</td> <td>' . $mva_user ->user_email  . '</td></tr>'; 
            
        }
        
        $out = $out . '</tbody></table> <!-- /wp:table -->';
    }
    else //if none cover that
    {
        $out = 'No users currently have government view access';
    }
    echo $out;
    echo "<h3>Set new MVA user</h3>";
    echo mva_adder();//calls the mva connection adder form setup from below.
    
    
    echo "<h3>Remove MVA user</h3>";
    echo mva_remover();//calls the mva connection remover  form setup from below.
    //mva connecter block start
    //makes the form
    function mva_add_form_code() {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Enter New MVA Account\'s Email Address <br />';
        echo '<input type="email" name="mva-email" value="' . ( isset( $_POST["mva-email"] ) ? esc_attr( $_POST["mva-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="mva-add-submitted" value="Send"/></p>';
        echo '</form>';
        
    }
    //does the work when the form is submitted
    function do_mva_add()
    {
        require_once dirname(__FILE__) . '/functions.php'; // general functions
         if ( isset( $_POST['mva-add-submitted'] ) ) {
    
            // sanitize form values
            $mva_email   = sanitize_email( $_POST["mva-email"] );
                
            $new_mva = get_user_by('email' , $mva_email);
            if ($new_mva == false)//catches invalid input
            {
                echo "Invalid email address";
            }
            else 
            {
                create_mta_flag($new_mva->ID);//creates the flag
                 echo "<meta http-equiv='refresh' content='0'>";//refreshs the page on submission
            }
            
        }
    }
    
    //is called to plug the other twon into the page
    function mva_adder()
    {
        
        ob_start();
        do_mva_add();
        mva_add_form_code();
        
        return ob_get_clean();
    }
    //end connector block
    
    //start disconnecter block
    function mva_remove_form_code() {
        echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
        echo '<p>';
        echo 'Enter MVA Account to deactivate\'s Email Address <br />';
        echo '<input type="email" name="mva-email" value="' . ( isset( $_POST["mva-email"] ) ? esc_attr( $_POST["mva-email"] ) : '' ) . '" size="40" />';
        echo '</p>';
        echo '<p><input type="submit" name="mva-remove-submitted" value="Send"/></p>';
        echo '</form>';
        
    }
    
    //does the disconnect work
    function do_mva_remove()
    {
        require_once dirname(__FILE__) . '/functions.php'; // general functions
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
                echo "<meta http-equiv='refresh' content='0'>";//refreshs the page on submission
            }
            
        }
    }
    //puts the above together
    function mva_remover()
    {
        
        ob_start();
        do_mva_remove();
        mva_remove_form_code();

        return ob_get_clean();
    }
    //end block

?>