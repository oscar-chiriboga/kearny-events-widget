# Kearny Events Widget

A custom WordPress plugin that adds an Elementor widget and shortcode for displaying upcoming events from The Events Calendar with featured images.

## Requirements

- WordPress 6.0+
- PHP 7.4+
- The Events Calendar (free or Pro)
- Elementor (for the widget; shortcode works without Elementor)
- The Events Calendar: Community Events (optional, for the Submit an Event button)

## Installation

1. Zip the `kearny-events-widget` folder.
2. In WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Upload the zip, click Install, then Activate.

## Using the Elementor widget

1. Edit a page in Elementor.
2. In the widget panel, search for **Kearny Events**.
3. Drag it into the section where you want the events to appear.
4. Configure in the left panel:
   - **Events Query** — number of events (default 8), category filter
   - **Layout** — carousel or grid, columns (responsive), image aspect ratio
   - **Display Fields** — toggle image, date, venue, excerpt
   - **Submit Event Button** — toggle, text, URL, alignment, logged-in only option
   - **More Events Button** — toggle, text, URL (defaults to `/events`)
   - **Placeholder Card Colors** — 5 colors that cycle on cards with no featured image
   - **Style** tab — card background, gap, title color/typography, button colors, submit button style

## Using the shortcode

Drop into any post, page, or Elementor Shortcode widget:

```
[kearny_events]
```

With custom options:

```
[kearny_events count="8" columns="4" layout_mode="carousel" show_venue="yes"]
```

### Shortcode attributes

| Attribute | Default | Description |
|-----------|---------|-------------|
| count | 8 | Number of events to show |
| columns | 4 | Cards visible (1–4) |
| category | (empty) | Event category slug filter |
| layout_mode | carousel | `carousel` or `grid` |
| show_image | yes | Show featured image / placeholder card |
| show_date | yes | Show event date/time |
| show_venue | no | Show venue name |
| show_excerpt | yes | Show excerpt |
| excerpt_length | 20 | Excerpt word count |
| image_ratio | 1/1 | Aspect ratio (4/3, 16/9, 1/1, 3/2, 3/4) |
| show_arrows | yes | Show prev/next arrows on carousel |
| show_button | yes | Show "View All Events" button |
| button_text | View All Events | Button label |
| button_url | /events | Button destination |
| show_submit_button | yes | Show "Submit an Event" button |
| submit_button_text | Submit an Event | Submit button label |
| submit_button_url | /events/community/add/ | Submit button destination |
| submit_button_align | right | `left`, `center`, or `right` |
| submit_logged_in_only | no | Only show submit button to logged-in users |
| empty_message | No upcoming events. Check back soon! | Message when no events exist |

## How it works

The widget queries the `tribe_events` custom post type directly with a meta query on `_EventEndDate` to hide past events, sorted by `_EventStartDate` ascending. Results are cached in a transient for 10 minutes and automatically cleared whenever an event is saved or deleted. It does not override any plugin templates, so it will survive updates to The Events Calendar without breaking.

## Placeholder cards

When an event has no featured image, the widget automatically generates a styled card using a rotating palette of background colors with the event category and title displayed as text — similar to the reference design. The 5 palette colors are fully customizable in the Elementor panel under **Placeholder Card Colors**.

## Customization

All styling uses CSS custom properties on `.kearny-events`. Override in your theme's custom CSS:

```css
.kearny-events {
    --kearny-accent: #3a88c9;
    --kearny-accent-dark: #2d6fa8;
    --kearny-text: #111;
    --kearny-muted: #666;
    --kearny-card-radius: 8px;
    --kearny-gap: 20px;
}
```
