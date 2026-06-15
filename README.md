# Require Protocol Plugin for YOURLs

Require Protocol is a YOURLS plugin that ensures original URLs have a valid protocol — and can optionally enforce HTTPS.

#### Features

- Enforces presence of a protocol (`http://` or `https://`)
- Optional: Automatically add `http://` when no protocol is present
- Optional: Automatically add `https://` when no protocol is present
- Optional: Allow only HTTPS
- Optional: Automatically upgrade `http://` → `https://`
- Consistent error messages in the YOURLS admin
- Requires YOURLS 1.10.4+

---

### Installation

1. Download or clone the repository
2. Copy the folder to `user/plugins/yourls-require-protocol/`
3. In YOURLS Admin: **Activate the plugin**

---

### Configuration

All settings can be found at the top of `plugin.php`.

#### Allow only HTTPS
```php
define('REQP_REQUIRE_HTTPS', true);
```

#### Automatically upgrade http:// to https://
```php
define('REQP_UPGRADE_TO_HTTPS', true);
```

#### When no protocol is present: automatically add http://
```php
define('REQP_AUTO_ADD_HTTP', true);
```

#### When no protocol is present: automatically add https://
```php
define('REQP_AUTO_ADD_HTTPS', true);
```

Note: If both `REQP_AUTO_ADD_HTTPS` and `REQP_AUTO_ADD_HTTP` are set to `true`, `https://` takes precedence.

### Examples

| Input              | Setting                                               | Result              |
|--------------------|-------------------------------------------------------|---------------------|
| example.com        | REQP_AUTO_ADD_HTTPS = true                            | https://example.com |
| example.com        | REQP_AUTO_ADD_HTTP = true                             | http://example.com  |
| example.com        | REQP_AUTO_ADD_HTTPS = true, REQP_AUTO_ADD_HTTP = true | https://example.com |
| http://example.com | REQP_UPGRADE_TO_HTTPS = true                          | https://example.com |
| http://example.com | REQP_REQUIRE_HTTPS = true                             | Error               |
| example.com        | no auto-fix option                                    | Error               |

### License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for full license text.

### Support

For issues, feature requests, or contributions, please visit the [GitHub repository](https://github.com/julabo/yourls_require_protocol).
