<?php
   /*
   Plugin Name: OpenPolls Launcher
   Plugin URI: https://openpolls.com
   Description: Go to Settings > OpenPolls Launcher and paste your Launcher code. Your code is located under the Settings tab in your OpenPolls profile.
   Version: 1.0
   Author: OpenPolls
   Author URI: https://openpolls.com
   License: none
   */
 
   add_action('admin_menu', 'openpolls_custom_admin_menu');
   add_action( 'wp_footer', 'add_openpolls_scripts' );
   
   
   
   
   function openpolls_custom_admin_menu() {
	    add_options_page(
	        'Openpolls Script',
	        'OpenPolls Launcher',
	        'manage_options',
	        'openpolls-plugin',
	        'openpolls_scripts'
	    );
	}
	
	
	
	function openpolls_install() {
		global $wpdb;
	
		$table_name = $wpdb->prefix . 'openpolls_script';
		
		$charset_collate = $wpdb->get_charset_collate();
	
		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			script varchar(255) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
	}
	
	function openpolls_scripts() {
		
		$script = openpolls_get_install_data();
		if($_POST){
			openpolls_install();
			
			$valid = false;
			
			if (strpos($_POST["script"], 'openpolls.com/app/templates/default/js/embed/poll_embed.js') !== false) {
				if (strpos($_POST["script"], '_opp_code_cust') !== false) {
					$valid = true;
				}
				
			}
			if($valid){
				openpolls_install_data($_POST["script"]);
				echo "<div class='updated'><p>Installation successful. Now you can choose to display any poll on any page of your website from the Launcher tab in your OpenPolls profile.</p></div>";
				
			}else{
				echo "<div class='updated' style='border-color:red;'><p>Installation failed. Please paste your Launcher code, which is located under the Settings tab in your OpenPolls profile.</p></div>";
			}
			$script = $_POST["script"];
			
		}
		$actual_link = $_SERVER['REQUEST_URI'];
		
    ?>
    
    <div class="wrap">
        <h2>OpenPolls Launcher</h2>
        
        <form method="post" action="<?=$actual_link?>" class="form"> 
	        <table class="form-table">
		        <tr valign="top">
		        <th scope="row">Paste your code here:</th>
		        <td><textarea type="text" name="script" value="<?php echo esc_attr( get_option('new_option_name') ); ?>" style="width: 300px;height: 150px;"/><?= stripslashes($script); ?></textarea></td>
		        </tr>
		         
		        <tr valign="top">
		        	<th scope="row">&nbsp;</th>
		        	<td><input type="submit" value="Install Launcher"></td>
		        </tr>
		    </table>

        </form>
    </div>
    <?php
	   
	    
}
	
	function openpolls_install_data($script) {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'openpolls_script';
		
		$myrows = $wpdb->get_results( "SELECT * FROM ".$table_name );
		//print_r($myrows[0]->id);
		if(count($myrows) >0) {
			$wpdb->update( 
				$table_name, 
				array( 
					'time' => current_time( 'mysql' ),
					'script' => $script, 
				), 
				array( 'id' => $myrows[0]->id ) 
			);
		}else{
			$wpdb->insert( 
				$table_name, 
				array( 
					'time' => current_time( 'mysql' ), 
					'script' => $script, 
				) 
			);
		}

	}
	
	function openpolls_get_install_data() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'openpolls_script';
		$myrows = $wpdb->get_results( "SELECT * FROM ".$table_name );
		return $myrows[0]->script;
		
	}
	
  
	function add_openpolls_scripts() {
		
		$script = openpolls_get_install_data();
		echo stripslashes($script);
	
	 
	}
?>