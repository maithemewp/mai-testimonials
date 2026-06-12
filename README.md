# Mai Testimonials

​1. Install/activate.
1. Add Testimonials via Dashboard > Testimonials > Add New.
1. Create a "Page" called "Testimonials".

## Mai Theme v2
1. Add a Mai Testimonials block (or Mai Post Grid block set to display Testimonials on the Entries tab) and modify the settings as you'd like.

## Mai Theme v1/Classic
1. Add a shortcode [grid content="testimonial" number="all" columns="2"] and modify grid parameters as you'd like.
1. To use grid as a testimonial slider (works great in the Sections template), use [grid content="testimonial" columns="1" slider="true"].

## Filters

### `mai_testimonials_render_schema`
Disable all testimonial schema (JSON-LD) output. Return `false` to prevent the consolidated Organization/Review schema from being rendered.

```php
/**
 * Disable all Mai Testimonials schema output.
 *
 * @param bool $render Whether to render the schema. Default true.
 *
 * @return bool
 */
add_filter( 'mai_testimonials_render_schema', '__return_false' );
```

### `mai_testimonials_schema`
Modify the schema array before it's output, instead of disabling it entirely.

```php
/**
 * Filter the Mai Testimonials schema array before output.
 *
 * @param array $schema The JSON-LD schema array.
 *
 * @return array
 */
add_filter( 'mai_testimonials_schema', function( $schema ) {
	// Modify $schema as needed.
	return $schema;
} );
```
