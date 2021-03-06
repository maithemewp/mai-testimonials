# Changelog

## 2.2.0 (5/11/21)
* Changed: Post type args now explicitely force no archive or single view. If you need either view you need to use `mai_testimonial_args` filter to change everything how you want it.

## 2.1.0 (3/2/21)
* Added: Testimonials now use the block editor for content.
* Changed: Testimonials now output full content, including blocks and shortcodes (requires Mai Engine 2.11).

## 2.0.3 (2/13/21)
* Added: Mai logo icon to updater.

## 2.0.2 (1/5/21)
* Fixed: Mai Post Grid block still linking testimonials when post type is private.

## 2.0.1 (12/11/20)
* Changd: Plugin header consistency.

## 2.0.0 (12/1/20)
* Added: Support for Mai Theme v2.

## 0.5.3 (12/16/19)
* Changed: Open website links in new tab.
* Changed: Update the updater.

## 0.5.2
* Added: Add 'page-attributes' support to post type so it's easier to change menu order. Now works with Simple Page Ordering plugin out of the box.

## 0.5.1
* Changed: Only run updater in the admin.
* Fixed: Check [grid] 'content' attribute isset before checking if it's a testimonial.
* Fixed: Remove testimonials from search results.

## 0.5.0
* Changed: Updater script to latest version. Load styles via wp_add_inline_style intead of loading a full CSS file just for a few lines of code.
* Changed: Updater point to new repo location.
* Changed: Reference Mai Theme instead of Mai Pro.

## 0.4.0
* Fixed: Constant name referencing Mai Favorites.

## 0.3.0
* Added: Testimonial Categories to allow displaying testimonials in a specific category via [grid content="testimonial" taxonomy="testimonial_cat" terms="123"].
