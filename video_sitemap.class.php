<?php

/**
 * All the function relative to an image sitemap
 * @author ryan
 *
 */
class Video_Sitemap{
	
	/**
	 * Number max of link in a sitemap file. Google say 50k
	 * @var integer
	 */
	const MAX_LINK = 45000;
	
	/**
	 * Size max for a sitemap file. Google say 10Mb
	 * @var integer
	 */
	const MAX_SIZE = 9000000;
	
	/**
	 * absolute path to wrtie the file
	 * @var unknown_type
	 */
	private $pathRoot;
	
	/**
	 * Name of the file
	 * @var string
	 */
	private $videoNameFile;
		
	function __construct($path, $videoname ){
		
		
		$this->pathRoot = $path;
		
		//echo "path: $path";
	//	echo "name: $videoname";
		
		$this->videoNameFile = $videoname;
	}

	/**
	 * Write content in the sitemap file
	 * @param string $content
	 * @param integer $numFile Number of the file
	 * @return FALSE on error, number of caracter written on sucess
	 */
	private function write($content,$numFile){
		$fileName = $this->pathRoot.$this->videoNameFile.$numFile.'.xml';
		return @file_put_contents($fileName, $content);
	}
	
	
	/**
	 * Main function
	 * @return boolean TRUE on sucess, FALSE on error
	 */
	public function generate($res, $show = false){

		/**
		 * All the image of the blog
		 * @var array
		 */
		
		if ($show)
		{
			//echo "in generate";
		}
		$videos = $this->get_videos($show);
		
		if ($show)
		{
			echo "found videos: " . count($videos) . "<br/>";
		}
			
		/**
		 * WP options, mainly stats about generation
		 * @var array
		 */
		$options = array();
		
		/**
		 * Number of images
		 * @var integer
		 */
		$nbOfVideos = 0;
		
		/**
		 * number of character to write to the sitemap file
		 * @var integer
		 */
		$length = 0;
		
		/**
		 * Number of image site map file
		 * @var integer
		 */
		$numFile = 0;
		
		/**
		 * id of the post where the image is link. Usefull to the loc tag
		 * @var integer
		 */
		$idParent = -1;
		
		$output = '';
		
		//echo "<p>generating video sitemap:</p>";
		
		
		$output .= '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">'."\n";
		
		$videocustomvar = sw_media_sitemap_get_video_videocustomvariable();
		$thumbcustomvar = sw_media_sitemap_get_video_thumbnailcustomvariable();
		
		foreach($videos as $post){
			//new file
			
			
			/*if( $nbOfVideos % self::MAX_LINK == 0 || ob_get_length() > self::MAX_SIZE ){
				//not the fist iteration -> close file
				if($nbOfVideos){
					$output .= "\t<url>\n</urlset>";
					if( ! $this->write($output, $numFile ) )
						return false;
					$output = '';
					$numFile++;
				}
				
				//create file
			}*/
			

			
			
			setup_postdata($post);
			//var_dump($post);
			//echo "date: " . get_the_time();
			//var_dump($post);
			//new post
			/*if( $idParent != $video->ID ){
				//close previous <url>
				if($nbOfVideos){
					$output .= "</url>\n";
				}
				$pageUrl = get_permalink($video->ID);
				if(empty( $pageUrl) )
					$pageUrl = trailingslashit(get_option('siteurl') );
				$output .= "<url>\n\t<loc>".$pageUrl."</loc>\n";
				$idParent = $video->postID;
			}*/
			
			
			$content_loc = "";
			$t = get_post_meta($post->ID, $videocustomvar, true);
			if (isset($t))
			{
				//echo "content_loc: $t<br/>";
				$content_loc = $t;
			}
			unset($t);
			
			$player_loc = "";
			$t = $post->guid;
			if (isset($t))
			{
				//echo "player_loc: $t<br/>";
				$player_loc = $t;
			}
			unset($t);
			
			
			
			
			
			
			//$video->guid;
			
			$thumbnail_loc ="";
			$t = get_post_meta($post->ID, $thumbcustomvar, true);
			if (isset($t))
			{
				//echo "thumbnail_loc: $t<br/>";
				$thumbnail_loc = $t;
			}
			unset($t);
			
					
			$title = "";
			$t = $post->post_title;
			if (isset($t))
			{
				//echo "title: $t<br/>";
				$title = $t;
			}
			unset($t);
			
			
			$description=$post->post_content;
			
		//		$datetime = strtotime( $post->post_date);
		//	$mysqldate = date(DATE_W3C, $datetime);
			
 		//	echo "dateb:" . $mysqldate;
 			
			//echo "datea: " . date(DATE_W3C,$post->post_date);
			//echo "date2: " . $post->post_date;
			$rating = "";
			$view_count = "";
			$publication_date = date(DATE_W3C, strtotime($post->post_date));
			$expiration_date = "";
			$category = "";
			$duration = "3000";
			
			$loc = get_permalink($post->ID);
			//$restriction = "":
			//display image data
			$output .= "\t<url>\n\t\t";
			$output .= "\t<loc>$loc</loc>\n\t\t";
			$output .= "\t<video:video>\n\t\t";
			$output .= "<video:content_loc>$content_loc</video:content_loc>\n\t";
			$output .= "<video:player_loc>$player_loc</video:player_loc>\n\t";
			$output .= "<video:thumbnail_loc>$thumbnail_loc</video:thumbnail_loc>\n\t";
			$output .= "<video:title><![CDATA[\"$title\"]]></video:title>\n\t";
			$output .= "<video:description><![CDATA[\"$description\"]]></video:description>\n\t";
			//$output .= "<video:rating>$rating</video:rating>\n\t";
			//$output .= "<video:view_count>$view_count</video:view_count>\n\t";
			$output .= "<video:publication_date>$publication_date</video:publication_date>\n\t";
			//$output .= "<video:expiration_date>$expiration_date</video:expiration_date>\n\t";
			//$output .= "<video:category>$category</video:category>\n\t";
			$output .= "<video:duration>$duration</video:duration>\n\t";
			//$output .= "<video:restriction relationship='allow'>IE GB US CA</video:restriction>\n\t";
			$output .= "</video:video>\n\t";
			$output .= "</url>\n\t\t";
			
			
			//$output .= "\t<image:image>\n\t\t<image:loc>". $video->url ."</image:loc>\n";
			//if( isset($video->caption) ){
			//	$output .= "\t\t<image:caption>".$video->caption."</image:caption>\n";
		//	}
			//if( isset($video->title) ){
		//		$output .= "\t\t<image:title>".$video->title."</image:title>\n";
			//}
			

			if ($show)
			{
				echo "<p>processing video: $post->ID  $title $loc</p>";
			}
			
			$nbOfImages++;
		}
		$output .= "\n</urlset>";
		
		//echo "<br/><br/>XML:$output";
		
		
		if( ! $this->write( $output , $numFile ) )
						return false;

		$options['nbVideo'] = $nbOfVideos;
		$options['nbVideoFile'] = $numFile;				
		//sw_media_sitemap_saveOption(OPTN_VIDEO,$options);				
		
		return $numFile;
	}
	
