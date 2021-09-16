=== Visual Builder for Contact Form 7 ===
Contributors: etruel
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GT3UVS8UCAHV8
Tags: visual builder, form builder, visual form, contact, form, contact form, feedback, email, ajax, captcha, akismet, multilingual
Requires at least: 4.1
Tested up to: 5.8.1
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds a Visual Builder and a code highlighter for contact form 7 forms.  ADD-on.  Requires Contact Form 7 Plugin.

== Description ==

Currently Contact Form 7 plugin just allow editing forms with HTML in standard textarea.  This Add-on allows previews, items order with drag and drop, and deletes field items just with a click.

Also adds a HTML code highlighter in the textarea and some selectable themes to choose different colors schemes.

See [Screenshots](https://wordpress.org/plugins/visual-builder-for-contact-form-7/screenshots/) to see simple instructions and get an idea of its appearance.

== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

= Using the Plugin Manager =

1. Click Plugins
2. Click Add New
3. Search for `Contact Form 7 Visual Builder`
4. Click Install
5. Click Install Now
6. Click Activate Plugin
7. Now you must see a Form Preview with Visual Builder options near textarea when edit a form

= Manually =

1. Upload `contact-form-7-visual-builder` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Screenshots ==

1. Standard textarea from Contact Form 7, will be replaced with Visual Builder.  Every field without html tags is reflected with <p> tag in textarea when the form is saved.

2. New areas added after activate the plugin. Now you can see the textarea with code highlighter with some themes to choice.  At right-top cornner an eye to show or hide the visual form fields. 

3. Now you can Drag & Drop to change fields order. (If Adds a new field with above tags, must save to see changes in Visual Builder)

4. After Drop a field you'll see the change also in textarea. You must save Form to save all you have changes.

5. Option to delete a field and its labels just with a click.

6. Click de eye to hide/show the Visual Builder.
 

== Changelog ==

= 2.5 Sep 16, 2021 =
* Tested with WP 5.8.1 and CF7 5.4.2
* New icons and logo :D 
* Tweaks some styes.
* Added .pot catalog and Spanish language files.
* Fixes issue does not show form or visual builder on New Form.
* Updated Author name and URIs.

= 2.4 May 2, 2021 =
* Compatibility with Contact Form 7 v5.4.1 and WordPress v5.7.1
* Adds requirements on activating to avoid errors if CF7 is not present.
* Tweak uses the WordPress wheel gif for reload when refreshing the form.
* Fixes the functionality of the form viewer hiding the reload form.
* Fixes bug when refreshing the form and deleting an item.
* Fixes failure to execute script elements in visual form.
* Fixes javascript errors on contact forms list.
* Fixes javascript errors for cm.theme.options on editing form screen.

= 2.3 =
* Compatibility with Contact Form 7 v4.8 and Wordpress v4.8. 
* Few tweaks on design and cosmetic.

= 2.2 =
* Fixes a Fatal error: Can’t use function return value in write context in some versions of PHP.

= 2.1 =
* Improvement the text editor to works with javascript. You can now change order like any field.
* Improvement the text editor retain the css in the top of the form.
* Tweak, Option to refresh the visual form.
* Tweak, by adding nonces to the AJAX requests.

= 2.0 =
Closest...
* Tested with WP 4.7 and CF7 4.6
* Tweak, the color scheme is now htmlmixed for html, js and css coding.
* Tweak, eye icon is now a wordpress dashicon saving loading time of data images in css.
* Improvement, Visual form view (eye icon) state is now saved on click via ajax.
* Improvement, TextArea code Highlighter have now selectable themes to choose different colors schemes.
* Improvement The color schema is saved via ajax on select.
* Fixes the shortcodes without html tags as in default forms adding p tags.
* New collaborator & new Banners :D

= 1.1 =
Second approach:
Added HTML code highlighter in the textarea.
Added support for "p" and "label" html tags.  (Until now was just p)

= 1.0 =
First approach to Visual Builder for forms from Contact Form 7.

== Upgrade Notice ==
2.5 Bumps to WP 5.8.1.

