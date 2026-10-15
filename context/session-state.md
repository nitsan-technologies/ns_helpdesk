# ns_helpdesk — Session state

## 2026-10-14 — ns_license integration, premium strip, agent context

- Branch `master` (HEAD 6223c59). All changes uncommitted.
- Added `nitsan/ns-license ^14.5.4` / `ns_license 14.5.4-14.9.99`.
- Premium strip with "Start Free Trial" → ns_license module `?trial=ns_helpdesk#free-section` in the edit form
  and the Page module preview of both plugins (see `features/premium-cta.md`). Resolver priority 43.
- Verified live on TYPO3 14.3.7 classic (temporary admin made system maintainer, two hidden test elements;
  all removed afterwards, `settings.php` restored). The List View preview still shows its FlexForm values on
  v14. Not tested on v12/v13. Frontend not tested.
- Created agent context (`AGENTS.md`, `CLAUDE.md`, `context/`, `tasks/`); no previous context existed.

## 2026-10-14 — Banner redesign from review

- Team-wide wording ("You're using the free version of NS Helpdesk" / "Unlock premium features and get more
  out of NS Helpdesk."), two-line layout, arrow on the button, more padding, rounded card, inset inside the
  FlexForm panel (see `features/premium-cta.md`). Version 14.0.0 → 14.1.0 (`ext_emconf.php`).
- Edit form and Page module verified live on 14.3.7; test data removed. Uncommitted.

## 2026-10-15 — Context audit

- Code audit: DataHandler hook only fills empty slugs of new tickets; `formConf` include/required work only for
  the textarea; added CSP/import map, legacy preview keys, module icons, `globalStorage`, deprecation.
