<?php
/**
 * Cloudinary upload widget on learner artwork comment forms (logged-in staff).
 *
 * @package Art_Tutor_Hanoi
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'wp_enqueue_scripts',
	function () {
		if ( ! is_singular( 'post' ) || ! ath_is_learner_artwork_post() || ! is_user_logged_in() ) {
			return;
		}

		wp_enqueue_script(
			'cloudinary-upload-widget',
			'https://widget.cloudinary.com/v2.0/global/all.js',
			array(),
			null,
			true
		);

		$folder = get_post()->post_name;

		wp_add_inline_script(
			'cloudinary-upload-widget',
			sprintf(
				'(function(){
					function init(){
						var btn=document.getElementById("ath-cloudinary-upload");
						if(!btn||btn.dataset.bound||typeof cloudinary==="undefined")return;
						btn.dataset.bound="1";
						btn.addEventListener("click",function(){
							cloudinary.createUploadWidget({
								cloudName:"dmmlu0ebu",
								uploadPreset:"ArtTutor_web",
								folder:%s,
								multiple:true,
								maxFiles:10
							},function(err,res){
								if(err||!res||res.event!=="success")return;
								var url=res.info.secure_url;
								var preview=document.getElementById("ath-cloudinary-preview");
								if(preview){
									var img=document.createElement("img");
									img.src=url;
									img.style.cssText="max-width:150px;margin:5px;border-radius:6px";
									preview.appendChild(img);
								}
								var box=document.getElementById("comment");
								if(box){
									box.value+=(box.value?"\\n":"")+url+"\\n";
									box.focus();
								}
							}).open();
						});
					}
					if(document.readyState==="loading"){
						document.addEventListener("DOMContentLoaded",init);
					}else{init();}
				})();',
				wp_json_encode( $folder )
			)
		);
	},
	20
);

add_action(
	'comment_form_logged_in_after',
	function () {
		if ( ! is_singular( 'post' ) || ! ath_is_learner_artwork_post() ) {
			return;
		}

		echo '<div class="ath-cloudinary-upload" style="margin:20px 0;">';
		echo '<button type="button" id="ath-cloudinary-upload" style="padding:8px 15px;background:#000;color:#fff;border:none;border-radius:6px;cursor:pointer;">Upload Image</button>';
		echo '<div id="ath-cloudinary-preview" style="margin-top:15px;"></div>';
		echo '</div>';
	},
	5
);

/**
 * Remove legacy Cloudinary upload markup pasted into post content (broken Gutenberg paragraphs).
 *
 * @param string $content Post content HTML.
 */
function ath_strip_legacy_comment_upload_markup( $content ) {
	if ( ! is_singular( 'post' ) || ! ath_is_learner_artwork_post() ) {
		return $content;
	}

	// Entire broken block: button + preview + script split into <p> tags.
	$content = preg_replace(
		'/<div[^>]*>\s*<button id="upload_widget"[\s\S]*?<\/script>\s*<\/p>/i',
		'',
		$content
	);

	$content = preg_replace( '/<button id="upload_widget"[\s\S]*?<\/button>/i', '', $content );
	$content = preg_replace( '/<div id="upload_preview"[^>]*>[\s\S]*?<\/div>/i', '', $content );

	// Escaped JS lines WordPress turned into paragraphs.
	$content = preg_replace(
		'/<p(?:\s[^>]*)?>[\s\S]*?(?:createUploadWidget|typeof cloudinary|upload_widget|upload_preview|myWidget\.open)[\s\S]*?<\/p>/i',
		'',
		$content
	);

	return $content;
}

add_filter( 'the_content', 'ath_strip_legacy_comment_upload_markup', 12 );
