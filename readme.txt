=== Store Locator Plus® | Extenders ===
Plugin Name:       Store Locator Plus® | Extenders
Contributors: DeBAAT, freemius
Donate link:       https://www.de-baat.nl/slp/
Tags:              Extenders, Events, Social Media, User Managed Locations
Required PHP:      8.0
Requires at least: 6.0
License:           GPL3
Tested up to:      6.1.1
Stable tag:        6.1.1

Adds power user features like managing location based events, social media information and locations managed by other logged in users to Store Locator Plus®.

== Description ==

[SLP](https://www.storelocatorplus.com/) | [Location and Directory SaaS](https://my.storelocatorplus.com)| [WordPress Plugins](https://wordpress.storelocatorplus.com)  | [Documentation](https://www.de-baat.nl/slp/)

Adds the features needed by power users such as managing location based events, social media information and locations managed by other logged in users.

This is the newest add-on pack that rolls up the features of the legacy Events Location Manager, Social Media Extender and User Managed Locations add-on packs in one convenient package.

= How does it work? =

Ensure you have the right versions of [Store Locator Plus](https://wordpress.storelocatorplus.com) plugin installed.
Add a store user field to the locations in Store Locator Plus and use it to filter for allowed users.

= Features =

Adds the following field to location data:

* Store User

= Additional Premium Features =

Via the [Documentation](https://www.de-baat.nl/slp/) site, you can purchase the premium version to enhance this Extenders add-on even more with the following features:

* Adds an additional field to location data for each social media supported.
* Adds an additional field to location data for each event supported.

== Installation ==

= Requirements =

* Store Locator Plus: 2210.25
* WordPress: 6.0
* PHP: 8.0

= Install After SLP =

1. Go fetch and install [Store Locator Plus](https://wordpress.org/plugins/store-locator-le/).
2. Install this plugin directly from the WordPress org site.

OR

2. Download this plugin from the WordPress org site to get the latest .zip file.
3. Go to plugins/add new.
4. Select upload.
5. Upload the zip file.

== Frequently Asked Questions ==

= What are the terms of the license? =

The license for the free plugin is GPL. You get the code, feel free to modify it as you wish. We prefer that our customers pay us for the Premium version because they like what we do and want to support our efforts to bring useful software to market. Learn more on our [DeBAAT License Terms](https://www.de-baat.nl/general-eula/) page.

= How does the add-on work? =

The add-on adds a new page to the set of SLP configuration pages.
The 'Extenders' page presents the Admin with a number of sections:



The 'User Managed Page' section presents the Admin with a list of configured users.
The manage locations capability of each individual user can be toggled between Allow and Disallow.
Each allowed user will get access to the Locations configuration page of the SLP plugin.
For the Admin, the Locations configuration page shows an additional column with the
user who is allowed to manage that particular location.
Each individual location can be managed by both the Admin and the allowed user.
The Admin also has the capability to change the value of the store user.

The 'Settings Page' section presents the Admin with a list of settings to control the working of this add-on.
It also contains sub-sections to control the settings of the other functionalities presented in the other sections described above.

== Changelog ==

= 6.1.1 =
* Tested to work with WP 6.1.1 and SLP 2210.25.
* Tested to work with PHP 8
* Updated Freemius SDK to V2.5.3
* Fixed new install activation
* Improved selected_nav handling

= 5.9.1 =
* Tested to work with WP 5.9.1 and SLP 5.12.
* Security fix

= 5.9.0 =
* Tested to work with WP 5.9 and SLP 5.12.

= 5.8.0 =
* Updated Freemius SDK to 2.4.2
* Fixed issue with static functions
* Tested to work with WP 5.8.3 and SLP 5.12.

= 5.6.0 =
* Renamed display name to Store Locator Plus® - Extenders
* Moved UserManager functionality to the Admin Users page
* Fixed handling of options after update


= 5.5.2 =
* Fixed handling updates.
* Improved languages.

= 5.5.1 =
* Fixed directory handling.
* Improved validation of input processing.

= 5.5.0 =
* Started SLP Extenders as a free implementation for User Managed Locations.

= 5.4.1 =
* Fixed loading of menu items
* Updated Freemius SDK

= 5.4.0 =
* Fixed loading of classes

= 5.3.3 =
* Fixed handling of general options

= 5.3.2 =
* Tested against WP and SLP 5.3.2

= 5.1.0 =
* Fixed handling of Freemius
* Added text to this readme

= 5.0.2 =
* Fixed handling of Freemius update method

= 5.0.1 =
* Fixed handling of Event start and end_date

= 5.0.0 =
* Started as a replacement for SME, UML, ELM
* Requires SLP 5.0.0 or higher
* Tested with WP 5.0.3
