--------------------------------
 Neos MailObfuscator
--------------------------------

In order to make life to spammers more difficult this extension provides obfuscation of email addresses. The email
address is obfuscated by a rot13 like algorithm with variable offsets.

When the link is clicked the email address is unobfuscated by the same algorithm in javascript.

```
 <a href="mailto:foo@example.com">foo@example.com</a>
```

will become

```
 <a href="javascript:linkTo_UnCryptMailto('obfuscatedEmail', -randomNumber)">foo (at) example.com</a>
```

The replacement in done in 2 steps, it is possible to have something different displayed as linkname then as
link target.

```
 <a href="mailto:foo@example.com">Contact us</a>
```

This will become

```
 <a href="javascript:linkTo_UnCryptMailto('obfuscatedEmail', -randomNumber)">Contact us</a>
```

Installation
--------------------------------

```
 $ composer require networkteam/neos-mailobfuscator
```

There is no need to configure something as the processor is attached to all content of a node type.