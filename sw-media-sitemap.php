<?php
/*
Plugin Name: SW Video SiteMap
Plugin URI: http://www.salesworks.com
Description: SW Video Sitemap is a sitemap generator for images, video, and flash animation
Author: Ryan Folstad
Author URI: http://www.salesworks.com
Version: 1.0.1
*/


require_once 'sitemap.class.php';

//---------------------------------------------------------------------------
register_activation_hook(__FILE__, 'sw_media_sitemap_install');
register_deactivation_hook(__FILE__, 'sw_media_sitemap_uninstall');
//---------------------------------------------------------------------------
//add_action('admin_init', 'sw_media_sitemap_generate');


add_action('save_post ', 'sw_sitemap_generate', "N",false);


//Existing posts was deleted
//add_action('delete_post', 'sw_media_sitemap_generate',9999,1);
add_action('delete_post', 'sw_sitemap_generate',false);
			
//Existing post was published
add_action('publish_post', 'sw_sitemap_generate',false);
			
//Existing page was published
add_action('publish_page', 'sw_sitemap_generate',false);
			

//add_action('publish_page ', 'sw_media_sitemap_generate');
add_action('admin_menu', 'sw_media_sitemap_create_menu');
//---------------------------------------------------------------------------	

/*define('OPTN_IMAGE','sw_media_sitemap_image');
define('OPTN_VIDEO','sw_media_sitemap_video');
define('OPTN_FLASH','sw_media_sitemap_flash');*/
define('OPTN_SYSTM','sw_media_sitemap_systm');
define('OPTN_OPTIONS','sw_media_sitemap_options');

define('OPTN_SAVED','sw_media_sitemap_saved');

define('WPMS_DIR','media-sitemap');
define('INDEX_SITEMAP','media_sitemap.xml');
define('IMG_SITEMAP','img_sitemap');

	/**
	 * 
	 * @return 
	 */
if(!function_exists("sw_media_sitemap_install")){
	function sw_media_sitemap_install(){
//		$path = sw_media_sitemap_get_root_path( true );
		
		
		
		
		
		$errorM = '';
		$isError = false;
/*		
		if( ! mkdir( $path . WPMS_DIR , 0755) ){
			$isError = true;
			$errorM .= $path . ' '. __('the directory must be writable at least for the installation of the plugin.','sw-media-sitemap') . '<br/><br/>'; 
		}
/**/		
		if(! function_exists('file_put_contents') ){
			$isError = true;
			$errorM .= __('Please update to PHP5','sw-media-sitemap') . '<br/><br/>';
		}
		if(version_compare($wp_version,"2.9","<")){
			$isError = true;
			$errorM .=  __('Sw Media Sitemap requires WordPress 2.9 or newer.<a href="http://codex.wordpress.org/Upgrading_WordPress">Please update!</a><br/>');
		}
		
		if($isError){
			//die( $errorM );
		}
		
		//add_option( OPTN_IMAGE, '', '', 'no');
		//add_option( OPTN_VIDEO, '', '', 'no');
		//add_option( OPTN_FLASH, '', '', 'no');
		
		$default_sitemap_options =  array(
						'sw_categories' 				=> '5',
						'mode' 			=> '',
						'videocustomvariable' => '_videoembed',  //$videocustomvariable,
						'thumbnailcustomvariable' => 'featuredimage', //$thumbnailcustomvariable,
						'exclude' => '', //$exclude,
						'videositemap_loc' => 'video_sitemap' //$videositemap_loc
					);
		
		
		add_option(OPTN_OPTIONS, serialize($default_sitemap_options),'','no');
		add_option( OPTN_SYSTM, serialize(array('time' => 0, 'success' => 3 )), '', 'no');
	}
}

	/**
	 * Destroy everything create by the plugin 
	 * @return 
	 */
if(!function_exists("wp_media_siteap_uninstall")){
	function sw_media_sitemap_uninstall(){    
		//delete_option( OPTN_IMAGE, serialize( array() ), '', 'no');
		//delete_option( OPTN_VIDEO, serialize( array() ), '', 'no');
		//delete_option( OPTN_FLASH, serialize( array() ), '', 'no');
		delete_option(OPTN_OPTIONS, serialize( array()), '','no');
		delete_option( OPTN_SYSTM, serialize( array() ), '', 'no');
	}	
}	
	


