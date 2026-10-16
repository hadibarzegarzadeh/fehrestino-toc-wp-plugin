# Fehrestino - Table of Contents for WordPress

Fehrestino is a WordPress plugin that automatically builds a clean, interactive table of contents from the headings of your posts and pages.

## Features

- Automatic table of contents generated from H2 to H6 headings
- Choose exactly which heading levels are included
- Respects heading hierarchy with nested lists
- Four ready-made styles: Neo, Minimal, Gradient and Card
- Custom accent color
- Optional heading numbering
- Smooth scrolling and scrollspy that highlights the section you are reading
- Collapsible box with a configurable default state (open or collapsed)
- Minimum number of headings required before the table is shown
- Automatic insertion into post content, or manual placement with a shortcode
- Works on posts, with an option to enable it on pages
- Admin settings page with live theme previews
- Interface languages: Persian and English, with translation files included

## Installation

1. Download this repository as a ZIP file.
2. In WordPress, go to Plugins > Add New > Upload Plugin and upload the ZIP.
3. Activate the plugin.
4. Open the Fehrestino settings page in the admin menu to configure it.

You can also copy the plugin files into `wp-content/plugins/fehrestino/` manually.

## Usage

By default the table of contents is inserted automatically into single posts that contain enough headings.

To place it manually, turn off automatic insertion in the settings and add the shortcode where you want it:

```
[fehrestino]
```

## Settings

| Option | Description |
| --- | --- |
| Title | Heading shown above the table of contents |
| Heading levels | Which of H2-H6 are listed |
| Theme | Neo, Minimal, Gradient or Card |
| Accent color | Main color of the box |
| Numbering | Show numbers next to entries |
| Default state | Open or collapsed on page load |
| Smooth scroll | Animated scrolling to the heading |
| Scrollspy | Highlight the active section while scrolling |
| Enable on pages | Also show the table on static pages |
| Auto insert | Insert automatically or use the shortcode |
| Minimum headings | Hide the table when there are fewer headings |
| Language | Plugin interface language |

## Requirements

- WordPress 5.0 or later
- PHP 7.2 or later

## Structure

```
fehrestino.php
admin/
assets/
includes/
languages/
public/
```
