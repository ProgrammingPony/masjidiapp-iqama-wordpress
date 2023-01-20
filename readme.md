# MasjidiApp Athan and Iqama Integration

This is a basic WordPress Plugin that intends to make it easy for Masajid and Musallahs to add a widget for their WordPress site that displays the athan and iqama times.

## What is MasjidiApp?
MasjidiApp provides a rich system for masajid to customize the athan times they use based on well-known calculation methods. It also has different options for setting iqama times. [Site to add and administers masajid](https://admin.masjidiapp.com/). You can read more about MasjidiApp [here](https://stream.masjidiapp.com/masjidiapp/2021/02/16/masjidi-api/).

> **Note:** The owner of this Plugin is not affiliated with UmmahSoft. For any inquiries related to managing your masjid outside this WordPress plugin please review instructions on [this page](https://stream.masjidiapp.com/masjidiapp/2021/02/16/masjidi-api/).

## How to Use
### Setting Defaults Parameters
After activating this plugin, you will find a new administrator menu `Settings` → `MasjidiApp Iqama`. This menu allows you to set default values which will be used with shortcodes whenever their corresponding shortcode parameters are absent. Setting these defaults are _not required_ for the widget to work.

> Supported defaults are for the _Api Key_ and _Masjid Id_.

### Widget Usage
The shortcode tag `masjidiapp_current_iqama_times` is used to display the widget. It has the following available parameters (not case-sensitive):

| Attribute Name | Default | Description | 
| -------------- | ------- | ----------- |
| `ShowAthan` | `true`  | Displays athan times in the widget as well. Iqama is always displayed. |
| `MasjidId` | n/a | MasjidiAPI ID assigned to the masjid or musallah. This is what we used to get the timings of the specific masjid. |
| `ApiKey` | n/a | MasjidiAPI api key assigned to the masjid or musallah. This is not currently in use, but is required by UmmahSoft and may be required in a future release. To avoid interuption in a future update for the widget to work. Try to get this as soon as you can. |

Example usages

| Shortcode | Description |
| --------- | ----------- |
| `[masjidiapp_current_iqama_times]` | Displays both the athan and iqama times using the default masjid id and api key specified in `Settings` → `MasjidiApp Iqama` admin menu. |
| `[masjidiapp_current_iqama_times ShowAthan=false MasjidId="53368" ApiKey="api-kee"]` | Displays only the iqama times of the masjid with ID `53368` and API key `api-kee` (which is not really an API key). |

## Support
[GitHub's issue tracking system](https://github.com/ProgrammingPony/masjidiapp-iqama-wordpress/issues) will be used to track all requests (feature requests, bugs, security, etc). Before creating a new issue check if there is an existing issue that discusses what you wanted to add first. If it exists, please add a reaction to the original post to indicate that you are impacted by this issue (for bugs) or that you are anticipating the same requested feature (enhancements). The reactions help us prioritize the most impactful bugs to address and features to implement.

> This is a free plugin. Contributors could be active on other projects as well, so responses could be delayed.

### Bugs
Try to provide as much relevant details as you can to help us reproduce, or investigate the problem. This could include some mixture of:
* Clear steps to reproduce, with applicable parameters where available (ex. MasjidId, ApiKey)
* Error stack trace from the logs where our code is referenced. You can enable [WP Debugging plugin](https://wordpress.org/plugins/wp-debugging/) to have error displayed on page. Do not keep this plugin active when it is no longer required in production environment for security purposes.

### Feature Requests / Enhancements
Ensure to include what problem that is intended to be solved with the issue so that we can discuss/consider alternatives that might solve the problem better.