if(!function_exists("sw_media_sitemap_loadOption")){	
	function sw_media_sitemap_loadOption( $name){
	//	echo "loading option: $name";
		//var_dump(get_option($name));
		return maybe_unserialize(get_option( $name, ""));
	}
}


if(!function_exists("sw_media_sitemap_saveOption")){	
	function sw_media_sitemap_saveOption($name, $array){
		//echo "saving option: $name";
		//var_dump($array);
		update_option( $name, serialize($array) );
	}
}

if(!function_exists("sw_media_sitemap_get_videocustomvariable")){
	function sw_media_sitemap_get_video_videocustomvariable(){
		$optionsOptions=sw_media_sitemap_loadOption(OPTN_OPTIONS);	
		$ret = $optionsOptions['videocustomvariable'];
		return $ret;
	}
}	




if(!function_exists("sw_media_sitemap_get_video_thumbnailcustomvariable")){
	function sw_media_sitemap_get_video_thumbnailcustomvariable(  ){
		$optionsOptions=sw_media_sitemap_loadOption(OPTN_OPTIONS);	
		$ret = $optionsOptions['thumbnailcustomvariable'];
		return $ret;
	}
}

if(!function_exists("sw_media_sitemap_get_video_exclude")){
	function sw_media_sitemap_get_video_exclude(  ){
		$optionsOptions=sw_media_sitemap_loadOption(OPTN_OPTIONS);	
		$ret = $optionsOptions['exclude'];
		return $ret;
	}
}	


if(!function_exists("sw_media_sitemap_get_video_categories")){
	function sw_media_sitemap_get_video_categories(  ){
		
		//$root = sw_media_sitemap_get_root_path();
		
		$optionsOptions=sw_media_sitemap_loadOption(OPTN_OPTIONS);
		
		$ret = $optionsOptions['sw_categories'];
		
		return $ret;
	}
}	


if(!function_exists("sw_media_sitemap_get_video_name")){
	function sw_media_sitemap_get_video_name(  ){
		
		//$root = sw_media_sitemap_get_root_path();
		
		$optionsOptions=sw_media_sitemap_loadOption(OPTN_OPTIONS);
		
		$ret = $optionsOptions['videositemap_loc'];
		
		return $ret;
	}
}	

	/**
	 * Return the absolute path of the root directory of the website
	 * @return string
	 */




if(!function_exists("sw_media_sitemap_get_root_path")){
	function sw_media_sitemap_get_root_path( $isRoot = false ){
		$home = get_option( 'home' );
		$siteurl = get_option( 'siteurl' );
		
		
		if ( $home != '' && $home != $siteurl ) {
		        $wp_path_rel_to_home = str_replace($home, '', $siteurl); /* $siteurl - $home */
		        $pos = strpos($_SERVER["SCRIPT_FILENAME"], $wp_path_rel_to_home);
		        $home_path = substr($_SERVER["SCRIPT_FILENAME"], 0, $pos);
		} else {
			$home_path = ABSPATH;
		}
		
		return trailingslashit($home_path);
		
		/*if( $isRoot )
			return trailingslashit($home_path);
		else
			return trailingslashit($home_path . WPMS_DIR);*/
	}
}	
	

if (!function_exists("sw_sitemap_generate")) {
	function sw_sitemap_generate()
	{
		sw_media_sitemap_generate(false);
	}
}
	/**
	 * Generate Sitemap
	 * @return unknown_type
	 */
