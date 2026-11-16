# SecureLab

An interactive, local-first web security learning lab. Learn to recognize common web vulnerabilities, reason about their impact, and apply safer fixes.

## Topics

- Authentication vs authorization
- IDOR / broken object-level authorization
- Cross-site scripting (XSS)
- SQL injection
- Cross-site request forgery (CSRF)
- Security headers

## Run locally

This is a static learning app. No backend, database, or package installation is required.

```bash
python3 -m http.server 8000
```

Open http://localhost:8000.

## Learning flow

1. Read the fictional scenario.
2. Inspect the intentionally flawed code sample.
3. Reveal hints progressively.
4. Choose the root cause and check your reasoning.
5. Review the safer implementation.

Progress is stored in your browser's localStorage.

## Safety

All examples are fictional and intended for local education. Only test systems you own or have explicit permission to assess. Vulnerable snippets are demonstrations, not production code.

## Structure

- `index.html` - app shell
- `styles.css` - responsive interface
- `app.js` - lessons, hints, quiz logic, and progress tracking

## License

MIT