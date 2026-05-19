<?php

add_action( 'wp_footer', 'mai_render_testimonials_schema' );
/**
 * Renders Organization + Review schema from collected testimonial data.
 *
 * Builds a single Organization object containing an aggregateRating and the
 * nested reviews collected during this request, so multiple testimonial blocks
 * on one page produce one consolidated, valid JSON-LD script.
 *
 * @since 2.7.4
 *
 * @return void
 */
function mai_render_testimonials_schema() {
	/**
	 * Allow disabling all testimonial schema output.
	 *
	 * @since 2.7.4
	 *
	 * @param bool $render Whether to render the schema.
	 */
	if ( ! apply_filters( 'mai_testimonials_render_schema', true ) ) {
		return;
	}

	$reviews = mai_testimonials_get_schema();

	if ( ! $reviews ) {
		return;
	}

	$sum   = 0;
	$count = count( $reviews );

	foreach ( $reviews as $review ) {
		$sum += absint( $review['reviewRating']['ratingValue'] );
	}

	$schema = [
		'@context'        => 'https://schema.org/',
		'@type'           => 'Organization',
		'name'            => get_bloginfo( 'name' ),
		'aggregateRating' => [
			'@type'       => 'AggregateRating',
			'ratingValue' => round( $sum / $count, 1 ),
			'bestRating'  => 5,
			'ratingCount' => $count,
		],
		'review' => $reviews,
	];

	$schema = apply_filters( 'mai_testimonials_schema', $schema );

	printf( '<script type="application/ld+json">%s</script>', wp_json_encode( $schema ) );
}
