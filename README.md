# Magic Login (wv-login)

## Plugin Details
* Contributors: vincent.bourgonje
* Tags: login, email
* Requires at least: 5.0
* Tested up to: 5.7.1
* Stable tag: 5.7.1
* License: GPLv2 or later License URI: https://www.gnu.org/licenses/gpl-2.0.html

This is a Wordpress plugin that allows users to login by requesting a loginlink per e-mail. 

## Description
Creating a relative save way for existing users to login via a link send per e-mail. To login with a link send by email you will need a valid account on the website. You can restrict the roles that are allowed to use this kind of login. A login token will be valid for only two hours and will be destroyed after using it. So it can be used only once. After that the user needs to request a new loginlink. 

This plugin does only handle the login part but it does not add any security features to secure pages or other content. So it will be used most of the times in combination with other plugins that can lock down your website for anonymous users.

## Installation

1. Upload the plugin zip file with the Wordpress plugin upload and installation feature 
1. Activate the plugin through the 'Plugins' menu in WordPress
1. After activation you will find the basic settings under the main Wordpress settings > WV Login
   1. In the emailtemplate you can use the following tokens:
   1. `#LINK#` Login url - you need to add the right html to make it work
   1. `#FIRSTNAME#` User firstname 
   1. `#LASTNAME#` User lastname
   1. `#SENDER#` Full name of the sender (Sendername can be configured in the settings screen as well)
1. Be sure your e-mail will be delivered, if neccesary install an additional SMPT plugin
1. Create a page and add the shortcode `[WVLOGIN]`

## Changelog
You will find the complete changelog on [Github](https://github.com/vincentbourgonje/wv-login/commits/main)

