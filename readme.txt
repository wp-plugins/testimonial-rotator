=== Plugin Name ===
Contributors: halgatewood
Donate link: http://halgatewood.com/donate/
Tags: testimonials, sidebar, shortcode, testimonial, praise, homage, testimony, witness, appreciation, green light, rotator, rotators, for developers
Requires at least: 3.5
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add Testimonials to your WordPress Blog or Company Website.

== Description ==
Finally a really simple way to manage testimonials on your site. This plugin creates a testimonial and a testimonial rotator custom post type, complete with WordPress admin fields for adding testimonials and assigning them to rotators for display. It includes a Widget and Shortcode to display the testimonials.

It's designed with minimal CSS so it will blend with your theme and can easily be updated to match your theme by your developer.

= Documentation =
Help documents and code snippets can be viewed at http://halgatewood.com/docs/plugins/testimonial-rotator/


= Shortcode examples =
Here are few ways you can include the rotator on your pages and posts.

`Display Testimonials from All Rotators
[testimonial_rotator]

Display Testimonials in Rotator #407
[testimonial_rotator id="407"]

List All Testimonials in Rotator #407
[testimonial_rotator id="407" format="list"]

Limit Results to 10
[testimonial_rotator id="407" format="list" limit=10]

Hide Titles
[testimonial_rotator id="407" hide_title=1]

Randomize Testimonials
[testimonial_rotator id="407" shuffle=1]`


= Example Shortcode HTML =
Here was what the html looks like so you can over ride the styles. Add the .wrap to your CSS selector to override the basic plugin styles.

.testimonial_rotator_wrap .testimonial_rotator { background: red; }

`
<div class="testimonial_rotator_wrap">
	<div id="testimonial_rotator_359" class="testimonial_rotator">
		<div class="slide">
			<h2>Post Title</h2>
			<div class="img">Featured Image</div>
			<div class="text"><p>Testimonial Content</p></div>
		</div>
		<div class="slide">
			<h2>Post Title</h2>
			<div class="img">Featured Image</div>
			<div class="text"><p>Testimonial Content</p></div>
		</div>
	</div>
</div>
`


= Example Widget HTML =
The widget HTML is slightly different than the shortcode version. This allows you to style each one differently.

`
<h3 class="widget-title">Widget Title</h3>

<div class="testimonial_rotator_widget_wrap">
	<div id="testimonial_rotator_widget_sidebar-1" class="testimonial_rotator_widget">
		<div class="slide">
			<blockquote>
				<p>Content</p>
				<cite>- Post Title</cite>	
			</blockquote>
		</div>
		<div class="slide">
			<blockquote>
				<p>Content</p>
				<cite>- Post Title</cite>	
			</blockquote>
		</div>
	</div>
</div>
`



== Installation ==

1. Add plugin to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add a testimonial rotator
1. Create testimonials and specify which rotator you want it to be a part of
1. Add the rotators to your pages using the shortcode or developers can add the placeholders in their themes.


== Frequently Asked Questions ==


= I need more help, where do I go? =
As stated before this plugin has minimal CSS and styling baked in. This allows you to adapt if for your site and theme. If you need some code snippets to enrich your functionality please visit: http://halgatewood.com/docs/plugins/testimonial-rotator/



== Screenshots ==

1. New Sidebar added just for Testimonials
2. Adding a Testimonial Rotator
3. Add a new Testimonial. Uses built-in WordPress functionality like excerpt, featured images and menu order
4. A Testimonial Rotator inserted into a block of text with a shortcode
5. Testimonials have their own page and use the single template they can be customized by making a single-testimonial.php file in your theme.
6. Testimonial widget on the new TwentyFourteen theme
7. New Widget Options (version 1.3+)



== Changelog ==

= 1.4 =
* Use shortcode to display testimonials from all rotators by not passing in an 'id' attribute
* Completed preparation for translation, wrapped all text in __()
* Two new filters for the 'supports' section of the register_post_type: testimonial_rotator_supports and testimonial_rotator_testimonial_supports
* Two new filters for auto-height 'calc': testimonial_rotator_calc and testimonial_rotator_widget_fx

= 1.3.7 =
* Updated icon for WordPress 3.8
* Fixed translation and added languages folder, moved .pot to this folder
* Moved styles and scripts from action wp_head to wp_enqueue_scripts
* Fixed images and order not showing in admin list view
* Prepped for an upcoming PRO version!

= 1.3.6 =
* Fix bug not rotating widget

= 1.3.5 =
* Changed cycle2 to cycletwo as it was conflicting with other plugins

= 1.3.4 = 
* Fixed small bug where some themes were adding extra spaces and breaking the rotator

= 1.3.3 =
* Switched from jQuery Cycle1 to Cycle 2
* Widget now uses Rotator FX and Timeout settings
* Added .testimonial_rotator_widget_blockquote class to widget blockquote to help override some CSS problems with themes.
* Rotator Height is now fixed at the highest testimonial instead of auto adjusting the height

= 1.3.2 =
reset query bug

= 1.3 =
* Randomize testimonials without code
* Hide the title
* Display excerpt or full testimonial in width
* Display specific rotator in widget
* More shortcode examples
* The widget has been updated with all the features as options, no more coding!

= 1.2 =
* main testimonial now uses the_content filter to make styling better.
* include rotator using the rotator slug, for example: [testimonial_rotator id=homepage]
* new attributes to the shortcode: 
** hide_title: hides the h2 heading
** format: settings format=list will remove rotator and display all testimonials
** limit: set the number of testimonials to display, new default is -1 or all testimonials

= 1.1.5 =
* small bug in widget javascript

= 1.1.4 =
* reworking loading of scripts for rotator, should be sorted now.

= 1.1.3 =
* jQuery ready function always

= 1.1.2 =
* Testimonial widget using jQuery ready function instead of window.onload

= 1.1.1 =
* Can't remember, forgot to put this one in (not cool of me, I know)

= 1.1 =
* Small fix to make the testimonial widget fit it's container

= 1.0 =
* Initial load of the plugin.

