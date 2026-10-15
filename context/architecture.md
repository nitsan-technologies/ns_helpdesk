# ns_helpdesk — Architecture

## Content elements (`ext_localconf.php`, `Configuration/TCA/Overrides/tt_content.php`)

| CType | Title | `TicketsController` actions (all non-cacheable) | FlexForm (one sheet) |
|---|---|---|---|
| `nshelpdesk_helpdesklist` | Helpdesk - List View | `list`, `show`, `closeTicket`, `reopenTicket` | `NsHelpdesk_ListView.xml` (`sDEF`: `settings.listPageId`, `settings.list.createPageId`) |
| `nshelpdesk_helpdeskticket` | Helpdesk - Ticket Submission | `new`, `create`, `quickPopupTicket` (missing) | `NsHelpdesk_TicketSubmission.xml` (sheet `settings.quickreply`: `adminSubject`, `visitorSubject`, `popoupMessage`, `body` RTE) |

- Both use icon `ns_helpdesk-plugin-helpdesk`, wizard group `plugins` in `Configuration/page.tsconfig`,
  edit-form tab `plugin` with `pi_flexform`.
- Upgrade wizard `NsHelpdeskCTypeMigration` moves old `list_type` records to the CTypes.
- DataHandler hook `Hooks/DataHandler` (`processDatamap_postProcessFieldArray`, registered as
  `processDatamapClass['ns_helpdesk']`) fills an empty `slug` of a **new** ticket from `ticket_subject` with
  `Backend/TicketSlugHelper::sanitize()`; existing tickets are not touched (code-read).

## Page module preview (`Configuration/page.tsconfig`)

| CType | Template | Content |
|---|---|---|
| `nshelpdesk_helpdesklist` | `Resources/Private/Backend/NsHelpdeskList.html` | Premium strip, display type, List Page ID, Ticket Create Page ID |
| `nshelpdesk_helpdeskticket` | `Resources/Private/Backend/NsHelpdeskTicket.html` | Premium strip, display type |

`NsHelpdeskList.html` switches with `<t3:majorVersion version="14" operator=">=">` between
`record.pi_flexform.sheets.sDEF.settings.*` (v14) and `pi_flexform_transformed.settings.*` (v12/v13).
`Backend/NsHelpdesk.html` (old `switchableControllerActions` preview) is not referenced. `page.tsconfig` also
sets the legacy keys `preview.list.nshelpdesk_*`, which CType records never use.

## Classes

| Class | Role |
|---|---|
| `Controller/TicketsController` | Frontend actions, e-mail sending (`sendTemplateEmail()` with Fluid mail templates), unused backend branches |
| `Domain/Model/Tickets` | `ticketSubject`, `slug`, `ticketText`, `ticketPostDate`, `ticketStatus`, `assigneeId` (BackendUser), `userId` (FrontendUser), `ticketRating` |
| `Domain/Model/TicketStatus` | `statusTitle`, `statusColor` |
| `Domain/Model/BackendUser`, `FrontendUser`, `FrontendUserGroup` | Mapped to `be_users`, `fe_users`, `fe_groups` (`Configuration/Extbase/Persistence/Classes.php`) |
| `Domain/Repository/TicketsRepository` | `fetchTickets($filter)`, `getSlug()` |
| `Domain/Repository/TicketStatusRepository` | `getFromAll()` (ignore storage page) |
| `Hooks/DataHandler`, `Backend/TicketSlugHelper` | Slug cleanup on save |
| `Upgrades/NsHelpdeskCTypeMigration` | `list_type` → CType migration |
| `ViewHelpers/LoadAssetsViewHelper` | Used by `Layouts/Default.html` |
| `ViewHelpers/Typo3/MajorVersionViewHelper` | `<t3:majorVersion>` condition for previews |
| `ViewHelpers/ThemeContainsViewHelper` | Only used by `Partials/ConstantEditorOptions.html` |
| `Backend/PremiumStrip`, `Form/Container/…`, `Form/Resolver/…`, `ViewHelpers/Be/PremiumStripViewHelper` | Premium strip (see `features/premium-cta.md`) |

## Tables (`ext_tables.sql`, `ext_tables_static+adt.sql`)

| Table | Content |
|---|---|
| `tx_nshelpdesk_domain_model_tickets` | `ticket_subject`, `slug` (TCA slug from subject, `uniqueInSite`), `ticket_text`, `ticket_post_date`, `ticket_status`, `assignee_id`, `user_id`, `ticket_rating` |
| `tx_nshelpdesk_domain_model_ticketstatus` | `status_title`, `status_color`; static data: 1 New, 2 Close, 3 Re-open (pid 1) |

`fe_users.email` is made required (`TCA/Overrides/fe_users.php`).

## Routing

`Configuration/HelpdeskRoute.yaml`: Extbase route enhancer for `HelpdeskList` (`/{ticket-sub}` →
`Tickets::show`, `PersistedAliasMapper` on `slug`). Must be imported into the site config by the integrator.

## Views (frontend)

| File | Used by |
|---|---|
| `Layouts/Default.html` | Wrapper, `LoadAssetsViewHelper` |
| `Templates/Tickets/List.html`, `Show.html` | Ticket list (status filter) and detail with close/reopen + rating |
| `Templates/Tickets/New.html`, `Partials/Tickets/FormFields.html` | Ticket form (posts to `pageType 15021994`) |
| `Templates/Email/**`, `Layouts/Email.html` | User, admin and close/reopen mails |
| `Templates/Login/**` | felogin template override (static template) |

## TypoScript and assets

Static template "Helpdesk" (`Configuration/TypoScript/`) and site set `nitsan/ns-helpdesk` ("Helpdesk Sets")
— same setup: `plugin.tx_nshelpdesk_helpdesk` (copied to `helpdesklist` / `helpdeskticket`), AJAX page types,
felogin override, `page.include*` assets (Bootstrap 5.3, `Css/font-awesome.css`, `ns-helpdesk.css`,
`configuration.css`, `JavaScript/fenshelpdesk.js`, optional `JavaScript/Jquery.js`).
Backend: `Css/backend-premium-strip.css` (strip only). `Configuration/ContentSecurityPolicies.php` extends the
backend CSP (`style-src-elem` + `fonts.googleapis.com`, `font-src` + `fonts.gstatic.com`, both with `data:`);
`JavaScriptModules.php` maps `@nitsan/ns-helpdesk/` → `Resources/Public/JavaScript/` (code-read).

## Labels

`locallang.xlf` (UI, preview, premium strip), `locallang_db.xlf` (TCA, wizard description),
`locallang_flex.xlf` (FlexForms), `locallang_constant.xlf` (constants), others belong to the old module.
