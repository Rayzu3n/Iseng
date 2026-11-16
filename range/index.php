<?php
declare(strict_types=1);

session_name('securelab_range');
session_save_path('/tmp');
session_start();

$host = explode(':', $_SERVER['HTTP_HOST'] ?? 'localhost')[0];
if (!in_array($host, ['localhost', '127.0.0.1'], true)) {
    http_response_code(403);
    exit('This intentionally vulnerable lab is available only through localhost.');
}

$dbDirectory = '/var/lib/lab';
if (!is_dir($dbDirectory)) {
    mkdir($dbDirectory, 0750, true);
}

$pdo = new PDO('sqlite:' . $dbDirectory . '/range.sqlite', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function seedDatabase(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS profiles (
        id INTEGER PRIMARY KEY,
        user_id INTEGER NOT NULL,
        display_name TEXT NOT NULL,
        email TEXT NOT NULL,
        private_note TEXT NOT NULL
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS comments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        author TEXT NOT NULL,
        body TEXT NOT NULL,
        created_at TEXT NOT NULL
    )');

    $pdo->exec("INSERT OR IGNORE INTO users (id, username, password, role) VALUES
        (1, 'admin', 'admin123', 'administrator'),
        (2, 'student', 'student123', 'student'),
        (3, 'teacher', 'teacher123', 'teacher')");

    $pdo->exec("INSERT OR IGNORE INTO profiles (id, user_id, display_name, email, private_note) VALUES
        (1, 1, 'Ari Admin', 'ari.admin@example.test', 'Demo administrator profile.'),
        (2, 2, 'Nadia Student', 'nadia.student@example.test', 'Demo student project draft is saved.'),
        (3, 3, 'Raka Teacher', 'raka.teacher@example.test', 'Demo staff timetable review.')");
}

seedDatabase($pdo);

if (!isset($_SESSION['demo_user_id'])) {
    $_SESSION['demo_user_id'] = 2;
}

$allowedLabs = ['sqli', 'xss', 'idor'];
$lab = (string)($_GET['lab'] ?? 'sqli');
if (!in_array($lab, $allowedLabs, true)) {
    $lab = 'sqli';
}

$loginResult = null;
$loginError = '';
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'reset') {
        $pdo->exec('DELETE FROM comments');
        $pdo->exec("DELETE FROM sqlite_sequence WHERE name = 'comments'");
        header('Location: ?lab=xss&reset=1');
        exit;
    }

    if ($action === 'sqli_login' && $lab === 'sqli') {
        $username = (string)($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        // VULNERABLE DEMO: untrusted input is concatenated into SQL.
        $sql = "SELECT id, username, role FROM users WHERE username = '$username' AND password = '$password' LIMIT 1";

        try {
            $loginResult = $pdo->query($sql)->fetch() ?: null;
        } catch (PDOException $exception) {
            $loginError = $exception->getMessage();
        }
    }

    if ($action === 'add_comment' && $lab === 'xss') {
        $body = trim((string)($_POST['body'] ?? ''));

        if ($body !== '' && strlen($body) <= 1000) {
            // The insert is parameterized. The deliberate flaw is at output time below.
            $insert = $pdo->prepare(
                'INSERT INTO comments (author, body, created_at) VALUES (?, ?, ?)'
            );
            $insert->execute(['Guest Learner', $body, date('Y-m-d H:i:s')]);
            header('Location: ?lab=xss&posted=1');
            exit;
        }

        $flash = 'Enter a comment containing 1 to 1000 characters.';
    }
}

if (isset($_GET['reset'])) {
    $flash = 'Lab comments reset. The fictional seed data remains available.';
}
if (isset($_GET['posted'])) {
    $flash = 'Comment saved. Scroll to the noticeboard feed to inspect the rendered output.';
}

$currentUserQuery = $pdo->prepare(
    'SELECT id, username, role FROM users WHERE id = ?'
);
$currentUserQuery->execute([$_SESSION['demo_user_id']]);
$currentDemoUser = $currentUserQuery->fetch();

$comments = $pdo->query(
    'SELECT author, body, created_at FROM comments ORDER BY id DESC'
)->fetchAll();

$requestedProfileId = max(1, (int)($_GET['profile'] ?? 2));

// VULNERABLE DEMO: requested record is loaded without an ownership check.
$profileQuery = $pdo->query(
    'SELECT id, user_id, display_name, email, private_note
     FROM profiles WHERE id = ' . $requestedProfileId
);
$requestedProfile = $profileQuery->fetch();

$hints = [
    'sqli' => [
        'Notice that the username and password values are placed directly inside single quotes in the SQL string.',
        'The database parses the combined string as SQL syntax. A comment marker can make the rest of a statement irrelevant.',
        "In the username field, try: ' OR 1=1 -- x . Put any filler in the password field. This only selects a fictional row in this isolated demo database."
    ],
    'xss' => [
        'The submitted comment is stored as data. The important question is how it is rendered back into the page.',
        'Find the output line in the comments loop. Is the comment escaped with htmlspecialchars(), or interpreted as HTML?',
        "For a harmless local proof, submit this comment: <svg onload=alert(1)>. It should only display a browser dialog in this local lab."
    ],
    'idor' => [
        'The demo session is Nadia Student, user ID 2, and her own profile record is ID 2.',
        'The server reads a requested profile ID from the URL. Look for an ownership comparison between the requested record and the current session user.',
        'Use the profile selector to compare IDs 1, 2, and 3. The server returns the selected record even though the demo user owns only profile 2.'
    ],
];

$badCode = [
    'sqli' => <<<'CODE'
$sql = "SELECT id, username, role FROM users
        WHERE username = '$username'
        AND password = '$password' LIMIT 1";

$user = $pdo->query($sql)->fetch();
CODE,
    'xss' => <<<'CODE'
foreach ($comments as $comment) {
    // Unsafe: browser parses stored content as HTML.
    echo $comment['body'];
}
CODE,
    'idor' => <<<'CODE'
$id = (int) $_GET['profile'];

$profile = $pdo->query(
    'SELECT * FROM profiles WHERE id = ' . $id
)->fetch();

// No check that this record belongs to the session user.
CODE,
];

$goodCode = [
    'sqli' => <<<'CODE'
$query = $pdo->prepare(
    'SELECT id, username, role
     FROM users WHERE username = ? LIMIT 1'
);
$query->execute([$username]);
$user = $query->fetch();

// Verify a stored password hash with password_verify().
CODE,
    'xss' => <<<'CODE'
foreach ($comments as $comment) {
    echo htmlspecialchars(
        $comment['body'],
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
CODE,
    'idor' => <<<'CODE'
$query = $pdo->prepare(
    'SELECT * FROM profiles
     WHERE id = ? AND user_id = ?'
);
$query->execute([
    $requestedId,
    $_SESSION['user_id']
]);
$profile = $query->fetch();
CODE,
];

$lessonTitles = [
    'sqli' => 'SQL injection',
    'xss' => 'Stored XSS',
    'idor' => 'IDOR / access control',
];
$lessonNumbers = ['sqli' => '01', 'xss' => '02', 'idor' => '03'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#0d1219">
  <title>Injection Range | SecureLab</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="top">
  <div class="brand"><span class="mark">S</span><span>secure<span>lab</span></span></div>
  <div class="top-right"><span class="live"></span>LOOPBACK ONLY &nbsp;·&nbsp; ISOLATED RANGE</div>
</header>

<main>
  <section class="intro">
    <div>
      <div class="kicker">HANDS-ON SECURITY TRACK / 001</div>
      <h1>Injection Range</h1>
      <p>A real PHP + SQLite practice app. Submit inputs, observe the vulnerable behavior, inspect the source, then compare it with a safer implementation. All records are fictional.</p>
    </div>
    <span class="local-tag">● LOCAL SANDBOX</span>
  </section>

  <div class="warning">
    <strong>Safety boundary:</strong> this app intentionally contains vulnerabilities. It is bound to 127.0.0.1 and uses an isolated Docker network. Keep it local. Never publish this container or reuse its vulnerable patterns.
  </div>

  <nav class="tabs" aria-label="Labs">
    <a class="tab <?= $lab === 'sqli' ? 'active' : '' ?>" href="?lab=sqli"><b>01</b> SQL injection</a>
    <a class="tab <?= $lab === 'xss' ? 'active' : '' ?>" href="?lab=xss"><b>02</b> Stored XSS</a>
    <a class="tab <?= $lab === 'idor' ? 'active' : '' ?>" href="?lab=idor"><b>03</b> IDOR</a>
  </nav>

  <?php if ($flash !== ''): ?>
    <div class="result"><?= e($flash) ?></div>
  <?php endif; ?>

  <?php if ($lab === 'sqli'): ?>
  <div class="grid">
    <section>
      <div class="card">
        <div class="eyebrow">CASE FILE <?= $lessonNumbers[$lab] ?> / AUTHENTICATION</div>
        <h2>A login form that trusts its input</h2>
        <p class="muted">Try a normal demo account first. Then inspect the query and see whether the database treats your input as data or as part of the SQL statement.</p>
        <form method="post" action="?lab=sqli">
          <input type="hidden" name="action" value="sqli_login">
          <div class="form-row">
            <label for="username">Username</label>
            <input id="username" name="username" autocomplete="off" placeholder="e.g. student" required>
          </div>
          <div class="form-row">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="e.g. student123" required>
          </div>
          <button class="btn" type="submit">Submit login ↗</button>
        </form>

        <?php if ($loginResult): ?>
          <div class="result">
            <strong>The database returned a matching user.</strong><br>
            Signed in as <?= e($loginResult['username']) ?> · role: <?= e($loginResult['role']) ?> · record ID: <?= (int)$loginResult['id'] ?><br>
            <span class="muted">Inspect why the database accepted this combination.</span>
          </div>
        <?php elseif ($loginError !== ''): ?>
          <div class="result fail"><strong>Database rejected the query.</strong><br><?= e($loginError) ?></div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'sqli_login'): ?>
          <div class="result fail">No matching user. Check the demo credentials or inspect the query construction.</div>
        <?php endif; ?>

        <details><summary>Hint 1 · Find the trust boundary</summary><p><?= e($hints['sqli'][0]) ?></p></details>
        <details><summary>Hint 2 · Understand SQL parsing</summary><p><?= e($hints['sqli'][1]) ?></p></details>
        <details><summary>Hint 3 · Think about a boolean condition</summary><p><?= e($hints['sqli'][2]) ?></p></details>
      </div>

      <div class="card">
        <span class="vuln-label">VULNERABLE CODE · index.php</span>
        <h3>What the server executes</h3>
        <pre class="code"><?= e($badCode['sqli']) ?></pre>
        <div class="callout"><strong>Think:</strong> the query is built by combining SQL instructions with values supplied by the browser. What if a value changes the meaning of the statement?</div>
      </div>

      <div class="card">
        <span class="safe-label">SAFER PATTERN</span>
        <h3>Keep SQL structure separate from values</h3>
        <pre class="code"><?= e($goodCode['sqli']) ?></pre>
        <p class="muted">Prepared statements bind input as values rather than allowing it to become SQL syntax. A production app should store password hashes and verify them using password_verify().</p>
      </div>
    </section>

    <aside>
      <div class="card">
        <div class="eyebrow">DEMO CREDENTIALS</div>
        <h2>Seed accounts</h2>
        <p class="muted">These fictional accounts exist only in this local database.</p>
        <div class="profile">
          <div class="field"><span>admin</span><strong>admin123</strong></div>
          <div class="field"><span>student</span><strong>student123</strong></div>
          <div class="field"><span>teacher</span><strong>teacher123</strong></div>
        </div>
      </div>
      <div class="card">
        <div class="eyebrow">LEARNING OBJECTIVE</div>
        <h2>What to notice</h2>
        <ul class="hint-list">
          <li>Input validation is not the same as SQL parameterization.</li>
          <li>The database parses the combined query after unsafe concatenation.</li>
          <li>Prepared statements protect the query structure.</li>
        </ul>
      </div>
    </aside>
  </div>

  <?php elseif ($lab === 'xss'): ?>
  <div class="grid">
    <section>
      <div class="card">
        <div class="eyebrow">CASE FILE <?= $lessonNumbers[$lab] ?> / USER CONTENT</div>
        <h2>Community noticeboard</h2>
        <p class="muted">Comments are stored in SQLite and rendered below. This lab deliberately omits output encoding in one place. Keep the proof harmless and local.</p>
        <form method="post" action="?lab=xss">
          <input type="hidden" name="action" value="add_comment">
          <div class="form-row">
            <label for="body">Write a comment</label>
            <textarea id="body" name="body" maxlength="1000" placeholder="Write a normal comment, or inspect the hints..." required></textarea>
          </div>
          <button class="btn" type="submit">Post comment ↗</button>
        </form>
        <details><summary>Hint 1 · Stored or reflected?</summary><p><?= e($hints['xss'][0]) ?></p></details>
        <details><summary>Hint 2 · Find the output sink</summary><p><?= e($hints['xss'][1]) ?></p></details>
        <details><summary>Hint 3 · Harmless local proof</summary><p><?= e($hints['xss'][2]) ?></p></details>
      </div>

      <div class="card">
        <div class="eyebrow">LIVE OUTPUT / <?= count($comments) ?> COMMENTS</div>
        <h2>Noticeboard feed</h2>
        <?php if (!$comments): ?>
          <div class="empty">No comments yet. Post one above to populate the feed.</div>
        <?php endif; ?>
        <?php foreach ($comments as $comment): ?>
          <article class="comment">
            <div class="comment-meta"><?= e($comment['author']) ?> · <?= e($comment['created_at']) ?></div>
            <div class="comment-body"><?php
              // VULNERABLE DEMO: stored user content is intentionally rendered as HTML.
              echo $comment['body'];
            ?></div>
          </article>
        <?php endforeach; ?>
      </div>

      <div class="card">
        <span class="vuln-label">VULNERABLE CODE · index.php</span>
        <h3>The dangerous output operation</h3>
        <pre class="code"><?= e($badCode['xss']) ?></pre>
        <div class="callout"><strong>Think:</strong> the database insert uses a prepared statement. The flaw happens later, when saved text is sent to the browser without encoding.</div>
      </div>

      <div class="card">
        <span class="safe-label">SAFER PATTERN</span>
        <h3>Render plain text as plain text</h3>
        <pre class="code"><?= e($goodCode['xss']) ?></pre>
        <p class="muted">For plain-text comments, context-aware HTML escaping prevents the browser from interpreting submitted markup as page elements or script-capable content. Rich text needs a maintained sanitizer and a strict allowlist.</p>
      </div>
    </section>

    <aside>
      <div class="card">
        <div class="eyebrow">LEARNING OBJECTIVE</div>
        <h2>Follow the data</h2>
        <ul class="hint-list">
          <li>Find where the comment enters the app.</li>
          <li>Follow where it is stored.</li>
          <li>Find where it is placed into the HTML response.</li>
          <li>Compare that output with the safer implementation.</li>
        </ul>
      </div>
      <div class="card">
        <div class="eyebrow">RESET</div>
        <h2>Clear the noticeboard</h2>
        <p class="muted">Remove all comments created during this practice session.</p>
        <form class="reset" method="post" action="?lab=xss" onsubmit="return confirm('Clear all lab comments?')">
          <input type="hidden" name="action" value="reset">
          <button class="btn danger" type="submit">Reset lab comments</button>
        </form>
      </div>
    </aside>
  </div>

  <?php else: ?>
  <div class="grid">
    <section>
      <div class="card">
        <div class="eyebrow">CASE FILE <?= $lessonNumbers[$lab] ?> / OBJECT AUTHORIZATION</div>
        <h2>Student profile viewer</h2>
        <p class="muted">The demo session belongs to <strong><?= e($currentDemoUser['username'] ?? 'student') ?></strong>, user ID <?= (int)$_SESSION['demo_user_id'] ?>. Request a profile and inspect whether the server checks ownership.</p>
        <form method="get" action="">
          <input type="hidden" name="lab" value="idor">
          <div class="form-row">
            <label for="profile">Profile record ID</label>
            <select id="profile" name="profile">
              <option value="1" <?= $requestedProfileId === 1 ? 'selected' : '' ?>>1 · Ari Admin</option>
              <option value="2" <?= $requestedProfileId === 2 ? 'selected' : '' ?>>2 · Nadia Student</option>
              <option value="3" <?= $requestedProfileId === 3 ? 'selected' : '' ?>>3 · Raka Teacher</option>
            </select>
          </div>
          <button class="btn" type="submit">Load profile ↗</button>
        </form>

        <?php if ($requestedProfile): ?>
          <div class="profile">
            <div class="field"><span>Record ID</span><strong><?= (int)$requestedProfile['id'] ?></strong></div>
            <div class="field"><span>Name</span><strong><?= e($requestedProfile['display_name']) ?></strong></div>
            <div class="field"><span>Email</span><strong><?= e($requestedProfile['email']) ?></strong></div>
            <div class="field"><span>Private note</span><strong><?= e($requestedProfile['private_note']) ?></strong></div>
            <div class="field"><span>Owner user ID</span><strong><?= (int)$requestedProfile['user_id'] ?></strong></div>
          </div>
        <?php else: ?>
          <div class="result fail">Profile not found.</div>
        <?php endif; ?>

        <details><summary>Hint 1 · Authentication vs authorization</summary><p><?= e($hints['idor'][0]) ?></p></details>
        <details><summary>Hint 2 · Inspect the server lookup</summary><p><?= e($hints['idor'][1]) ?></p></details>
        <details><summary>Hint 3 · Compare the demo records</summary><p><?= e($hints['idor'][2]) ?></p></details>
      </div>

      <div class="card">
        <span class="vuln-label">VULNERABLE CODE · index.php</span>
        <h3>Record lookup with no ownership check</h3>
        <pre class="code"><?= e($badCode['idor']) ?></pre>
        <div class="callout"><strong>Think:</strong> a record ID is not a permission. The server must compare the requested object with the current user's authorization.</div>
      </div>

      <div class="card">
        <span class="safe-label">SAFER PATTERN</span>
        <h3>Scope the lookup to the authenticated user</h3>
        <pre class="code"><?= e($goodCode['idor']) ?></pre>
        <p class="muted">For staff access to another user's record, add an explicit role or policy check on the server. Do not rely on hiding a selector or button in the browser.</p>
      </div>
    </section>

    <aside>
      <div class="card">
        <div class="eyebrow">DEMO SESSION</div>
        <h2>Nadia Student</h2>
        <p class="muted">The lab fixes the demo identity to user ID 2 so you can focus on authorization.</p>
        <div class="profile">
          <div class="field"><span>Session user ID</span><strong>2</strong></div>
          <div class="field"><span>Own profile ID</span><strong>2</strong></div>
          <div class="field"><span>Expected access</span><strong>Own record only</strong></div>
        </div>
      </div>
      <div class="card">
        <div class="eyebrow">LEARNING OBJECTIVE</div>
        <h2>Ask the right question</h2>
        <ul class="hint-list">
          <li>Who is making the request?</li>
          <li>Which record is requested?</li>
          <li>What server-side rule connects the user to that record?</li>
        </ul>
      </div>
    </aside>
  </div>
  <?php endif; ?>

  <footer class="footer">
    <span>SECURELAB INJECTION RANGE · FICTIONAL DATA</span>
    <span>LOCALHOST ONLY · DO NOT DEPLOY</span>
  </footer>
</main>
</body>
</html>
