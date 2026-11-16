const lessons = [
{id:"intro",title:"The security mindset",category:"GETTING STARTED",time:"4 MIN",level:"BEGINNER",headline:"Think like a <em>defender.</em>",lead:"Security is not a collection of scary tools. It is the habit of asking who can do what, with which data, and under what conditions.",storyTitle:"A small app, a big responsibility",story:"Imagine a school portal. Students can view their own profile, teachers can manage class notes, and administrators can manage accounts. The app works perfectly during a demo. But what happens when someone changes a URL, submits unexpected input, or skips a screen?",points:[["01","Identify the asset","What data or action needs protection?"],["02","Find the boundary","Where does input cross from untrusted to trusted?"]],takeaway:"A vulnerability often appears when the application trusts a value or action without checking whether that trust is deserved.",question:"Which question is most useful when reviewing an access-control feature?",options:["Does the button look disabled?","Does the server verify the user's permission for this exact resource?","Is the URL difficult to guess?"],answer:1,explain:"The server must authorize every sensitive action. Hiding a button or making a URL hard to guess is not an access-control boundary."},
{id:"idor",title:"Broken access control",category:"CASE FILE 01",time:"7 MIN",level:"BEGINNER",headline:"The profile that wasn't <em>yours.</em>",lead:"A student portal lets users view their profile through a numeric identifier. A tiny assumption in the server code creates a serious privacy problem.",storyTitle:"Case: Campus Connect",story:"Nadia signs in and opens her profile. The address contains an account identifier. The product team assumed that knowing the identifier was enough to view the record. Review the server-side decision, not anyone else's account.",vulnerable:`// GET /api/profile/:userId
async function getProfile(req, res) {
  const profile = await db.users.findById(req.params.userId);
  if (!profile) return res.status(404).json({ error: "Not found" });

  // Authenticated, but authorized for this record?
  return res.json(profile);
}`,hint:["Authentication answers who you are. Authorization answers whether you may access this specific record.","Compare the requested record's owner with the identity established by the server-side session. Never accept the claimed owner from an editable client field."],question:"What is the core flaw in this handler?",options:["The identifier is numeric","It checks that a record exists, but never checks whether the signed-in user may access it","The route uses GET instead of POST"],answer:1,explain:"This is an insecure direct object reference (IDOR), a form of broken object-level authorization. A valid login does not grant access to every user's record.",fix:`// Derive identity from the trusted session.
async function getMyProfile(req, res) {
  const profile = await db.users.findById(req.user.id);
  if (!profile) return res.status(404).json({ error: "Not found" });
  return res.json(profile);
}`,fixText:"For staff workflows that access other records, add an explicit server-side permission check for the requested resource."},
{id:"xss",title:"Cross-site scripting",category:"CASE FILE 02",time:"7 MIN",level:"BEGINNER",headline:"When text becomes <em>code.</em>",lead:"A community board displays user-submitted comments. The developer inserts the comment directly into an HTML template, forgetting that user input is untrusted.",storyTitle:"Case: Noticeboard",story:"A student posts a comment. The page renders the content as HTML instead of treating it as plain text. What should the browser do with a comment: display it, or interpret it?",vulnerable:`// User-controlled content from a comment
const comment = { body: userSubmittedText };

// Unsafe: the browser parses the value as HTML.
commentContainer.innerHTML = comment.body;`,hint:["The dangerous sink is the API that parses a string as HTML.","Use a text-only rendering API for plain text. If rich HTML is required, use a maintained sanitizer with a strict allowlist."],question:"Which change is the safest default for displaying a plain-text comment?",options:["Use innerHTML after removing angle brackets manually","Use textContent so the browser treats the value as text","Disable the browser's script engine"],answer:1,explain:"Using textContent prevents the browser from parsing the supplied value as markup. Context-aware output encoding and safe templating matter throughout the app.",fix:`// Safe for plain-text content:
commentContainer.textContent = comment.body;`,fixText:"Avoid building HTML with string concatenation. For rich content, sanitize with a reputable library and a narrowly defined allowlist."},
{id:"sqli",title:"SQL injection",category:"CASE FILE 03",time:"8 MIN",level:"BEGINNER",headline:"A query with a <em>trust issue.</em>",lead:"An internal directory searches users by name. The SQL query is assembled by joining raw user input into the query text.",storyTitle:"Case: Staff Directory",story:"The search feature accepts a name from a form. A developer builds the database query by concatenating the value into SQL. The database cannot reliably distinguish the intended search value from SQL syntax.",vulnerable:`// Unsafe: input is concatenated into SQL.
const sql = "SELECT id, name FROM users WHERE name = '" + searchName + "'";
const rows = await db.query(sql);`,hint:["The issue is not that SQL is being used. The user-controlled value is joined into the query text.","Use a parameterized query. Keep SQL structure fixed and pass the search value separately."],question:"How should the search value be passed to the database?",options:["Escape a few special characters and keep concatenating","Put the value in a parameter placeholder and bind it separately","Hide the search field from anonymous visitors"],answer:1,explain:"Parameterized queries separate SQL code from data. The database treats the supplied name as a value, not as part of the SQL statement.",fix:`// Safe: query structure and data are separate.
const sql = "SELECT id, name FROM users WHERE name = ?";
const rows = await db.query(sql, [searchName]);`,fixText:"Use prepared statements or ORM parameter binding consistently. Also apply least-privilege database permissions."},
{id:"csrf",title:"Cross-site request forgery",category:"CASE FILE 04",time:"7 MIN",level:"INTERMEDIATE",headline:"A request you never <em>meant.</em>",lead:"A settings page changes an account email through a state-changing request. The app relies only on the browser's automatically attached session cookie.",storyTitle:"Case: Account Settings",story:"A signed-in user visits another website in the same browser. That site causes the browser to send a request to the settings application. The app sees a valid session cookie, but has no separate proof that the user intended this action.",vulnerable:`// Unsafe pattern: no anti-CSRF validation.
app.post("/account/email", requireLogin, async (req, res) => {
  await users.updateEmail(req.user.id, req.body.email);
  res.sendStatus(204);
});`,hint:["Browsers can attach cookies automatically. Authentication by cookie alone does not prove the user intentionally initiated a state-changing request.","Use a server-validated anti-CSRF token. SameSite cookies are useful defense-in-depth, not a universal substitute for a token strategy."],question:"What is a robust defense for cookie-authenticated state-changing requests?",options:["Use a hard-to-guess URL","Validate an anti-CSRF token and configure SameSite cookies appropriately","Only use POST instead of GET"],answer:1,explain:"CSRF defenses verify request intent beyond the automatically attached cookie. Use framework-provided CSRF middleware where available.",fix:`app.post("/account/email",
  requireLogin,
  validateCsrfToken,
  async (req, res) => {
    await users.updateEmail(req.user.id, req.body.email);
    res.sendStatus(204);
  }
); `,fixText:"Prefer maintained framework middleware. Configure secure cookie settings and avoid state changes on GET requests."},
{id:"headers",title:"Security configuration",category:"CASE FILE 05",time:"6 MIN",level:"INTERMEDIATE",headline:"The quiet power of <em>headers.</em>",lead:"A web app has correct access checks, safe queries, and escaped output. Configuration still matters: browsers need clear instructions for handling content and connections.",storyTitle:"Case: Launch checklist",story:"A reviewer checks response configuration before launch. The app has no clear Content Security Policy, permits framing by other sites, and does not consistently enforce HTTPS. These omissions can increase the impact of other mistakes.",vulnerable:`// Minimal response setup with no browser policy.
app.get("/", (req, res) => {
  res.sendFile("index.html");
});`,hint:["Response headers are browser-facing policy. They reduce the impact of some bugs but do not replace secure application logic.","Look into Content-Security-Policy, frame-ancestors, X-Content-Type-Options, and HSTS after HTTPS is correctly deployed."],question:"Which statement about security headers is correct?",options:["Security headers fix authorization bugs automatically","A Content Security Policy is defense-in-depth and does not replace safe coding","HSTS should be enabled before HTTPS works"],answer:1,explain:"Security headers help browsers enforce safer behavior, but they are an additional layer. Authorization, validation, and output handling still belong in application code.",fix:`// Example policy: review and tailor it.
res.setHeader("Content-Security-Policy",
  "default-src 'self'; object-src 'none'; base-uri 'self'; frame-ancestors 'none'");
res.setHeader("X-Content-Type-Options", "nosniff");
res.setHeader("Referrer-Policy", "strict-origin-when-cross-origin");`,fixText:"Deploy HTTPS first, then consider HSTS. Test a restrictive CSP against the real app before enforcing it."}
];

