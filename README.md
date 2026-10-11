# Last Updated Badge

Displays a "Last updated: [date]" notice on WordPress posts. Only shows when the post has been modified after its original publish date. Date format, label text, position, and post types are all configurable.

## Features
- Only shows when `post_modified > post_date` — never clutters freshly published content
- Configurable label format with `{date}` placeholder
- PHP date format string (with live preview in the settings page)
- Before or after content position
- Per-post-type control
- Semantic `<time>` element with `datetime` attribute for accessibility and SEO

## WordPress Concepts Demonstrated
| Concept | Where Used |
|---|---|
| `the_content` filter | `class-lub-output.php` |
| `get_post_modified_time` vs `get_post_time` | `class-lub-output.php` |
| `wp_date()` with PHP format string | `class-lub-output.php` |
| Settings API | `class-lub-settings.php` |
| `register_activation_hook` | `last-updated-badge.php` |

## Requirements
- WordPress 6.0+ / PHP 8.0+
## License
GPL-2.0-or-later
