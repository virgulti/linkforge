---
id: TASK-7
title: Validazione sicurezza URL
status: Done
assignee:
  - claude
created_date: '2026-06-11 11:49'
updated_date: '2026-06-11 13:27'
labels:
  - backend
  - security
dependencies:
  - TASK-3
priority: high
ordinal: 7000
---

## Description

<!-- SECTION:DESCRIPTION:BEGIN -->
Blocco URL malevoli: schema solo http/https, no IP privati (SSRF), blocklist domini, controllo redirect loop.
<!-- SECTION:DESCRIPTION:END -->

## Implementation Notes

<!-- SECTION:NOTES:BEGIN -->
Rule SafeUrl: solo http/https, IP privati/riservati bloccati (anche via DNS, disattivabile nei test), blocklist domini con sottodomini, anti-loop verso il proprio host. Da usare in TASK-4 e TASK-5 alla creazione dei link.
<!-- SECTION:NOTES:END -->
