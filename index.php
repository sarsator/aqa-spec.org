<?php
declare(strict_types=1);

// Detect language from URL
$supported = ['en','fr','de','es','it','pt','nl','pl','ro','sv','cs','zh','ja'];
$uri = trim($_SERVER['REQUEST_URI'] ?? '/', '/');
$parts = explode('/', $uri);
$lang = in_array($parts[0], $supported) ? $parts[0] : 'en';

// Load language file
$lang_file = __DIR__ . '/lang/' . $lang . '.json';
if (!file_exists($lang_file)) {
    $lang_file = __DIR__ . '/lang/en.json';
    $lang = 'en';
}
$t = json_decode(file_get_contents($lang_file), true);
if (!$t) {
    http_response_code(500);
    echo 'Language file error';
    exit;
}

$base_url = 'https://aqa-spec.org';
$canonical = $lang === 'en' ? $base_url . '/' : $base_url . '/' . $lang . '/';
$css_path = $lang === 'en' ? 'style.css' : '../style.css';
$badge_path = $lang === 'en' ? 'badges' : '../badges';

// Helper
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($t['title']) ?></title>
  <meta name="description" content="<?= e($t['meta_description']) ?>">
  <meta property="og:title" content="<?= e($t['og_title']) ?>">
  <meta property="og:description" content="<?= e($t['og_description']) ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <meta property="og:type" content="website">
  <link rel="canonical" href="<?= e($canonical) ?>">
<?php foreach ($supported as $sl): ?>
  <link rel="alternate" hreflang="<?= $sl ?>" href="<?= $sl === 'en' ? $base_url . '/' : $base_url . '/' . $sl . '/' ?>">
<?php endforeach; ?>
  <link rel="alternate" hreflang="x-default" href="<?= $base_url ?>/">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='80' font-family='sans-serif' fill='%232563eb'>A</text></svg>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $css_path ?>">
<?php if ($lang === 'zh'): ?>
  <style>body { font-family: 'Inter', 'PingFang SC', 'Microsoft YaHei', sans-serif; }</style>
<?php elseif ($lang === 'ja'): ?>
  <style>body { font-family: 'Inter', 'Hiragino Kaku Gothic ProN', 'Yu Gothic', 'Meiryo', sans-serif; }</style>
<?php endif; ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebSite",
    "name": "AQA — AI Question Answer",
    "url": "https://aqa-spec.org/",
    "description": "Open specification for structured Q&A content optimized for AI comprehension and citation.",
    "publisher": {
      "@type": "Organization",
      "name": "AI Labs Solutions",
      "url": "https://ai-labs-solutions.fr",
      "founder": {
        "@type": "Person",
        "name": "Davy Abderrahman"
      }
    },
    "license": "https://opensource.org/licenses/MIT"
  }
  </script>
<?php if ($lang === 'en'): // AQA Full + Shield only on English (source of truth) ?>
  <script type="application/ld+json">
  <?php readfile(__DIR__ . '/aqa-block.json'); ?>
  </script>
<?php endif; ?>
</head>
<body>

  <nav class="lang-nav">
    <div class="container">
<?php foreach ($supported as $sl): ?>
      <a href="<?= $sl === 'en' ? '/' : '/' . $sl . '/' ?>"<?= $sl === $lang ? ' style="color:#fff"' : '' ?>><?= $sl ?></a>
<?php endforeach; ?>
    </div>
  </nav>

  <header class="site-header">
    <div class="container">
      <h1><?= e($t['h1']) ?></h1>
      <p class="subtitle"><?= e($t['subtitle']) ?></p>
      <div class="header-meta">
        <span class="badge">v1.2.2-draft</span>
        <a href="https://github.com/sarsator/aqa-specification">GitHub</a>
        <a href="https://opensource.org/licenses/MIT">MIT License</a>
        <span><?= e($t['schema_compatible']) ?></span>
      </div>
    </div>
  </header>

  <main class="container">

    <section id="problem">
      <h2><?= e($t['problem_title']) ?></h2>
      <p><?= e($t['problem_text']) ?></p>
    </section>

    <section id="solution">
      <h2><?= e($t['solution_title']) ?></h2>
      <p><?= e($t['solution_text']) ?></p>
    </section>

    <section id="signals">
      <h2><?= e($t['signals_title']) ?></h2>
      <table>
        <thead>
          <tr>
            <th><?= e($t['signals_col_signal']) ?></th>
            <th><?= e($t['signals_col_description']) ?></th>
          </tr>
        </thead>
        <tbody>
<?php foreach ($t['signals'] as $s): ?>
          <tr><td><?= $s['signal'] ?></td><td><?= e($s['desc']) ?></td></tr>
