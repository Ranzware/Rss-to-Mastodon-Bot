Certainly! Below is a clear and concise **README description** for your GitHub repository. This description explains how to use the script, its features, and how to set it up.

---

# Mastodon RSS Feed Poster

This PHP script fetches items from an RSS feed and posts them to Mastodon. It ensures no duplicate posts are made and allows for customization, such as adding "Read More" before links and extracting hashtags from the title of the RSS feed item.

## Features

- **Fetch RSS Feed**: Fetches items from a specified RSS feed URL.
- **Post to Mastodon**: Posts the fetched item to Mastodon using the Mastodon API.
- **Avoid Duplicates**: Tracks posted items using GUIDs to avoid duplicate posts.
- **Customizable Hashtags**: Automatically extracts hashtags from the title of the RSS feed item.
- **One Post Per Run**: Posts only one new item per script execution, making it suitable for frequent runs (e.g., via cron jobs).

## Prerequisites

- **PHP**: Ensure PHP is installed on your system.
- **Mastodon Access Token**: Generate an access token from your Mastodon instance by creating an application in your account settings.
- **RSS Feed URL**: The URL of the RSS feed you want to fetch.

## Setup

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/mastodon-rss-feed-poster.git
   cd mastodon-rss-feed-poster
   ```

2. **Edit the Script**:
   Open the `script.php` file and update the following variables with your details:
   - `$mastodonInstance`: Replace with your Mastodon instance URL (e.g., `https://mastodon.social`).
   - `$accessToken`: Replace with your Mastodon access token.
   - `$rssFeedUrl`: Replace with the URL of the RSS feed you want to fetch.

3. **Run the Script**:
   Execute the script from the command line:
   ```bash
   php script.php
   ```

4. **Automate with Cron** (Optional):
   To run the script automatically at regular intervals, add a cron job. For example, to run the script every hour:
   ```bash
   0 * * * * /usr/bin/php /path/to/your/script.php
   ```

## How It Works

1. The script fetches the RSS feed and parses it using PHP's `SimpleXML`.
2. It checks if each item has already been posted by comparing GUIDs stored in a text file (`posted_guids.txt`).
3. If the item is new, it extracts hashtags from the title (e.g., `#Tech`, `#News`) and appends them to the post.
4. The script posts the item to Mastodon with the following format:
   ```
   Title of the RSS Item

   Description of the RSS Item

   Read More: https://example.com/link

   #ExtractedHashtags
   ```
5. After posting, the script records the GUID of the posted item to avoid duplicates in future runs.

## Example Output

If the RSS feed item has:
- Title: `New Tech Release #Tech #Innovation`
- Description: `This is a description of the new tech release.`
- Link: `https://example.com/link`

The script will post the following to Mastodon:
```
New Tech Release #Tech #Innovation

This is a description of the new tech release.

Read More: https://example.com/link

#Tech #Innovation
```

## Customization

- **Change Hashtag Extraction**: Modify the `extractHashtags()` function in the script to customize how hashtags are extracted.
- **Modify Post Format**: Edit the `$status` variable in the script to change the format of the Mastodon post.
- **Change GUID Storage**: Replace the `posted_guids.txt` file with a database if you prefer a more robust solution.

## License

This project is licensed under the GNU General Public License v3.0 - see the LICENSE file for details.
