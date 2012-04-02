=== Spammer Silent Treatment ===
Contributors: jmdodd
Tags: wp_mail, mail, spammer, user_status, buddypress
Requires at least: 3.3
Tested up to: 3.3.1
Stable tag: 0.1

Remove known spammer email addresses from wp_mail()'s To, Cc, and Bcc fields.

== Description ==

BuddyPress uses the user_status field to classify users as spammers. Users who 
are marked as spammers should not receive further email notifications from the 
system.

This plugin filters the To, Cc, and Bcc fields that are passed to wp_mail().

This plugin is a beta release, and as such, is not suitable for deployment on 
a production server.

== Installation ==

1. Upload the directory `spammer-silent-treatment` and its contents to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==

= 0.2 =
* Fix readme.txt Markdown.

= 0.1 =
* Initial release. 

== Upgrade Notice ==

= 0.2 =
* readme.txt update.

= 0.1 = 
* Initial release.

== Credits ==

Development funded, in part, by Ariel Meadow Stallings and the Offbeat Empire.
