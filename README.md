# SecureLab

A web security learning project with two parts:

## 1. Guided curriculum

A lightweight static site with six lessons, progressive hints, quizzes, and secure-code explanations.

Open `index.html` directly, or run:

```bash
python3 -m http.server 8000
```

Then visit http://localhost:8000.

## 2. Hands-on Injection Range

A real PHP + SQLite application with intentionally vulnerable forms and database-backed exercises:

- SQL injection in a demo login query
- Stored XSS in a comments board
- IDOR / broken object-level authorization in a profile viewer

The range is deliberately vulnerable and is isolated to localhost with Docker. It uses fictional records only.

See [range/README.md](range/README.md) for requirements, startup, and reset instructions.

## Safety

Do not deploy the vulnerable range, expose it to a LAN, or test these techniques against systems you do not own or have explicit permission to assess. Keep the vulnerable app inside its local Docker sandbox.

## Project structure

- `index.html`, `styles.css`, `app.js` - guided curriculum
- `range/` - isolated PHP + SQLite injection range
