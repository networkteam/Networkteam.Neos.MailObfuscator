# Neos MailObfuscator

In order to make life for spammers more difficult, this package provides an obfuscation of email addresses.
The email address is obfuscated by a rot13 like algorithm with random offsets.

When the link is clicked, the email address is unobfuscated by the same algorithm in JavaScript:

```html
<a href="mailto:foo@example.com">foo@example.com</a>
```

will become

```html
<a href="javascript:linkTo_UnCryptMailto('obfuscatedEmail', -randomNumber)">foo (at) example.com</a>
```

The replacement is done in 2 steps, thus it is possible to have a link label that is different from the email address:

```html
<a href="mailto:foo@example.com">Contact us</a>
```

will become

```html
<a href="javascript:linkTo_UnCryptMailto('obfuscatedEmail', -randomNumber)">Contact us</a>
```

## Installation

Install the composer package in your site package or distribution:

```shell
$ composer require networkteam/neos-mailobfuscator
```

There is no need for configuration, as a Fusion processor is attached to `body` of `Neos.Neos:Page`.
That means, that the complete content of body tag is obfuscated.

### Compatibility

See the following table for the correct plugin version to choose:

| Neos CMS | Plugin version |
| -------- | -------------- |
| >= 3.0   | 2.x            |
| < 3.0    | 1.x            |

## Configuration

Obfuscation can be disabled for specific node types by unsetting the processor:

```
prototype(Neos.Neos:Page) {
    @process.networkteamNeosMailObfuscator >
}
```

The JavaScript include can be disabled for custom minification:

```
prototype(Neos.Neos:Page) {
    networkteamNeosMailObfuscator >
}
```

The replacement string for the at-sign (@) can be configured. It will be inserted as HTML without escaping, so it's possible to replace it with something like an image:

```yaml
# Settings.yaml
Networkteam:
  Neos:
    MailObfuscator:
      atCharReplacementString: '<img src="https://example.com/at-icon.png" alt="at" />'
```

## Acknowledgments

Original email address obfuscation code by [TYPO3 CMS](http://www.typo3.org).

Development sponsored by [networkteam GmbH - Neos Agentur](https://networkteam.com/fokus/neos-cms.html).

## License

Licensed under GPLv2+, see [LICENSE](LICENSE).
