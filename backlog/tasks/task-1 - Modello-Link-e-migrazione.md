---
id: TASK-1
title: Modello Link e migrazione
status: In Progress
assignee:
  - aider
created_date: '2026-06-11 11:45'
updated_date: '2026-06-11 12:28'
labels:
  - backend
  - db
dependencies: []
priority: high
ordinal: 1000
---

## Description

<!-- SECTION:DESCRIPTION:BEGIN -->
Tabella links: short_code unico, original_url, user_id nullable, scadenza opzionale. Modello Eloquent con relazioni.
<!-- SECTION:DESCRIPTION:END -->

## Acceptance Criteria
<!-- AC:BEGIN -->
- [x] #1 Migrazione crea tabella links con indice unico su short_code
- [x] #2 Factory e seeder funzionanti
<!-- AC:END -->

## Implementation Notes

<!-- SECTION:NOTES:BEGIN -->
agy appeso senza output dopo 2h, killato. Torna ad aider ora che ha il modello locale Ollama.
<!-- SECTION:NOTES:END -->
