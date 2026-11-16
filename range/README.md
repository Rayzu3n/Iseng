# SecureLab Injection Range

A deliberately vulnerable, local-only web app for learning how common web flaws behave in a real request/response cycle.

## Included labs

1. **SQL injection** - a login query concatenates untrusted values into SQL. Use the form and observe how the database interprets the input.
2. **Stored XSS** - comments are stored and later rendered without output encoding. Use a harmless browser alert to verify the issue.
3. **IDOR / broken object-level authorization** - a demo student session can request profile records without an ownership check.

Every lab includes progressive hints, vulnerable code, and a safer implementation.

## Requirements

- Docker Engine
- Docker Compose v2

## Start

From this directory:

\`\`\`bash
docker compose up --build
\`\`\`

Open **http://localhost:8080**.

The service is published only on the host loopback interface, and its Docker network is internal with no external network access. Do not change the port binding to 0.0.0.0 or deploy this container to a public server.

## Demo accounts

These accounts are fictional seed data for the lab:

| Username | Password | Role |
|---|---|---|
| admin | admin123 | administrator |
| student | student123 | student |
| teacher | teacher123 | teacher |

The SQL injection challenge is intentionally vulnerable; a successful demonstration does not require a real password.

## Suggested path

1. Start with SQL injection. Try normal credentials, then inspect the query and reveal hints.
2. Continue to stored XSS. Submit a harmless alert payload and reload the comments page.
3. Finish with IDOR. Compare the current demo user's profile ID with the profile IDs exposed by the page.
4. Read the secure pattern for each issue and explain why it prevents the flaw.

## Stop and reset

Stop the lab:

\`\`\`bash
docker compose down
\`\`\`

Reset all lab data, including stored comments:

\`\`\`bash
docker compose down -v
docker compose up --build
\`\`\`

The reset command deletes only this lab's Docker volume.

## Safety boundary

This range contains intentionally vulnerable code. It is for localhost-only practice with fictional data. Do not expose it to a LAN or the public internet, and do not reuse the vulnerable patterns in real applications. The XSS exercise is designed around a harmless local alert; do not add code that collects data or communicates with other systems.

## Implementation notes

- PHP 8.4 + Apache
- SQLite database seeded at first run
- No external database or third-party runtime dependency
- Container runtime is read-only apart from /tmp and the lab data volume
- The Docker network is marked internal

The intentionally vulnerable code is clearly marked in index.php. Look for VULNERABLE DEMO comments.
