---
id: TASK-4
title: API REST gestione link
status: Done
assignee:
  - aider
created_date: '2026-06-11 11:46'
updated_date: '2026-06-11 16:12'
labels:
  - backend
  - api
dependencies:
  - TASK-2
priority: medium
ordinal: 4000
---

## Description

<!-- SECTION:DESCRIPTION:BEGIN -->
Endpoint CRUD /api/links con autenticazione Sanctum, rate limiting e validazione URL.
<!-- SECTION:DESCRIPTION:END -->

## Implementation Notes

<!-- SECTION:NOTES:BEGIN -->
API REST completata con autenticazione Sanctum, rate limiting e validazione URL. Aggiunto trait HasApiTokens sul modello User per far passare i test.
<!-- SECTION:NOTES:END -->