const nav = document.getElementById("lesson-nav");
const content = document.getElementById("lesson-content");
let current = 0;
let hintIndex = {};
let completed = JSON.parse(localStorage.getItem("securelab-completed") || "[]");
let revealed = {};
const esc = value => String(value).replace(/[&<>"']/g, char => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[char]));
function progress() {
  document.getElementById("progress-label").textContent = completed.length + " / " + lessons.length + " COMPLETED";
  document.getElementById("progress-bar").style.width = (completed.length / lessons.length * 100) + "%";
}
function renderNav() {
  nav.innerHTML = lessons.map((lesson, i) => '<button class="nav-item ' + (i === current ? "active" : "") + '" data-index="' + i + '"><span class="nav-number">' + String(i + 1).padStart(2, "0") + '</span><span>' + esc(lesson.title) + '</span>' + (completed.includes(lesson.id) ? '<span class="nav-check">✓</span>' : "") + '</button>').join("");
  nav.querySelectorAll("button").forEach(button => button.addEventListener("click", () => { current = Number(button.dataset.index); render(); }));
  progress();
}
function codeBlock(code, label) {
  return '<div class="code-window"><div class="code-head"><div class="code-dots"><i></i><i></i><i></i></div><span class="code-label">' + label + '</span></div><pre><code>' + esc(code) + '</code></pre></div>';
}
function fixMarkup(lesson) {
  return '<div class="fix-card"><div class="step-label">✓ SAFER IMPLEMENTATION</div><h3 style="font-size:13px;margin:8px 0">The defensive pattern</h3><p>' + esc(lesson.fixText) + '</p></div>' + codeBlock(lesson.fix, "SAFER PATTERN");
}
function render() {
  const lesson = lessons[current];
  renderNav();
  document.getElementById("crumb-current").textContent = lesson.category;
  hintIndex[lesson.id] ??= 0;
  let html = '<div class="hero"><div class="lesson-kicker">' + lesson.category + ' · ' + String(current + 1).padStart(2, "0") + ' / 06</div><h1>' + lesson.headline + '</h1><p class="lead">' + lesson.lead + '</p><div class="meta-row"><span class="pill">◷ ' + lesson.time + '</span><span class="pill">◎ ' + lesson.level + '</span><span class="pill">LOCAL LAB</span></div></div>';
  if (lesson.story) {
    html += '<h2 class="section-title">The scenario</h2><div class="story-card"><div class="step-label">◈ INCIDENT BRIEF</div><h3 style="font-size:14px;margin:0 0 9px">' + lesson.storyTitle + '</h3><p>' + lesson.story + '</p></div><h2 class="section-title">Inspect the code</h2><p class="lead" style="font-size:11px">This intentionally flawed snippet is a teaching example. Find the missing security decision.</p>' + codeBlock(lesson.vulnerable, "VULNERABLE EXAMPLE") + '<h2 class="section-title">Unlock a hint</h2><div class="hint-box"><div class="hint-top"><strong>✳ GUIDED HINT</strong><span class="hint-count">' + Math.min(hintIndex[lesson.id], lesson.hint.length) + ' / ' + lesson.hint.length + ' REVEALED</span></div><p>' + (hintIndex[lesson.id] ? esc(lesson.hint.slice(0, hintIndex[lesson.id]).join(" ")) : "No hints revealed yet. Think about what the server is trusting.") + '</p><div class="button-row" style="margin-bottom:0"><button class="btn" id="hint-btn" ' + (hintIndex[lesson.id] >= lesson.hint.length ? "disabled" : "") + '>Reveal next hint ↗</button></div></div>';
  } else {
    html += '<h2 class="section-title">The review checklist</h2><div class="two-col">' + lesson.points.map(point => '<div class="mini-card"><span class="mini-icon">◈</span><strong>' + point[0] + ' · ' + point[1] + '</strong><p>' + point[2] + '</p></div>').join("") + '</div><div class="content-card" style="margin-top:18px"><div class="step-label">CORE PRINCIPLE</div><p style="margin-top:10px">' + lesson.takeaway + '</p></div>';
  }
  html += '<h2 class="section-title">Your analysis</h2><div class="quiz-card"><h3>' + lesson.question + '</h3><div class="answer-list">' + lesson.options.map((option, i) => '<label class="answer-option"><input type="radio" name="answer" value="' + i + '"><span>' + esc(option) + '</span></label>').join("") + '</div><button class="btn primary" id="check-answer">Check my reasoning</button><div id="answer-feedback"></div></div>';
  if (revealed[lesson.id]) html += fixMarkup(lesson);
  html += '<div class="lesson-end"><small>' + (completed.includes(lesson.id) ? "✓ LESSON COMPLETED" : "LEARNING IN PROGRESS") + '</small><div class="button-row" style="margin:0"><button class="btn ghost" id="prev" ' + (current === 0 ? "disabled" : "") + '>← Previous</button><button class="btn primary" id="next">' + (current === lessons.length - 1 ? "Back to start" : "Next lesson") + ' →</button></div></div>';
  content.innerHTML = html;
  document.getElementById("hint-btn")?.addEventListener("click", () => { hintIndex[lesson.id] = Math.min(hintIndex[lesson.id] + 1, lesson.hint.length); render(); });
  document.getElementById("check-answer").addEventListener("click", () => {
    const selected = document.querySelector('input[name="answer"]:checked');
    const box = document.getElementById("answer-feedback");
    if (!selected) { box.innerHTML = '<div class="feedback bad">Choose an answer first. Your reasoning matters more than speed.</div>'; return; }
    const correct = Number(selected.value) === lesson.answer;
    box.innerHTML = '<div class="feedback ' + (correct ? "good" : "bad") + '">' + (correct ? "✓ Good reasoning. " : "Not quite. Revisit the scenario. ") + esc(lesson.explain) + '</div>';
    if (correct) {
      revealed[lesson.id] = true;
      if (!completed.includes(lesson.id)) { completed.push(lesson.id); localStorage.setItem("securelab-completed", JSON.stringify(completed)); }
      renderNav();
      if (!content.querySelector(".fix-card")) box.insertAdjacentHTML("afterend", fixMarkup(lesson));
    }
  });
  document.getElementById("prev").addEventListener("click", () => { if (current > 0) { current--; render(); } });
  document.getElementById("next").addEventListener("click", () => { current = current === lessons.length - 1 ? 0 : current + 1; render(); });
}
document.getElementById("theme-toggle").addEventListener("click", () => document.body.classList.toggle("light"));
render();