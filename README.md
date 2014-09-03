-------------------
Neos MailObfuscator
-------------------

In order to make life for spammers more difficult this package provides an obfuscation of email addresses.
The email address is obfuscated by a rot13 like algorithm with random offsets.

When the link is clicked the email address is unobfuscated by the same algorithm in JavaScript:

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

Installation
------------

Install the composer package in your site package or distribution:

```shell
$ composer require networkteam/neos-mailobfuscator
```

There is no need for configuration, as a TypoScript processor is attached to all prototypes extending
`TYPO3.Neos:Content`.

Configuration
-------------

Obfuscation can be disabled for specific node types by unsetting the processor:

```
prototype(Vendor.MyPackage:MyNodeType) {
	@process.networkteamNeosMailObfuscator >
}
```

The JavaScript include can be disabled for custom minification:

```
page = prototype(TYPO3.Neos:Page) {
	body.javascripts.networkteamNeosMailObfuscator >
}
```

Acknowledgments
---------------

Original email address obfuscation code by [TYPO3 CMS](http://www.typo3.org).

Development sponsored by [networkteam GmbH - Neos Agentur](http://networkteam.com/typo3-cms/typo3-neos-agentur.html).

License
-------

Licensed under GPLv2+, see [LICENSE](LICENSE)
