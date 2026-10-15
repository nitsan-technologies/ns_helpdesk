# AGENTS.md — ns_helpdesk (TYPO3 Helpdesk)

Agent router for the TYPO3 Helpdesk free extension. Load only what your task needs.

> **Principles:** `context/principles.md`
> **Session log:** `context/session-state.md`
> **Doc index:** `context/docs-map.md`

---

## Always load

| File | Why |
|---|---|
| `context/principles.md` | Guardrails, "nothing that works may break" rule |
| `context/core.md` | Identity, TYPO3/PHP/ns_license constraints, tooling, known issues |

---

## Task router

| Task | Load |
|---|---|
| Ticket list / detail / close / reopen, ticket submission form, e-mails, login, TypoScript / site set settings | `context/features/ticket-plugins.md` |
| Premium strip / free trial / ns_license dependency | `context/features/premium-cta.md` |
| Classes, tables, TCA, templates, Page module preview, route enhancer, assets | `context/architecture.md` |
| Lint / validate / manual test | `tasks/run-quality.md` |
| Update agent context after a change | `tasks/context-update.md` |

---

## Feature status

| Feature | Status | Agent entry |
|---|---|---|
| "Helpdesk - List View": logged-in frontend user's tickets, detail, close (with rating) / reopen | Done | `context/features/ticket-plugins.md` |
| "Helpdesk - Ticket Submission": AJAX ticket form, notification e-mails | Done | `context/features/ticket-plugins.md` |
| Page module previews of both plugins | Done | `context/architecture.md` |
| Premium strip + ns_license free-trial deep link (edit form + Page module) | Done (needs ns_license 14.5.4 release) | `context/features/premium-cta.md` |

---

## Quick commands

```bash
cd packages/ns_helpdesk            # or typo3conf/ext/ns_helpdesk in Classic mode
for f in $(find . -name "*.php" -not -path "./.git/*"); do php -l "$f"; done
composer validate --no-check-publish
for f in Resources/Private/Language/*.xlf; do xmllint --noout "$f"; done
```
