# NusaBoard Security Lab

A Laravel community publishing application with real authentication, database-backed content, comments, an admin area, and intentionally vulnerable workflows for local security education.

## Main project

The full Laravel 13 application is in the \`laravel-app/\` directory. Start with [the Laravel setup guide](laravel-app/README.md).

Features include:
- Registration, login, member dashboard, and role labels
- Article CRUD, categories, publishing status, and featured content
- Public search, article details, and stored comments
- Admin overview and user directory
- Deliberate SQL injection, stored XSS, IDOR, mass assignment, and role authorization mistakes

## Local-only warning

This repository contains intentionally vulnerable code. Run it only on your own machine with fictional data. Do not deploy it to a public host or expose it to a network.

The earlier static curriculum and small PHP range are retained as supplementary material. The Laravel app is the main full-application exercise.
