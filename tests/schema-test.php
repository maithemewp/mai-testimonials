<?php
/**
 * Dependency-free smoke test for the testimonial JSON-LD schema path.
 *
 * The schema builder/renderer has no WordPress coupling beyond a handful
 * of helper functions, so we stub those and assert the output directly.
 * This pins the two regressions this code has already had: the
 * aggregateRating average math and the silently-dropped Grid block path.
 *
 * Run: php tests/schema-test.php
 * Exits non-zero on the first failed assertion.
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['render_schema'] = true;
$GLOBALS['post_content']  = '';

function add_action( ...$a ) {}
function apply_filters( $tag, $value ) {
	if ( 'mai_testimonials_render_schema' === $tag ) {
		return $GLOBALS['render_schema'];
	}
	return $value;
}
function get_bloginfo( $key ) { return 'Acme Co'; }
function get_the_title( $post ) { return $post['title']; }
function get_the_date( $format, $post ) { return $post['date']; } // Stubbed as already-ISO.
function get_the_content( $post = null ) { return $GLOBALS['post_content']; }
function do_blocks( $content ) { return $content; }
function wp_strip_all_tags( $string ) { return trim( preg_replace( '/<[^>]*>/', '', $string ) ); }
function wp_json_encode( $data ) { return json_encode( $data, JSON_UNESCAPED_SLASHES ); }
function absint( $v ) { return abs( (int) $v ); }

require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/schema.php';

$failures = 0;

function check( $label, $condition ) {
	global $failures;
	if ( $condition ) {
		echo "PASS  $label\n";
	} else {
		echo "FAIL  $label\n";
		$failures++;
	}
}

function render() {
	ob_start();
	mai_render_testimonials_schema();
	return ob_get_clean();
}

function decode( $html ) {
	return json_decode( preg_replace( '#^<script[^>]*>|</script>$#', '', $html ), true );
}

// 1. No reviews cached -> no output.
check( 'empty cache renders nothing', '' === render() );

// 2. Collect two reviews (mirrors add_review() / entry_schema()).
$GLOBALS['post_content'] = "<p>Great <a href='#'>service</a>!</p><script>evil()</script>  Loved   it.";
mai_testimonials_get_schema( mai_testimonials_get_review_schema( [ 'title' => 'Jane Doe', 'date' => '2026-05-19T15:09:44-04:00' ] ) );
$GLOBALS['post_content'] = 'Solid product.';
mai_testimonials_get_schema( mai_testimonials_get_review_schema( [ 'title' => 'John Roe', 'date' => '2026-01-02T09:00:00-04:00' ] ) );

$html = render();
$json = decode( $html );

check( 'single ld+json script', 1 === substr_count( $html, '<script' ) );
check( 'Organization @type', 'Organization' === $json['@type'] );
check( 'aggregateRating ratingValue is the average (5)', 5 === $json['aggregateRating']['ratingValue'] || 5.0 === $json['aggregateRating']['ratingValue'] );
check( 'aggregateRating bestRating is 5 (not summed)', 5 === $json['aggregateRating']['bestRating'] );
check( 'aggregateRating ratingCount is 2', 2 === $json['aggregateRating']['ratingCount'] );
check( 'every review has datePublished', '2026-05-19T15:09:44-04:00' === $json['review'][0]['datePublished'] && '2026-01-02T09:00:00-04:00' === $json['review'][1]['datePublished'] );
check( 'reviewBody is plain text (no markup)', false === strpos( $json['review'][0]['reviewBody'], '<' ) );
check( 'reviewBody strips script content + collapses whitespace', 'Great service! Loved it.' === $json['review'][0]['reviewBody'] );

// 3. Disable filter suppresses all output even with reviews cached.
$GLOBALS['render_schema'] = false;
check( 'mai_testimonials_render_schema=false renders nothing', '' === render() );

echo $failures ? "\n$failures FAILED\n" : "\nAll passed\n";
exit( $failures ? 1 : 0 );
