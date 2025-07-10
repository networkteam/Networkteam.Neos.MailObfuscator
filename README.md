# Neos MailObfuscator

To make life for spammers more difficult, this package provides obfuscation of email addresses.
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

If content security policy is applied in your website use the `StructuredMailtoLinkObfuscatingConverter` implementation.
This does not add inline JavaScript into `href` attribute, but adds data attributes for token and vector values.

```html
<a href="#" data-mailto-token="obfuscatedEmail" data-mailto-vector="randomNumber">Contact us</a>
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

### Content Security Policy (CSP)

If you have CSP enabled in your project, the inline JavaScript code in `href` attribute of an obfuscated link will lead to
JavaScript error in browser console when clicking the link:

```
Refused to run the JavaScript URL because it violates the following Content Security Policy directive: "...".
```

To bypass this error, you can use a different MailtoLinkObfuscatingConverter implementation. This will skip adding
inline JavaScript to `href` attribute and add data attributes holding encrypted email (token) and used offest (vector) instead.
The JavaScript implementation handles the data attributes and decrypts the value.

To enable this behavior, you have to configure another implementation for `\Networkteam\Neos\MailObfuscator\Converter\MailtoLinkConverterInterface`.
Do so by adding the following lines to your `Objects.yaml`:

_Objects.yaml_

```
'Networkteam\Neos\MailObfuscator\Converter\MailtoLinkConverterInterface':
  className: 'Networkteam\Neos\MailObfuscator\Converter\StructuredMailtoLinkObfuscatingConverter'
```

## EEL Helpers
There are Eel helpers available to use MailObfuscator functions in Fusion

```
// Convert @ Character
${Networkteam.Neos.MailObfuscator.convertAtChar('foo@example.com')}
// returns: foo (at) example.com

```

```
// Convert Mail to Href
${Networkteam.Neos.MailObfuscator.convertMailto2Href('foo@example.com')}
// returns javascript:linkTo_UnCryptMailto('obfuscatedEmail', -randomNumber)
```


## Development

To compile JavaScript via yarn run:

```bash
yarn install
yarn build
```

## Acknowledgments

Original email address obfuscation code by [TYPO3 CMS](http://www.typo3.org).

Development sponsored by [networkteam GmbH - Neos Agentur](https://networkteam.com/fokus/neos-cms.html).

## License

Licensed under GPLv2+, see [LICENSE](LICENSE).
