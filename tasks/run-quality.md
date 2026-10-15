# Run quality checks

The repo has no PHPUnit, PHPStan or CS config. Available checks:

```bash
# PHP syntax, once per installed PHP version (8.1+)
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done

# composer.json (expected warning: deprecated licence id "GPL-2.0+")
composer validate --no-check-publish

# XLF well-formedness
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```

Fluid templates can't be checked with `xmllint` (conditions like `{a} && {b}` are not valid XML).
After adding or renaming classes in Classic mode run `typo3 dumpautoload`, then `typo3 cache:flush`.

## Manual test (per TYPO3 major 12 / 13 / 14)

Setup: static template "Helpdesk" or site set `nitsan/ns-helpdesk`; storage page, admin mail and an existing
default assignee configured; felogin page and a frontend user with e-mail; list and create pages with the two
plugins.

1. Edit form of each helpdesk element → tab "plugin": one premium strip at the top of the FlexForm panel.
   Other plugins' FlexForms keep only their own strip or none.
2. Page module: one strip at the top of each helpdesk preview; the List View preview shows List Page ID and
   Ticket Create Page ID on every major.
3. "Start Free Trial" opens the ns_license module and its trial wizard for `ns_helpdesk`
   (needs a system maintainer). Check light and dark backend themes.
4. Frontend, logged in: submit a ticket → success message, ticket record with slug and status New, mails
   to visitor / admin / assignee (check Mailpit).
5. List View: own tickets only, status filter, detail page, close with rating, reopen.
6. Logged out: no tickets listed.

Remove test tickets, content elements, frontend and temporary backend users afterwards.
