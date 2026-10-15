# ns_helpdesk — Core facts

## Identity

| Item | Value |
|---|---|
| Extension key | `ns_helpdesk` |
| Composer name | `nitsan/ns-helpdesk` |
| Title (ext_emconf) / product name | TYPO3 Helpdesk Extension / Help Desk |
| Namespace | `NITSAN\NsHelpdesk\` → `Classes/` (ext_emconf also declares a `Classes/` classmap) |
| Version (ext_emconf) | 14.1.0 (latest tag 14.0.0) |
| Repository | https://github.com/nitsan-technologies/ns_helpdesk |
| Licensing status (NITSAN Free/Freemium sheet) | **Freemium** — this repo is the free version |
| Premium version (sheet) | https://t3planet.de/typo3-beratungsstelle-erweiterung (price "unknown" in the sheet) |

## Compatibility

| Source | TYPO3 | PHP |
|---|---|---|
| `ext_emconf.php` | `12.0.0-14.9.99` | not declared |
| `composer.json` | `^12 \|\| ^13 \|\| ^14` | not declared |
| ns_license 14.5.4 (dependency) | `12.4.18-14.9.99` | — |

- Effective minimum is **TYPO3 12.4.18** because of ns_license; the extension's own constraint still says 12.0.0.
- Code uses union types, `match` and `readonly` → PHP 8.1+ (TYPO3 12 minimum anyway).

## Dependencies

| Package | Constraint | Why |
|---|---|---|
| `typo3/cms-core` | `^12 \|\| ^13 \|\| ^14` | core |
| `nitsan/ns-license` / `ns_license` | `^14.5.4` / `14.5.4-14.9.99` | TYPO3 Shop module + free-trial deep link (see `features/premium-cta.md`) |
| `typo3/cms-felogin` | not declared | ticket list needs a logged-in frontend user; the static template overrides felogin templates |
| jQuery | not declared | `fenshelpdesk.js` needs it; bundled copy loaded only with `helpdeskJqueryLib` (default off) |

## Tooling

- No PHPUnit, PHPStan, CS fixer or package.json in the repo.
- CI: `.github/workflows/ter-release.yml` only (publishes tags to TER).
- Translations: Crowdin (`.crowdin.yml`), source files `Resources/Private/Language/*.xlf`.

## Conventions

- Extbase plugins registered as content elements (`ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT`),
  `Configuration/Services.yaml` autowires `NITSAN\NsHelpdesk\` (models excluded).
- Fluid namespaces in backend previews use the inline syntax (`{namespace nh=…}`).
- Backend additions (premium strip) use core icons, Bootstrap utilities and their own stylesheet in
  `Resources/Public/Css/`, `light-dark()` for both backend colour schemes.

## Known issues (existing, not fixed)

Behaviour details: `features/ticket-plugins.md`. All code-read.

- `showAction`, `closeTicketAction` and `reopenTicketAction` don't check that the ticket belongs to the
  current frontend user; the Show template only checks that *a* user is logged in.
- `createAction`, `closeTicketAction`, `reopenTicketAction` `echo` JSON and call `exit()` instead of returning
  a response.
- The AJAX page types (`741852` list, `15021994` create, `15021995` close/reopen) copy
  `tt_content.list.20.nshelpdesk_*`, which CType plugins don't define, and use `switchableControllerActions`
  (ignored since v12). `New.html` posts the ticket form to `pageType="15021994"`, so check ticket creation
  first when it fails (not verified live).
- "Helpdesk - Ticket Submission" registers a `quickPopupTicket` action that `TicketsController` doesn't have.
- The static template overrides `plugin.tx_felogin_login` templates site-wide and loads Bootstrap 5.3,
  Font Awesome, two CSS files and `fenshelpdesk.js` on every page, plus a "Please include Jquery" console
  script in `footerData.110`.
- `Configuration/TCA/Overrides/fe_users.php` makes `fe_users.email` required for every frontend user.
- `initializeObject()` checks `plugin.tx_nshelpdesk_helpdesk` without the trailing dot, so `$this->config`
  stays empty. `layoutRootPaths.0` points to `EXT:tx_nshelpdesk/…`.
- Default assignee falls back to backend user uid 1; if that user doesn't exist, ticket creation answers
  `{"status":"error"}`. Default `adminMail` constant is `test.t3admn@gmail.com`.
- `getMailTemplateDetails()` reads `$_SERVER['HTTPS']` without `isset` (warning on plain HTTP).
- Backend module leftovers: no module is registered, but the controller has backend branches
  (`ModuleTemplate`, `tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1`), and `module.tx_nshelpdesk*` TypoScript,
  `Templates/Tickets/Dashboard.html`, `GetConstants.html`, `Templates/Default/ConstantEditor*`,
  `Backend/NsHelpdesk.html`, `Backend/Layouts/`, `Backend/Partials/`, `Layouts/Widget/`, `Templates/Widget/`,
  `ThemeContainsViewHelper` and `BackendModule.xlf` / `locallang_helpdesk_dashboard.xlf` belong to it.
  `page.tsconfig` sets `dashboardPreset-nsHelpdesk`, but no preset is defined. `lib.getBeAvatar` outputs "Hello".
  `Configuration/Icons.php` still registers `module-nshelpdesk` and `parent-module-nshelpdesk`.
  `initializeAction()` reads extension configuration `globalStorage`, but there is no `ext_conf_template.txt`.
- The Ticket Submission FlexForm names its only sheet `settings.quickreply` instead of `sDEF`.
- `Configuration/TCA/Overrides/tt_content.php` uses `ExtensionManagementUtility::addPiFlexFormValue()`,
  deprecated in TYPO3 14.0 (#107047, removed in v15): triggers `E_USER_DEPRECATED` on v14.
- Wizard titles in `page.tsconfig` are hard-coded English; the edit-form tab label is the literal `plugin`.
- `composer.json` licence `GPL-2.0+` is a deprecated SPDX id (`composer validate` warning).
- README: TYPO3 14 badge links to `get.typo3.org/version/13`, licence badge says GPL-3.0, premium link
  (`t3planet.de/en/typo3-helpdesk-extension`) differs from the sheet's URL, changelog links to t3planet.com.
- `ter-release.yml` uses PHP 7.4 and the deprecated `::set-output`.