if(!function_exists("sw_media_sitemap_generate")){
	function sw_media_sitemap_generate($show = false){
		
		if ($show)
		{
			echo "Generating Sitemap..<br/>";
		}
		
		$startDate = sw_media_sitemap_timer();
		//echo "Start Date: $starDate";
		
		
		$sitemap = new Sitemap( sw_media_sitemap_get_root_path(), sw_media_sitemap_get_video_name());
		
		//echo "Sitemap: $sitemap";
		
		$res = $sitemap->generate($show);
		if( $res === false  ){
			$endDate = sw_media_sitemap_timer();
			$executionTime = number_format($endDate - $startDate,7);
			//$options = array('time' => $executionTime, 'success' => 0 );
			sw_media_sitemap_saveOption(OPTN_SYSTM, $options);
			return false;
		}
		$endDate = sw_media_sitemap_timer();
		$executionTime = number_format($endDate - $startDate,7);
		//$options = array('time' => $executionTime, 'success' => 1 );
		
		if ($show)
		{
			echo "<p>Sitemap built in : $executionTime</p>";
			
			echo "<a href='". get_option('siteurl') ."/". sw_media_sitemap_get_video_name() . "0.xml'>Review</a>";
		}
		//sw_media_sitemap_saveOption(OPTN_SYSTM, $options);
		/**/
	}
}
	
if(!function_exists("sw_media_sitemap_error")){	
	function sw_media_sitemap_error(){
		echo '<script type="text/javascript">alert("sw-media-sitemap encounter an error and wasn\'t able to generate the sitemap file.\n\nPlease look at the option page of the plugin for more information.")</script>';
	}
}
	
if(!function_exists("sw_media_sitemap_create_menu")){
	function sw_media_sitemap_create_menu(){
		add_options_page('sw-media-sitemap','sw-media-sitemap',1, __FILE__,'sw_media_sitemap_option_page','sw_media_sitemap_option_page');
	}
}
	
