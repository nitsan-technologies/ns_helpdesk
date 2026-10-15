# ns_helpdesk — Operating Principles

*Every change to code, context or docs must serve the goals below.*

---

## Goal 1 — Nothing that works may break

The extension is declared for TYPO3 v12, v13 and v14 at the same time (Composer and Classic mode).

- Every change must keep all three majors working. Follow the existing switches (`<t3:majorVersion>` in the
  Page module preview, `enum_exists(IconSize::class)`, the constructor + `setData()` pair in the node resolver)
  instead of inventing new ones.
- Prefer the smallest change that fixes the root cause. No drive-by refactors.
- Run `tasks/run-quality.md` and re-test the affected flow in a frontend and backend before claiming done.

## Goal 2 — Token-cost efficiency (agent context)

- **DRY context:** each fact lives in exactly ONE file. Reference, don't copy.
- **Load on demand:** read `context/features/<area>.md` for the task, not everything.
- **Prune as you go:** keep `context/session-state.md` short.

## Goal 3 — Accuracy and trust

- Answer from context + code, never from invented API, table or field names.
- Conflicting facts across files = fix the source file.

---

## Guardrails (non-negotiable)

- Tickets contain personal data (frontend user, e-mail, ticket text). Don't expose them to other users; access
  checks belong in the controller, not only in templates.
- Keep the static template (`Configuration/TypoScript/`), the site set (`Configuration/Sets/ns_helpdesk/`,
  incl. `settings.definitions.yaml`) and the constants in `TypoScript/FeaturesConstants/` in sync when adding or
  renaming a setting.
- Ticket status uids 1 (New), 2 (Close), 3 (Reopen) are hard-coded in the controller and seeded by
  `ext_tables_static+adt.sql`; don't renumber them.
- Do not change ns_license from an ns_helpdesk task; reuse its module and trial modal.
- All UI text goes into `Resources/Private/Language/*.xlf` (English source only, translations via Crowdin),
  no hard-coded labels.
- FormEngine node resolver priorities must be unique per node name across all installed extensions
  (see `features/premium-cta.md`).

---

## Apply this whenever you

- Edit `context/` or `AGENTS.md`
- Land a feature or bugfix (update the matching `context/features/*.md` + `session-state.md`)
