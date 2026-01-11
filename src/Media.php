<?php

namespace Fellyph\TextToAudio;

/**
 * Handles saving audio data to the WordPress Media Library.
 */
class Media {
	/**
	 * Save audio data as an attachment.
	 *
	 * @param int    $post_id    The associated post ID.
	 * @param string $audio_data Binary audio data.
	 * @return int|\WP_Error Attachment ID or WP_Error.
	 */
	public function save_audio( $post_id, $audio_data ) {
		$upload_dir = wp_upload_dir();
		$filename   = 'post-audio-' . $post_id . '-' . time() . '.mp3';
		$filepath   = $upload_dir['path'] . '/' . $filename;

		if ( false === file_put_contents( $filepath, $audio_data ) ) {
			return new \WP_Error( 'file_save_error', __( 'Failed to save audio file to disk.', 'text-to-audio' ) );
		}

		$filetype = wp_check_filetype( $filename, null );

		$attachment = [
			'post_mime_type' => $filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'guid'           => $upload_dir['url'] . '/' . $filename,
		];

		$attachment_id = wp_insert_attachment( $attachment, $filepath, $post_id );

		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filepath );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		return $attachment_id;
	}
}