if(!function_exists("sw_media_sitemap_option_page")){
	

	function sw_media_sitemap_option_page(){
		echo '<h1>Salesworks Media Sitemap Options</h1>';
?>
		
<?php
		$optionsSys = sw_media_sitemap_loadOption( OPTN_SYSTM );
		//$optionsImg = sw_media_sitemap_loadOption( OPTN_IMAGE );
		//$optionsVideo = sw_media_sitemap_loadOption( OPTN_VIDEO );
		$optionsOptions=sw_media_sitemap_loadOption(OPTN_OPTIONS);
		$path = sw_media_sitemap_get_root_path( );
		
		$action = $_POST['action'];
		$nonce = $_POST['_wpnonce'];
		$ref = $_POST['_wp_http_referer'];
		
		//if ( !wp_verify_nonce( $_POST['attachments_nonce'], plugin_basename(__FILE__) )) {
		//return $post_id;
	
		if ($action =="build")
		{
			echo "<div id=message class='updated fade below-h2'><p>Generating Sitemap!</p>";
			sw_media_sitemap_generate(true);
			echo "</div>";
			echo "<a href='options-general.php?page=salesworks-media-sitemap/sw-media-sitemap.php'>Back to Settings</a>";
			return;
		}
		if ($action =="update")
		{
			//echo "update specified";
				/*if (wp_verify_nonce($_POST['sw-media-sitemap-update-options'], plugin_basename(__FILE__) ))
				{
					echo "nonce verified";
				}*/
			
			$sw_categories = $_POST['sw_categories'];
			$mode = $_POST['mode'];
			$videocustomvariable = $_POST['videocustomvariable'];
			$thumbnailcustomvariable = $_POST['thumbnailcustomvariable'];
			$exclude = $_POST['exclude'];
			$videositemap_loc =  $_POST['videositemap_loc'];
			
			
			$sitemap_options =  array(
						'sw_categories' 				=> $sw_categories,
						'mode' 			=> $mode,
						'videocustomvariable' 			=> $videocustomvariable,
						'thumbnailcustomvariable' 			=> $thumbnailcustomvariable,
						'exclude' 			=> $exclude,
						'videositemap_loc' => $videositemap_loc
					);
				
			sw_media_sitemap_saveOption(OPTN_OPTIONS, $sitemap_options);
			echo "<div id=message class='updated fade below-h2'><p>Saved Options</p></div>";
				
			
		}
		else
		{
				if($optionsOptions)
				{
					$sw_categories = $optionsOptions['sw_categories'];
					$mode = $optionsOptions['mode'];
					$videocustomvariable = $optionsOptions['videocustomvariable'];
					$thumbnailcustomvariable = $optionsOptions['thumbnailcustomvariable'];
					$exclude = $optionsOptions['exclude'];
					$videositemap_loc = $optionsOptions['videositemap_loc'];
					
				}
		}
		
?>

<?php 

	if (!wpms_IsFileWritable(sw_media_sitemap_get_root_path() . sw_media_sitemap_get_video_name()) )
	{
		echo "<div id=error>ERROR: " . sw_media_sitemap_get_root_path() . sw_media_sitemap_get_video_name() . " is not writeable and I can't fix it</div>";
	}
	else
	{
		//echo "<div id=message class='updated fade below-h2'>" . sw_media_sitemap_get_root_path() . sw_media_sitemap_get_video_name() . "</div>";
	}

?>


		<form action="options-general.php?page=salesworks-media-sitemap/sw-media-sitemap.php" method="post" >
		<?php wp_nonce_field('sw-media-sitemap-update-options'); ?>
		<ul>
		
		<li><label for=sw_categories>Categories:</label><input id=sw_categories name=sw_categories value=<?php echo $sw_categories; ?> /> </li>
		<li><small>Only check posts in these categories for videos (specify the category id's in a comma seperated list)</small></li>
		<!-- <li><label for=mode>Mode:</label>
		<select>
			<option value=meta>Meta Variable</option>
			<option value=media>Media Library</option>
		</select></li>-->
		<li><label for=videocustomvariable>Video Post Meta Variable:</label><input id=videocustomvariable name=videocustomvariable value=<?php echo $videocustomvariable; ?>></input></li>
		<li><small>The post meta variable that contains the location of the video file.  ie: _videoembed which contains http://yourserver/video.mp4</small></li>
		<li><label for=thumbnailcustomvariable>Thumbnail Post Meta Variable:</label><input id=thumbnailcustomvariable name=thumbnailcustomvariable value=<?php echo $thumbnailcustomvariable; ?>></input></li>
		<li><small>The post meta variable that contains the location of the thumbnail file</small></li>
		<li><label for=exclude>Exclue Posts:</label><input id=exclude name=exclude  value=<?php echo $exclude; ?>></input></li>
		<li><small>Comma seperated list of posts to exclude from the video sitemap</small></li>
		<li><label for=videositemap_loc>Video Sitemap Location:</label><input id=videositemap_loc name=videositemap_loc  value=<?php echo $videositemap_loc; ?>></input></li>
		<li><small>The filename to write the sitemap .xml will automatically be appended.</small></li>
		
		
		<li></li>
					
					<li>
			<p class="submit">
				<input type="submit" class="button-primary" value='Save Settings'" />
			</p>
			</li>
		</ul>
		<input type="hidden" name="action" value="update" />
		</form>
		
		<form action="options-general.php?page=salesworks-media-sitemap/sw-media-sitemap.php" method="post" >
		<input type=submit id=build class="button-primary" value="Build Sitemap"></input>
		<input type="hidden"  name="action" value="build" />
		</form>
	<?php
		
	}
}
	
	/**
	 * Function necessary to get time execution of the script
	 * @return unknown_type
	 */
if(!function_exists("sw_media_sitemap_timer")){
	function sw_media_sitemap_timer()
	{
		$time=explode(' ',microtime() );
		return $time[0] + $time[1];
	} 
}
	
	/**
	 * Checks if a file is writable and tries to make it if not.
	 *
	 * @since 3.05b
	 * @access private
	 * @author  VJTD3 <http://www.VJTD3.com>
	 * @return bool true if writable
	 */
if(!function_exists("wpms_IsFileWritable")){
	function wpms_IsFileWritable($filename) {
		//can we write?
		if(!is_writable($filename)) {
			//no we can't.
			if(!@chmod($filename, 0666)) {
				$pathtofilename = dirname($filename);
				//Lets check if parent directory is writable.
				if(!is_writable($pathtofilename)) {
					//it's not writeable too.
					if(!@chmod($pathtoffilename, 0666)) {
						//darn couldn't fix up parrent directory this hosting is foobar.
						//Lets error because of the permissions problems.
						return false;
					}
				}
			}
		}
		//we can write, return 1/true/happy dance.
		return true;
	}
}

if(!function_exists("wpms_get_site_url")){
	function wpms_get_site_url(){
		return trailingslashit(get_option('siteurl') ) . WPMS_DIR . '/' ;
	}
}
