---
id: TASK-3
title: Redirect con tracking click
status: Done
assignee:
  - claude
created_date: '2026-06-11 11:46'
updated_date: '2026-06-11 13:27'
labels:
  - backend
dependencies:
  - TASK-2
priority: high
ordinal: 3000
---

## Description

<!-- SECTION:DESCRIPTION:BEGIN -->
Route GET /{code}: redirect 301 + registrazione click (timestamp, referrer, user agent) su tabella clicks, in coda per non rallentare il redirect.
<!-- SECTION:DESCRIPTION:END -->

## Acceptance Criteria
<!-- AC:BEGIN -->
- [x] #1 Redirect sotto i 50ms senza attendere il tracking
<!-- AC:END -->



## Implementation Notes

<!-- SECTION:NOTES:BEGIN -->
Implementati: migration clicks, modello Click, job RecordClick in coda, RedirectController, route catch-all con constraint. Decisione: 302 invece di 301 (il 301 e' cachato dal browser e ucciderebbe il tracking). IP salvato solo come hash sha256+app key (privacy).
<!-- SECTION:NOTES:END -->
