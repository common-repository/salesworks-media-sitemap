<?php
/**
 * General class to generate the sitemap
 * @author ryan folstad
 *
 */
class Sitemap{
		
	/**
 	* Absolute path  to the website root directory
 	* @var string
	*/
	protected $pathRoot;
	
	/**
	 * Name of sitemap file for image sitemap
	 * @var unknown_type
	 */
	//protected $imgNameFile = IMG_SITEMAP;
	//protected $videoNameFile = VIDEO_SITEMAP;
	
	protected $fileName;
	
	function __construct($path, $name){ 
		$this->pathRoot = $path;
		$this->fileName = $name;
		
	}
	
	static public function get_imgNameFile(){
		return $this->imgNameFile;
	}
	
	/**
	 * Generate the XML Sitemap
	 * @return bool
	 */
	public function generate($show = false){
		
//		$this->test();
		
		//generate index
//		$res = $this->generate_image();
//		if( $res === false  ){
	//		return false;
//		}
		//generate index
	//	$res = $this->generate_index($res);
//		if( $res === false  ){//

//			return false;
//	}
		//generate video index
		$res = $this->generate_video($res, $show);
		{
			if ($res === false) {
				return false;
			}
		
		}
		
		return true;
	}
	
	private function generate_video($res, $show)
	{
		require_once 'video_sitemap.class.php';
		$video = new Video_Sitemap($this->pathRoot, $this->fileName);
		if ($show)
		{
			//echo "in generate_video";
		}
		return $video->generate($res, $show);
	}
	
	/**
	 * Generate the xml sitemap with on;y the image
	 * @return bool
	 */
	
	
	private function generate_image(){
		require_once 'image_sitemap.class.php';
		$img = new Image_Sitemap( $this->pathRoot, $this->imgNameFile );
		return $img->generate();
	}
	
	
	/**
	 * Generate the index Sitemap which contain all the link to other sitemap files
	 * @param $numFile Number of sitemap files we have generated
	 * @return bool TRUE on succes, FALSE if an error occur
	 */
	private function generate_index($numFile){
		
		$numFile++;
		$output = "<?xml version='1.0' encoding='UTF-8'?>\n\t<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>\n";
		$siteURL = wpms_get_site_url();
	
		//Links to image sitemap
		for($i=0;$i<$numFile;$i++){
			$output .= "\t<sitemap>\n\t\t<loc>". $siteURL .$this->imgNameFile.$i .".xml</loc>\n\t\t<lastmod>". date('c') ."</lastmod>\n\t</sitemap>\n";
			
		}
		$output .= '</sitemapindex>';
		
		if( false === file_put_contents($this->pathRoot.'media_sitemap.xml', $output) ){
			return false;
		}
		
		return true;					
	}
}//end of class


/**
* Checks if the plugin options have been saved once
* and adds a message to inform the user if not
*
* @since 1.0.3
* @author scripts@schloebe.de
*/
/*function sw_activationNotice() {
	$assignoptionsoncemessage = __('You just installed the "Salesworks Video Sitemap" plugin. Please <a href="options-general.php?page=salesworks-media-sitemap/sw-media-sitemap.php">save the options once</a> to assign the new capabilities to the system!', 'salesworks-media-sitemap');
	echo '<div id="assignoptionsoncemessage" class="error fade">
		<p>
			<strong>
				' . $assignoptionsoncemessage . '
			</strong>
		</p>
	</div>';
}

if( (version_compare( $GLOBALS['wp_version'], '2.4.999', '>' ) && get_option('ridwpa_reassigned_075_options') == '0') || (version_compare( $GLOBALS['wp_version'], '2.4.999', '>' ) && get_option('ridwpa_reassigned_115_options') == '0') ) {
	add_action('admin_notices', 'ridwpa_activationNotice');
}

/*
 *if( (version_compare( $GLOBALS['wp_version'], '2.4.999', '>' ) && get_option('ridwpa_reassigned_075_options') == '0') || (version_compare( $GLOBALS['wp_version'], '2.4.999', '>' ) && get_option('ridwpa_reassigned_115_options') == '0') ) {
	add_action('admin_notices', 'ridwpa_activationNotice');
}

 * 
 * */


 

