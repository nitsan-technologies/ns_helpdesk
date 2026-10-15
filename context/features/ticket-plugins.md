# Feature — Ticket list and ticket submission

*Status: Done (free version). Behaviour below is code-read; known defects live in `core.md`.*

## Setup

1. Include the static template "Helpdesk" or the site set `nitsan/ns-helpdesk`.
2. Import the ticket status records (`ext_tables_static+adt.sql`, done by the extension setup) and set the
   storage page, admin e-mail/name and default assignee (backend user uid) in the constants / site settings.
3. Frontend login (felogin) for customers; their `fe_users` record needs an e-mail.
4. List page: "Helpdesk - List View" (List Page ID, Ticket Create Page ID). Create page: "Helpdesk - Ticket
   Submission" (admin / visitor mail subjects, popup message, mail body).
5. Optional: import `Configuration/HelpdeskRoute.yaml` into the site config for `/ticket-slug` URLs.

## Settings (constants / site set, `plugin.tx_nshelpdesk_helpdesk.*`)

| Setting | Default | Effect |
|---|---|---|
| `notify.email.adminMail` / `adminName` | `test.t3admn@gmail.com` / `NS Helpdesk` | Sender/receiver of admin mails |
| `notify.adminNotify` | empty | Also mail the admin on new tickets |
| `includeJSLibrary.helpdeskJqueryLib` | empty | Load the bundled jQuery |
| `storagePid.storagePid` | empty | Ticket storage page |
| `loginPage` | empty | Login page link |
| `commonSettings.defaultAssigneeId` | 1 | Backend user that gets new tickets |
| `formConf.*` | — | Labels and placeholders (name, email, subject, textarea), submit label; `textareafield.include` / `required` (1) and `minChar` (150) |

Name, email and subject are always shown and marked mandatory: setup maps their `include` / `required` to
constants that are never defined, and `Partials/Tickets/FormFields.html` doesn't read them. The same applies to
`settings.imageTypes` / `imageMaxSize` (code-read).

## List View

- `listAction()`: tickets of the logged-in frontend user (`fetchTickets(['userid' => …])` → `user_id`).
  Nothing is listed for anonymous visitors. The status filter (AJAX via `pageType 741852`) reads
  `tx_nshelpdesk_nitsan_nshelpdeskhelpdeskmi1[ticketStatus]` — the old backend module namespace — so it
  probably doesn't filter on the frontend (not verified).
- `showAction()`: one ticket (via slug route or `tx_nshelpdesk_helpdesklist[tickets]`).
- `closeTicketAction()`: status → 2 (Close), stores `rating`, mails the admin (or the user when `fromBackend`).
- `reopenTicketAction()`: status → 3 (Re-open), same mail logic. Both answer JSON and exit.

## Ticket Submission

- `newAction()` renders the form; `createAction()` (AJAX, `pageType 15021994`) sets assignee, unique slug,
  status (first `TicketStatusRepository::findAll()` record, i.e. New when seeded; the form's hidden
  `ticketStatus` is not mapped), post date, persists, then mails the visitor (`User/UserMail`), optionally the
  admin (`Admin/AdminNotify`) and the assignee. Placeholders in the mail body: `{visitor_name}`,
  `{ticket_number}`, `{ticket_assignee}`. The ticket's frontend user comes from the form's hidden `userId`.

## Premium-only (not in this repo)

Per the product page: backend helpdesk dashboard (answer tickets, status, assignment, search/filter), agent
profiles, auto tagging and assignment, triggers and automation, customisable form fields and multiple forms,
satisfaction ratings and comments, quick helpdesk popup widget, Google reCAPTCHA, custom-field e-mails.
