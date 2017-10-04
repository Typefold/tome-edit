<?php

/**
 * TOOD: extract all functions related to tinymce in different class
 *
 * @package    tome-edit
 * @subpackage tome-edit/admin/lib/external-media
 */
class Tome_External_Media_Helpers {


	public static function get_thumbnails( $video_url ) {

		$host = self::get_src_host( $video_url );


		switch ( $host ) {
			case 'www.youtube.com':
			case 'youtube.com':
			case 'youtu.be':
				$pattern = '/^.*(youtu.be\/|v\/|embed\/|watch\?|youtube.com\/user\/[^#]*#([^\/]*?\/)*)\??v?=?([^#\&\?]*).*/';
				preg_match( $pattern, $video_url, $result);
				return 'http://img.youtube.com/vi/' . $result[3] . '/0.jpg';
			break;

			case 'player.vimeo.com':
			case 'vimeo.com':
				$pattern = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';
				preg_match( $pattern, $video_url, $result );
				return self::get_vimeo_thumbnail( $result[5] );

			break;

			default:
			break;
		}

		echo 'couldn"t find video thumbnail.';

		return false;
	}




	private function get_src_host( $src_attribute ) {
		preg_match("/^(?:ftp|https?):\/\/(?:[^@:\/]*@)?([^:\/]+)/", $src_attribute, $result); 

		return $result[1];
	}



	private function get_vimeo_thumbnail( $vimeo_id ) {
		$hash = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$vimeo_id.php"));
		return $hash[0]['thumbnail_medium'];
	}


}