<?php endforeach; ?>
        </tbody>
      </table>
    </section>

    <section id="conformance">
      <h2><?= e($t['conformance_title']) ?></h2>
      <div class="levels">
        <div class="level-card">
          <h3><?= e($t['basic_title']) ?></h3>
          <p class="effort"><?= e($t['basic_effort']) ?></p>
          <p><?= e($t['basic_desc']) ?></p>
          <img src="<?= $badge_path ?>/aqa-basic.svg" alt="AQA Basic badge" width="90" height="20">
        </div>
        <div class="level-card">
          <h3><?= e($t['standard_title']) ?></h3>
          <p class="effort"><?= e($t['standard_effort']) ?></p>
          <p><?= e($t['standard_desc']) ?></p>
          <img src="<?= $badge_path ?>/aqa-standard.svg" alt="AQA Standard badge" width="106" height="20">
        </div>
        <div class="level-card">
          <h3><?= e($t['full_title']) ?></h3>
          <p class="effort"><?= e($t['full_effort']) ?></p>
          <p><?= e($t['full_desc']) ?></p>
          <img src="<?= $badge_path ?>/aqa-full.svg" alt="AQA Full badge" width="80" height="20">
        </div>
      </div>
    </section>

    <section id="shield">
      <h2><?= e($t['shield_title']) ?></h2>
      <p><?= $t['shield_text'] ?></p>
      <p><img src="<?= $badge_path ?>/aqa-shield.svg" alt="AQA Shield badge" width="94" height="20"></p>
    </section>

    <section id="quickstart">
      <h2><?= e($t['quickstart_title']) ?></h2>
      <p><?= e($t['quickstart_intro']) ?></p>
      <div class="code-block">
        <button class="copy-btn" onclick="copyCode(this)"><?= e($t['copy_btn']) ?></button>
        <pre><code>{
  "@context": [
    "https://schema.org",
    "https://aqa-spec.org/ns/context.jsonld"
  ],
  "@type": "Article",
  "headline": "Frequently Asked Questions",
  "author": {
    "@type": "Organization",
    "name": "Your Company"
  },
  "datePublished": "2024-01-15",
  "dateModified": "2026-03-20",
  "inLanguage": "en",
  "mainEntity": {
    "@type": "FAQPage",
    "mainEntity": [
      {
        "@type": "Question",
        "name": "Your question here?",
        "dateCreated": "2024-01-15",
        "dateModified": "2026-03-20",
        "citation": "https://source-url.com/document",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "Your answer here."
        }
      }
    ]
  }
}</code></pre>
      </div>
      <p><?= e($t['quickstart_validate']) ?></p>
      <pre><code>pip install jsonschema requests
python validators/validate.py your-file.jsonld</code></pre>
      <p><a href="https://github.com/sarsator/aqa-specification/blob/master/docs/migration-guide.md"><?= e($t['quickstart_migration']) ?></a> — <?= e($t['quickstart_migration_desc']) ?></p>
    </section>

    <section id="features">
      <h2><?= e($t['features_title']) ?></h2>
      <div class="feature-groups">
        <div class="feature-group">
          <h3><?= e($t['protection_title']) ?></h3>
          <ul>
<?php foreach ($t['protection_items'] as $item): ?>
            <li><?= $item ?></li>
<?php endforeach; ?>
          </ul>
        </div>
        <div class="feature-group">
          <h3><?= e($t['enrichment_title']) ?></h3>
          <ul>
<?php foreach ($t['enrichment_items'] as $item): ?>
            <li><?= $item ?></li>
<?php endforeach; ?>
          </ul>
        </div>
        <div class="feature-group">
          <h3><?= e($t['feedback_title']) ?></h3>
          <ul>
<?php foreach ($t['feedback_items'] as $item): ?>
            <li><?= $item ?></li>
<?php endforeach; ?>
          </ul>
        </div>
        <div class="feature-group">
          <h3><?= e($t['distribution_title']) ?></h3>
          <ul>
<?php foreach ($t['distribution_items'] as $item): ?>
            <li><?= $item ?></li>
<?php endforeach; ?>
          </ul>
        </div>
      </div>
    </section>

    <section id="resources">
      <h2><?= e($t['resources_title']) ?></h2>
      <ul class="resources-list">
<?php foreach ($t['resources'] as $r): ?>
        <li><a href="<?= e($r['url']) ?>"><?= e($r['label']) ?></a> — <?= e($r['desc']) ?></li>
<?php endforeach; ?>
      </ul>
    </section>

    <section id="faq">
      <h2><?= e($t['faq_title']) ?></h2>
      <p><?= $t['faq_intro'] ?></p>
<?php foreach ($t['faq'] as $faq): ?>
      <div class="faq-item">
        <h3><?= e($faq['q']) ?></h3>
        <p><?= e($faq['a']) ?></p>
      </div>
<?php endforeach; ?>
    </section>

    <section id="philosophy">
      <h2><?= e($t['philosophy_title']) ?></h2>
      <p><?= e($t['philosophy_text_1']) ?></p>
      <p><?= e($t['philosophy_text_2']) ?></p>
    </section>

  </main>

  <footer class="site-footer">
    <div class="container">
      <p><?= e($t['footer_spec']) ?> — <a href="https://opensource.org/licenses/MIT">MIT License</a></p>
      <p><?= e($t['footer_created']) ?> — <a href="https://ai-labs-solutions.fr">AI Labs Solutions</a></p>
      <p><a href="https://github.com/sarsator/aqa-specification">GitHub</a></p>
    </div>
  </footer>

  <script>
  function copyCode(btn) {
    var code = btn.parentElement.querySelector('code');
    navigator.clipboard.writeText(code.textContent).then(function() {
      btn.textContent = <?= json_encode($t['copy_btn_done']) ?>;
      setTimeout(function() { btn.textContent = <?= json_encode($t['copy_btn']) ?>; }, 2000);
    });
  }
  </script>

</body>
</html>
