<?php
/*Plugin Name: WP Contributors
Plugin URI: http://dharmendrartcamp.tk/
Description:  A plugin to display list of contributors on a post.
Author: Dharmendra
Author URI: http://dharmendrartcamp.tk/
Text Domain: wp_contributors
Version: 1.0.0
*/

/** 
 * The wp_contributors Class.
 */
if(!class_exists(wp_contributors)):
class wp_contributors {

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 */
	public function __construct() {
		//hook for post edit screen
		if ( is_admin() ) {
			add_action( 'add_meta_boxes', array( $this, 'add_wp_contributors_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_wp_contributors' ) );
		}
		//Filter the content to append contributors list on a singe post.
		add_filter( 'the_content', array( $this, 'append_wp_contributors') );
	}

	//Filter the content to append contributors list on a singe post.
	function append_wp_contributors($content) {
		global $post;
		//Check for post type and singe post template.
		if ($post->post_type == 'post' && is_single())
		{
			if ($wp_contributors = get_post_meta( $post->ID, 'wp_contributors',true))
			{
				$contributors = '<div style="border:1px solid grey;padding:5px;"><b>Contributors:</b> <ul style="list-style:none;">';
				foreach($wp_contributors as $wp_contributor){
					//List all author with the display name and email
					$author = get_userdata($wp_contributor);
					$contributors .= '<li><a href="'. get_author_posts_url( $author->ID ) .'">';
					$contributors .= get_avatar($author->user_email,24);
					$contributors .= ' ' . $author->display_name . '</a></li>';
				}
				$contributors .= "</ul></div>";
				$content .= $contributors;
			}
		}
		return $content;
	}
	/**
	 * Adds the meta box container.
	 */
	public function add_wp_contributors_meta_box( $post_type ) {
            $post_types = array('post');     //limit meta box to certain post types
            if ( in_array( $post_type, $post_types )) {
		add_meta_box(
			'some_meta_box_name'
			,__( 'Contributors', 'wp_contributors' )
			,array( $this, 'render_meta_box_content' )
			,$post_type
			,'advanced'
			,'high'
		);
            }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_wp_contributors( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_contributors_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['wp_contributors_inner_custom_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'wp_contributors_inner_custom_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		if ( 'post' != $_POST['post_type'] )
			return $post_id;
		
		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;

		/* OK, its safe for us to save the data now. */
		$wp_contributors =  $_POST['wp_contributors'];

		// Update the meta field.
		update_post_meta( $post_id, 'wp_contributors', $wp_contributors );
	}


	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {
	
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_contributors_inner_custom_box', 'wp_contributors_inner_custom_box_nonce' );
		
		//get all authors list
		$authors=get_users(array('who' => 'authors'));
		
		// Use get_post_meta to retrieve an existing value from the database.
		if (!$wp_contributors = get_post_meta( $post->ID, 'wp_contributors',true))
		{
			$wp_contributors = array();
		}
		echo '<ul class="post-revisions">';
		foreach($authors as $author){
			//List all author with the display name and email
			echo '<li><input type="checkbox" name="wp_contributors[]" value="' . $author->ID . '"';
			echo (in_array($author->ID, $wp_contributors)) ? 'checked >' : '>';
			echo get_avatar($author->user_email,24);
			echo ' ' . $author->display_name . ' (' . $author->user_email . ')</li>';
		}
		echo "</ul>";
		}
}
endif;
/**
* Calls the class on the post edit screen.
*/
new wp_contributors();

?>