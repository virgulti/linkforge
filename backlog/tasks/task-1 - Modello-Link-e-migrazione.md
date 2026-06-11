---
id: TASK-1
title: Modello Link e migrazione
status: Done
assignee:
  - aider
created_date: '2026-06-11 11:45'
updated_date: '2026-06-11 11:53'
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
Creati migration, modello Link con HasFactory, LinkFactory e seeder. Indice unico su short_code.
<!-- SECTION:NOTES:END -->
