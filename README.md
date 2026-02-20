# Share Interactive Content from Spotify - By PulseShare #
**Contributors:** swapnilde  
**Donate link:** https://paypal.me/SwapnilDeshpandeIN  
**Tags:** spotify, podcast, album, embed, audio, video player, wordpress, elementor  
**Requires at least:** 6.6
**Tested up to:** 6.9.1
**Stable tag:** 1.1.2  
**Requires PHP:** 8.0
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

Share interactive content from Spotify on your website seamlessly without any embed codes.

## Description ##

PulseShare provides wordpress blocks and elementor widgets to embed Spotify content on your website. You can embed podcast, an album, or other audio and video content to your website and promote your music, share your new podcast episodes with fans, or highlight your favourite album or playlist.

### How to set up the plugin:- ###

1. First you need to create a free Spotify Developer Account to get the Client ID and Client Secret. You can create a Spotify Developer Account here - https://developer.spotify.com/dashboard/login
2. Go to https://developer.spotify.com/dashboard/applications and Click on Create an app. Fill in the details and click on Create/Save button. Click on the Settings button in top. Copy the Client ID and Client Secret and paste it in the PulseShare settings page. Click on Save Settings button.
3. If you and to integrate albums then Open Spotify and go to the album you want to embed. Now copy the id from the url in the address bar: e.g. If the url is like this - https://open.spotify.com/album/0sNOF9WDwdfwdcDfdPD3Baj then the id is - `0sNOF9WDwdfwdcDfdPD3Baj`. Now copy and paste the id in the PulseShare settings page. Click on Save Settings button.
4. If you and to integrate podcast Open Spotify and go to the podcast you want to embed. Now copy the id from the url in the address bar: e.g. If the url is like this - https://open.spotify.com/show/0s54Nhd4345F9WDwdfwdcDfd then the id is - `0s54Nhd4345F9WDwdfwdcDfd`. Now copy and paste the id in the PulseShare settings page. Click on Save Settings button.

### NEED SUPPORT ###
Need help with something? Have an issue to report? Visit [Plugin’s Forum](https://wordpress.org/plugins/pulseshare/ "Plugin’s Forum").

OR

Get in touch with us on [EMAIL](mailto:pulseshare@swapnild.com)

Made with love by [Swapnil Deshpande](https://swapnild.com "Swapnil Deshpande")!

## Installation ##

1. Upload the plugin files to the `/wp-content/plugins/pulseshare` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->PulseShare screen to configure the plugin.

## Frequently Asked Questions ##

### Does this plugin support gutenberg? ###
Yes, this plugin supports gutenberg. You can use the PulseShare blocks in gutenberg.
    - Blocks available in gutenberg:
        - Podcast & Episodes
        - Album & Tracks

### Does this plugin require or support Elementor? ###
No, this plugin works without Elementor as well. But if you want to use it with Elementor, you can use the PulseShare widgets in Elementor.
    - Widgets available in Elementor:
        - Podcast & Episodes
        - Album & Tracks

### Does this plugin require Elementor Pro? ###
No, this plugin works with Elementor Free as well.

### Does this plugin require Spotify Developer Account? ###
Yes, you need to create a Spotify Developer Account to get the Client ID and Client Secret. You can create a Spotify Developer Account here - https://developer.spotify.com/dashboard/login

### How to get Spotify Client ID and Client Secret? ###
1. Go to https://developer.spotify.com/dashboard/applications
2. Click on Create an app
3. Fill in the details and click on Create/Save button.
4. Click on the Settings button in top.
5. Copy the Client ID and Client Secret and paste it in the PulseShare settings page
6. Click on Save Settings button.

### How to get Spotify Album ID? ###
1. Open Spotify and go to the album you want to embed
2. Now copy the id from the url in the address bar:
    - e.g. If the url is like this - https://open.spotify.com/album/0sNOF9WDwdfwdcDfdPD3Baj
    - Then the id is - 0sNOF9WDwdfwdcDfdPD3Baj
4. Now copy and paste the id in the PulseShare settings page
5. Click on Save Settings button.

### How to get Spotify Podcast ID? ###
1. Open Spotify and go to the podcast you want to embed
2. Now copy the id from the url in the address bar:
    - e.g. If the url is like this - https://open.spotify.com/show/0sNOF9WDwdfwdcDfdPD3Baj
    - Then the id is - 0sNOF9WDwdfwdcDfdPD3Baj
4. Now copy and paste the id in the PulseShare settings page.
5. Click on Save Settings button.


## Changelog ##

### 1.1.2 ###
* Improvement: Streamlined settings storage for better reliability.
* Improvement: Optimized settings page performance.
* Dev: Internal code cleanup.

### 1.1.1 ###
* Improvement: Spotify API error handling and user feedback in blocks and Elementor widgets.
* Fix: Standardized ID comparison in block editors to fix single item selection.

### 1.1.0 ###
* New: Server-side REST API proxy for Spotify — API credentials are no longer exposed to the browser.
* New: AES-256-CBC encryption for the Spotify client secret.
* New: Configurable Spotify market option for API requests.
* New: Append utm_source to all Spotify embed URLs.
* Improvement: Converted block editor components from class to functional components.
* Improvement: Standardized namespace capitalization across the plugin.
* Improvement: Comprehensive error handling and validation for Spotify API calls.
* Improvement: Updated block API version to 3.
* Improvement: Updated Album widget icon.
* Improvement: Added .distignore for cleaner plugin distribution.
* Improvement: Updated CI/CD workflows with Node.js build steps.
* Improvement: Compatibility with latest WordPress and Elementor versions.
* Fix: Widget registration now requires both non-empty API keys and Elementor.
* Fix: Plugin options and cached access token are properly deleted on uninstall.
* Fix: Corrected malformed CSS closing tag in options panel.
* Fix: Corrected typo in settings error message ID.
* Dev: Cleaned up unused dependencies.
* Dev: Added PHPCS ignore comments for base64 functions and unused parameters.

### 1.0.2 ###
* Improvement: Compatibility with latest WordPress version.
* Improvement: Compatibility with latest Elementor version.
* Fix: Spotify embed URLs in album block.
* Fix: Handle potential undefined index for options.

### 1.0.1 ###
* Improvement: Compatibility with latest WordPress version.
* Improvement: Compatibility with latest Elementor version.

### 1.0.0 ###
* Initial release

## Upgrade Notice ##