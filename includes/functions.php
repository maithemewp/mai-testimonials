<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Enqueues testimonials styles.
 *
 * @access private
 *
 * @since 2.4.0
 *
 * @param bool $preview If admin editor preview or not.
 *
 * @return void
 */
function mai_enqueue_testimonials_styles( $preview = false ) {
	$suffix = mai_testimonials_get_suffix();

	// Block.
	wp_enqueue_style( 'mai-testimonials', MAI_TESTIMONIALS_PLUGIN_URL . "assets/css/mai-testimonials{$suffix}.css", [], MAI_TESTIMONIALS_VERSION . '.' . date( 'njYHi', filemtime( MAI_TESTIMONIALS_PLUGIN_DIR . "assets/css/mai-testimonials{$suffix}.css" ) ) );

	// Editor.
	if ( $preview ) {
		wp_enqueue_style( 'mai-testimonials-editor', MAI_TESTIMONIALS_PLUGIN_URL . "assets/css/mai-testimonials-editor{$suffix}.css", [], MAI_TESTIMONIALS_VERSION . '.' . date( 'njYHi', filemtime( MAI_TESTIMONIALS_PLUGIN_DIR . "assets/css/mai-testimonials-editor{$suffix}.css" ) ) );
		wp_enqueue_script( 'mai-testimonials-editor', MAI_TESTIMONIALS_PLUGIN_URL . "assets/js/mai-testimonials-editor{$suffix}.js", [], MAI_TESTIMONIALS_VERSION . '.' . date( 'njYHi', filemtime( MAI_TESTIMONIALS_PLUGIN_DIR . "assets/js/mai-testimonials-editor{$suffix}.js" ) ), [ 'strategy' => 'defer' ] );
	}
	// Front end.
	else {
		// Slider.
		wp_enqueue_script( 'mai-testimonials', MAI_TESTIMONIALS_PLUGIN_URL . "assets/js/mai-testimonials{$suffix}.js", [], MAI_TESTIMONIALS_VERSION . '.' . date( 'njYHi', filemtime( MAI_TESTIMONIALS_PLUGIN_DIR . "assets/js/mai-testimonials{$suffix}.js" ) ), [ 'strategy' => 'defer' ] );
		wp_localize_script( 'mai-testimonials', 'maiTestimonialsVars',
			[
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'mai_testimonials_slider' ),
			]
		);
	}
}

/**
 * Gets the script/style `.min` suffix for minified files.
 *
 * @access private
 *
 * @since 2.4.0
 *
 * @return string
 */
function mai_testimonials_get_suffix() {
	static $suffix = null;

	if ( ! is_null( $suffix ) ) {
		return $suffix;
	}

	$debug  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$suffix = $debug ? '' : '.min';

	return $suffix;
}

/**
 * Gets the Review schema array for a single testimonial.
 *
 * Single source of truth for the Review schema, shared by the Mai Testimonials
 * block and the Mai Grid block so both paths stay in sync.
 *
 * @access private
 *
 * @since 2.7.4
 *
 * @param WP_Post $post The testimonial post object.
 *
 * @return array
 */
function mai_testimonials_get_review_schema( $post ) {
	$schema = [
		'@type'        => 'Review',
		'reviewRating' => [
			'@type'       => 'Rating',
			'ratingValue' => '5',
		],
		'author' => [
			'@type' => 'Person',
			'name'  => get_the_title( $post ),
		],
		'datePublished' => get_the_date( 'c', $post ), // ISO-8601, satisfies schema.org Date/DateTime.
		'reviewBody'    => mai_testimonials_get_schema_content( $post ),
	];

	return apply_filters( 'mai_testimonials_review_schema', $schema, $post );
}

/**
 * Collects Review schema for the current request.
 * Optionally add a new Review to the static cache.
 *
 * @access private
 *
 * @since 2.7.4
 *
 * @param array $review Array of schema data.
 *
 * @return array
 */
function mai_testimonials_get_schema( $review = [] ) {
	static $cache = [];

	if ( $review ) {
		$cache[] = $review;
	}

	return $cache;
}

/**
 * Gets plain-text schema content from a post for use as reviewBody.
 *
 * Returns plain text (no markup): Google expects reviewBody to be text,
 * not HTML, so all tags are stripped and whitespace collapsed.
 *
 * @access private
 *
 * @since 2.7.4
 *
 * @param WP_post $post The post object.
 *
 * @return string
 */
function mai_testimonials_get_schema_content( $post ) {
	$content = get_the_content( $post );
	$content = do_blocks( $content );
	$content = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $content ); // Strip script and style tags.
	$content = wp_strip_all_tags( $content );
	$content = preg_replace( '/\s+/', ' ', $content ); // Collapse whitespace.
	$content = trim( $content );

	return $content;
}
