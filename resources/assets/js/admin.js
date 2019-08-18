const hello = () => {
	const world = 'admin';
	console.log( `Hello ${ world }` );
};

hello();

/* global $, wp */
$( document ).ready( () => {
	// Uploading files
	let fileFrame;
	const wpMediaPostID = wp.media.model.settings.post.id; // Store the old id
	// let setToPostID = <?php echo $my_saved_attachment_post_id; ?>; // Set this
	const setToPostID = null;
	$( '#upload_image_button' ).on( 'click', function( event ) {
		event.preventDefault();
		// If the media frame already exists, reopen it.
		if ( fileFrame ) {
			// Set the post ID to what we want
			fileFrame.uploader.uploader.param( 'post_id', setToPostID );
			// Open frame
			fileFrame.open();
			return;
		}
		// Set the wp.media post id so the uploader grabs the ID we want when initialised
		wp.media.model.settings.post.id = setToPostID;
		// Create the media frame.
		fileFrame = wp.media.frames.file_frame = wp.media( {
			title: 'Select a image to upload',
			button: {
				text: 'Use this image',
			},
			multiple: false,	// Set to true to allow multiple files to be selected
		} );
		// When an image is selected, run a callback.
		fileFrame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			const attachment = fileFrame.state().get( 'selection' ).first().toJSON();
			// Do something with attachment.id and/or attachment.url here
			$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
			$( '#image_attachment_id' ).val( attachment.id );
			// Restore the main post ID
			wp.media.model.settings.post.id = wpMediaPostID;
		} );
		// Finally, open the modal
		fileFrame.open();
	} );
	// Restore the main ID when the add media button is pressed
	$( 'a.add_media' ).on( 'click', function() {
		wp.media.model.settings.post.id = wpMediaPostID;
	} );
} );
