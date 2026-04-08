=== NP Lead Chatbot ===
Contributors: nitspatel
Tags: lead generation, chatbot, contact form, leads, floating widget
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A beautiful floating lead-capture chatbot for WordPress. Collect visitor details, manage leads in your dashboard, and export to CSV - no coding needed.

== Description ==

**NP Lead Chatbot** adds a polished, animated floating chat widget to your WordPress site that captures visitor name, email, phone, and message - then stores every lead directly in your WordPress admin.

No third-party services. No monthly fees. No data leaves your server.

= Key Features =

* **Floating chat widget** - animated slide-up popup triggered by a fixed button in the corner of every page
* **Inline shortcode** - embed the form anywhere with `[npleadchat]`
* **Leads dashboard** - view, search, sort, and manage all leads from WP Admin → NP Lead Chatbot
* **CSV export** - download all leads as a spreadsheet with one click
* **Bulk actions** - select and delete multiple leads at once
* **Search & sort** - find leads by name, email, phone, or message; sort by name or date
* **Client-side validation** - instant feedback before form submission
* **REST API powered** - uses the WordPress REST API with nonce verification
* **GDPR-friendly** - all data stored locally in your own database, no external services
* **Accessibility ready** - ARIA labels, roles, keyboard navigation, and Escape-to-close support
* **Lightweight** - ~14 KB CSS, ~4 KB JS, no jQuery UI or heavy dependencies beyond jQuery

= How It Works =

1. A visitor clicks the floating chat button in the corner of your site
2. A smooth animated popup appears with a contact form
3. The visitor fills in their details and clicks Send
4. The lead is saved to your WordPress database instantly
5. You see the new lead in WP Admin → NP Lead Chatbot

= Shortcode =

Use `[npleadchat]` to embed the form inline in any page, post, or widget area.

= Privacy & GDPR =

This plugin stores lead data only in your own WordPress database. It makes no external HTTP requests and loads no resources from third-party servers. Font files are bundled locally.

== Installation ==

= Automatic installation =

1. Log in to your WordPress admin and go to **Plugins → Add New**
2. Search for **NP Lead Chatbot**
3. Click **Install Now**, then **Activate**

= Manual installation =

1. Download the plugin ZIP file
2. Go to **Plugins → Add New → Upload Plugin**
3. Choose the ZIP file and click **Install Now**
4. Click **Activate Plugin**

= After activation =

* The floating chat widget appears automatically on all pages — no configuration needed
* Go to **WP Admin → NP Lead Chatbot** to view collected leads
* Use the `[npleadchat]` shortcode to embed the form inline on any page

== Frequently Asked Questions ==

= Does this plugin work with any WordPress theme? =

Yes. The widget uses fixed positioning and high z-index values, so it works with any theme without conflicts.

= Where are leads stored? =

All leads are stored in a custom database table (`wp_npleadchat_leads`) in your own WordPress database. No data is sent to any external server.

= How do I view my leads? =

Go to **WP Admin → NP Lead Chatbot**. You will see a table of all submitted leads with search, sort, and bulk-delete options.

= How do I export leads? =

In the leads dashboard, click the **Export Leads** button in the top right. This downloads all leads as a `.csv` file.

= Can I embed the form in a page instead of using the floating widget? =

Yes. Add the shortcode `[npleadchat]` to any page, post, or widget area.

= Can I change the colors or text? =

Color and text customization via the admin settings panel is planned for a future release. You can also override styles using your theme's custom CSS.

= Does this plugin slow down my site? =

No. The CSS (~14 KB) and JS (~4 KB) are only loaded when needed, and the font files (~14 KB each) are bundled locally — no external requests.

= Is this plugin GDPR compliant? =

The plugin itself makes no external requests and stores data only in your own database. Whether your overall data collection practices are GDPR compliant depends on how you use the collected lead data. We recommend adding a privacy notice to your site explaining how you handle visitor data.

= What happens to the database table if I uninstall the plugin? =

The plugin includes an `uninstall.php` file that removes the custom database table when the plugin is deleted from the Plugins screen.

== Screenshots ==

1. The floating chat widget open on a page - gradient header, modern inputs, send button
2. The floating trigger button with pulse animation
3. WP Admin leads dashboard - table with search, sort, bulk actions, and CSV export
4. Successful form submission with animated confirmation message
5. Inline shortcode embed with gradient header

== Changelog ==

= 2.0.0 =
* Complete UI redesign - gradient header, DM Sans font (bundled locally), animated popup, modern inputs with focus states
* Added floating button toggle animation (chat icon ↔ close icon)
* Added slide-up open / scale-down close popup animation
* Added loading spinner on submit button during form submission
* Added styled success and error response messages with icons
* Added Escape key and outside-click to close popup
* Added ARIA labels and roles for accessibility
* Replaced Google Fonts external request with locally bundled woff2 font files
* Refactored frontend PHP - shared form field helper, proper label elements, autocomplete attributes
* Improved inline validation - error state clears as user types
* Added `[npleadchat]` inline embed with its own gradient header

= 1.2.0 =
* Added Export Leads (CSV) feature in the admin panel
* Improved admin UI
* Minor performance improvements

= 1.1.0 =
* Added bulk actions support in admin listing
* Added search functionality in admin listing
* Added column sorting in admin listing
* Improved UI and performance

= 1.0.0 =
* Initial release with lead capture form and admin dashboard

== Upgrade Notice ==

= 2.0.0 =
Major UI redesign. The floating widget and inline shortcode form now have a professional gradient header, smooth animations, and modern input styling. No database changes - safe to upgrade.

= 1.2.0 =
Adds CSV export for leads. Safe to upgrade.
