---
id: TASK-2
title: Servizio generazione short code
status: In Progress
assignee:
  - claude
created_date: '2026-06-11 11:46'
updated_date: '2026-06-11 12:10'
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
- [ ] #1 Codici univoci anche sotto concorrenza
- [ ] #2 Parole riservate (admin, api) escluse
<!-- AC:END -->
