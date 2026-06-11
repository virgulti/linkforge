---
id: TASK-2
title: Servizio generazione short code
status: Done
assignee:
  - claude
created_date: '2026-06-11 11:46'
updated_date: '2026-06-11 12:35'
labels:
  - backend
dependencies:
  - TASK-1
priority: high
ordinal: 2000
---

## Description

<!-- SECTION:DESCRIPTION:BEGIN -->
Servizio per generare codici base62 univoci (6 char), con gestione collisioni e codici custom riservati.
<!-- SECTION:DESCRIPTION:END -->

## Acceptance Criteria
<!-- AC:BEGIN -->
- [x] #1 Codici univoci anche sotto concorrenza
- [x] #2 Parole riservate (admin, api) escluse
<!-- AC:END -->

## Implementation Notes

<!-- SECTION:NOTES:BEGIN -->
Unicita' garantita da indice unico DB + retry su UniqueConstraintViolationException (no check-then-insert). Custom code 3-16 char, riservati case-insensitive. 7 test.
<!-- SECTION:NOTES:END -->
