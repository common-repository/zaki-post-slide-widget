=== Zaki Post Slide Widget ===
Contributors: r.conte
Donate link: http://www.zaki.it
Tags: posts, slider, bxslider, scroller, news
Requires at least: 3.3
Tested up to: 3.5.2
Stable tag: 1.3.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Widget that allows you to create a simple slider of posts using the jQuery library bxSlider v4 (http://bxslider.com).

== Description ==

Widget that allows you to create a simple slider of posts using the jQuery library [bxSlider v4](http://bxslider.com). You can choose from categories and custom post-type, set the number of posts to show, the number of posts for each block, show post-date, post-thumb (with custom size), custom additional widget class, post ordering, show archive link, text length, time and animation of scroll.

To customize the widget you can refer to this CSS containers:
`
.zakiPostSlideWidgetScroll {}
.zakiPostSlideWidgetPager {}
.zakiPostSlideWidgetArchive {}
`

== Installation ==

1. Unzip and upload the plugin in your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Drag & Drop the widget in your sidebar and customize your options

== Frequently asked questions ==

= Will be provided for new features? =
Not at the moment ;) however, any further requests will be appreciated.

= Need more bxSlider setting? =
Yes we know, bxSlider is full of settings! We have implemented the most used, in case you need a setting in particular please email us and we will put in the next release.

== Changelog ==

= 1.3.3 =
* (Upgrade) bxSlider upgraded to version 4.1.1
* (Minor Change) Plugin fully translated in English

= 1.3.2 =
* (Add) Added "Random" choice for orderby
* (Add) Added "Link on image" setting

= 1.3.1 =
* (Add) Added a setting for archive link in title
* (Minor Change) Renamed handle of wp_enqueue_script with "jquery.bxslider" for more compatibility with other plugin that use the same library

= 1.3 =
* (Add) Now for each widget's instance you can set a custom class

= 1.2.1 =
* (Bug fix) Fixed the bug on the inclusion of the library bxSlider
* (Minor Change) Simplified the CSS schema with 3 containers main

= 1.2 =
* (Upgrade) bxSlider upgraded to version 4.1
* (Add) Added post ordering

= 1.1 =
* (Add) Added post-thumb support with custom and native image size

= 1.0 =
* First release of the widget