	/**
	 * Return an array of object with image information inside
	 * @author Thomas Genin
	 * @return array
	 */
	private function get_videos($show = false){
		
		 
		global $wpdb;
	//	$category="Videos";
		//$variable="_videoembed";
		//p.post_title as title, p.post_excerpt as caption, p.guid as url, p.post_parent as postID, pm.meta_value as url
		
		$categories =  sw_media_sitemap_get_video_categories();
		$exclude = 		 sw_media_sitemap_get_video_exclude();
		/*$query = "SELECT * 
			FROM $wpdb->posts p 
			WHERE 
			p.post_type='post'";
		
		if ($categories !="")
		{
			$query .=" and p.id in ( select object_id from $wpdb->term_relationships where term_taxonomy_id in ($categories) )";
		}
		
		if ($exclude !="")
		{
			$query .= " and p.id not in ($exclude)";
			
		}*/
			// and p.ID"
			 //no in ("pm.post_id and pm.meta_key='_videoembed'";
	
		
		
		//echo "<p>SQL:$query</p>";
		
			 //,  ".$wpdb->prefix."terms t,  ".$wpdb->prefix."term_relationships tr
			 
		/*	SELECT P.post_title as title, P.post_excerpt as caption, P.guid as url, P.post_parent as postID
			FROM ".$wpdb->prefix."posts P
			WHERE post_type = 'attachment'
			ORDER BY post_parent DESC";*/
		//$wpdb->show_errors();
		
		$ret = query_posts("cat=$categories&posts_per_page=-1&exclude=$exlclude");
			
		//$ret = $wpdb->get_results($query, OBJECT);


		return $ret;
	}
}
