<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AQA Hub — Update Aggregation Service</title>
  <meta name="description" content="AQA Hub aggregates update notifications from AQA publishers and exposes them to AI systems. Open protocol, free to use.">
  <meta property="og:title" content="AQA Hub — Update Aggregation Service">
  <meta property="og:description" content="Centralized update aggregation for AQA-enabled FAQ content. IndexNow for FAQ.">
  <meta property="og:url" content="https://aqa-spec.org/hub/">
  <meta property="og:type" content="website">
  <link rel="canonical" href="https://aqa-spec.org/hub/">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='0.9em' font-size='80' font-family='sans-serif' fill='%232563eb'>H</text></svg>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 16px; }
    body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1a1a2e; background: #fff; line-height: 1.6; }
    a { color: #2563eb; text-decoration: none; }
    a:hover { text-decoration: underline; }
    .container { max-width: 800px; margin: 0 auto; padding: 0 1.5rem; }
    .site-header { background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 3rem 0 2.5rem; }
    .site-header h1 { font-size: 2.25rem; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 0.5rem; }
    .site-header .subtitle { font-size: 1.125rem; color: #4b5563; max-width: 600px; margin-bottom: 1.25rem; }
    .header-meta { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; font-size: 0.875rem; }
    .header-meta a { font-weight: 500; }
    section { padding: 2.5rem 0; border-bottom: 1px solid #e5e7eb; }
    section:last-of-type { border-bottom: none; }
    section h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; }
    section h3 { font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; margin-top: 1.5rem; }
    section p { margin-bottom: 1rem; }
    pre { background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem 1.25rem; overflow-x: auto; font-size: 0.85rem; line-height: 1.5; margin: 1rem 0; }
    code { font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace; }
    p code { background: #f3f4f6; padding: 0.15rem 0.35rem; border-radius: 3px; font-size: 0.85em; }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin: 1rem 0; }
    .stat-card { border: 1px solid #e5e7eb; border-radius: 4px; padding: 1rem; text-align: center; }
    .stat-card .value { font-size: 1.5rem; font-weight: 700; color: #2563eb; }
    .stat-card .label { font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem; }
    .method { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 3px; font-size: 0.8rem; font-weight: 700; font-family: monospace; margin-right: 0.5rem; }
    .method-post { background: #dbeafe; color: #1d4ed8; }
    .method-get { background: #d1fae5; color: #065f46; }
    .endpoint { font-family: monospace; font-weight: 600; }
    .param-table { width: 100%; border-collapse: collapse; margin: 0.75rem 0; font-size: 0.9rem; }
    .param-table th, .param-table td { text-align: left; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; }
    .param-table th { background: #f9fafb; font-weight: 600; }
    .required { color: #dc2626; font-size: 0.75rem; font-weight: 600; }
    .site-footer { padding: 2rem 0; border-top: 1px solid #e5e7eb; font-size: 0.85rem; color: #6b7280; text-align: center; }
    .site-footer p { margin-bottom: 0.3rem; }
    @media (max-width: 768px) {
      .site-header h1 { font-size: 1.75rem; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>

  <header class="site-header">
    <div class="container">
      <h1>AQA Hub</h1>
      <p class="subtitle">Update Aggregation Service for AQA-enabled content. IndexNow for FAQ.</p>
      <div class="header-meta">
        <a href="https://aqa-spec.org/">AQA Specification</a>
        <a href="https://github.com/sarsator/aqa-specification">GitHub</a>
        <span>Open protocol</span>
      </div>
    </div>
  </header>

  <main class="container">

    <section id="stats">
      <h2>Live Statistics</h2>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="value" id="stat-updates">--</div>
          <div class="label">Total Updates</div>
        </div>
        <div class="stat-card">
          <div class="value" id="stat-publishers">--</div>
          <div class="label">Publishers</div>
        </div>
        <div class="stat-card">
          <div class="value" id="stat-24h">--</div>
          <div class="label">Last 24h</div>
        </div>
        <div class="stat-card">
          <div class="value" id="stat-last">--</div>
          <div class="label">Last Update</div>
        </div>
      </div>
    </section>

    <section id="about">
      <h2>What is the AQA Hub?</h2>
      <p>The AQA Hub is a centralized intermediary that aggregates update notifications from multiple AQA publishers and exposes them to AI systems. When a publisher updates a FAQ answer, they notify the Hub. AI crawlers query the Hub to discover fresh content across all publishers, without polling each site individually.</p>
      <p>The Hub protocol is defined in <a href="https://github.com/sarsator/aqa-specification/blob/master/SPECIFICATION.md">Section 3.17 of the AQA Specification</a>. It is an open protocol — anyone can operate a Hub.</p>
    </section>

    <section id="api">
      <h2>API Reference</h2>

      <h3><span class="method method-post">POST</span> <span class="endpoint">/hub/api/v1/ping</span></h3>
      <p>Notify the Hub that AQA content has been updated.</p>

      <table class="param-table">
        <thead><tr><th>Field</th><th>Type</th><th>Description</th></tr></thead>
        <tbody>
          <tr><td><code>pageUrl</code> <span class="required">required</span></td><td>string (URL)</td><td>URL of the page containing AQA content</td></tr>
          <tr><td><code>questionName</code> <span class="required">required</span></td><td>string</td><td>The question that was updated</td></tr>
          <tr><td><code>previousVersion</code></td><td>string</td><td>Previous question version</td></tr>
          <tr><td><code>newVersion</code></td><td>string</td><td>New question version</td></tr>
          <tr><td><code>updateDate</code></td><td>string (ISO 8601)</td><td>When the update occurred</td></tr>
          <tr><td><code>changeDescription</code></td><td>string</td><td>What changed and why</td></tr>
          <tr><td><code>isNewQuestion</code></td><td>boolean</td><td>True if this is a new question</td></tr>
          <tr><td><code>publisher</code></td><td>string</td><td>Publisher name</td></tr>
          <tr><td><code>publisherUrl</code></td><td>string (URL)</td><td>Publisher website</td></tr>
          <tr><td><code>sector</code></td><td>string</td><td>NACE sector code (e.g. "69.20")</td></tr>
          <tr><td><code>language</code></td><td>string</td><td>BCP 47 language code (e.g. "fr")</td></tr>
          <tr><td><code>country</code></td><td>string</td><td>ISO 3166-1 alpha-2 (e.g. "FR")</td></tr>
        </tbody>
      </table>

      <p>Example:</p>
      <pre><code>curl -X POST https://aqa-spec.org/hub/api/v1/ping \
  -H "Content-Type: application/json" \
  -d '{
    "pageUrl": "https://www.example.com/faq",
    "questionName": "What is the corporate tax rate?",
    "newVersion": "3.0",
    "updateDate": "2026-04-03T14:30:00Z",
    "changeDescription": "Updated for 2026 Finance Act",
    "publisher": "Example Corp",
    "sector": "69.20",
    "language": "en",
    "country": "US"
  }'</code></pre>

      <p>Response: <code>202 Accepted</code></p>
      <pre><code>{
  "status": "accepted",
  "id": "a1b2c3d4e5f6g7h8",
  "message": "Update notification received."
}</code></pre>

      <h3><span class="method method-get">GET</span> <span class="endpoint">/hub/api/v1/updates</span></h3>
      <p>Query the aggregated update feed.</p>

      <table class="param-table">
        <thead><tr><th>Parameter</th><th>Type</th><th>Description</th></tr></thead>
        <tbody>
          <tr><td><code>since</code> <span class="required">required</span></td><td>string (ISO 8601)</td><td>Return updates after this date</td></tr>
          <tr><td><code>country</code></td><td>string</td><td>Filter by country (ISO 3166-1 alpha-2)</td></tr>
          <tr><td><code>sector</code></td><td>string</td><td>Filter by NACE sector code</td></tr>
          <tr><td><code>language</code></td><td>string</td><td>Filter by BCP 47 language code</td></tr>
          <tr><td><code>limit</code></td><td>integer</td><td>Max results (default: 100, max: 1000)</td></tr>
        </tbody>
      </table>

      <p>Example:</p>
      <pre><code>curl "https://aqa-spec.org/hub/api/v1/updates?since=2026-04-01T00:00:00Z&country=FR&limit=50"</code></pre>

      <p>Response: <code>200 OK</code></p>
      <pre><code>{
  "hub": "aqa-spec.org/hub",
  "queryTime": "2026-04-03T15:00:00Z",
  "totalResults": 42,
  "returned": 42,
  "updates": [...]
}</code></pre>

      <h3><span class="method method-get">GET</span> <span class="endpoint">/hub/api/v1/stats</span></h3>
      <p>Public statistics about the Hub.</p>

      <p>Example:</p>
      <pre><code>curl "https://aqa-spec.org/hub/api/v1/stats"</code></pre>

      <p>Response: <code>200 OK</code></p>
      <pre><code>{
  "hub": "aqa-spec.org/hub",
  "totalUpdates": 1234,
  "totalPublishers": 56,
  "last24h": 23,
  "lastUpdate": "2026-04-03T14:30:00Z",
  "topCountries": [{"country": "FR", "count": 45}],
  "topSectors": [{"sector": "69.20", "count": 20}]
}</code></pre>
    </section>

    <section id="limits">
      <h2>Rate Limits</h2>
      <p>The ping endpoint is rate-limited to 100 requests per IP address per hour. If you exceed this limit, you will receive a <code>429 Too Many Requests</code> response with a <code>Retry-After</code> header.</p>
      <p>The updates and stats endpoints are not rate-limited but responses are cached for 60 seconds.</p>
    </section>

    <section id="integration">
      <h2>Integration</h2>
      <p>To notify the Hub when your AQA content changes, add <code>"pingbackEndpoints"</code> to your AQA JSON-LD block:</p>
      <pre><code>{
  "@context": ["https://schema.org", "https://aqa-spec.org/ns/context.jsonld"],
  "@type": "Article",
  "specVersion": "1.2",
  "pingbackEndpoints": [
    "https://aqa-spec.org/hub/api/v1/ping"
  ],
  ...
}</code></pre>
      <p>See the <a href="https://github.com/sarsator/aqa-specification/blob/master/docs/migration-guide.md">Migration Guide</a> for full integration instructions.</p>
    </section>

  </main>

  <footer class="site-footer">
    <div class="container">
      <p>AQA Hub — Part of the <a href="https://aqa-spec.org/">AQA Specification</a> (MIT License)</p>
      <p>Created by Davy Abderrahman — <a href="https://ai-labs-solutions.fr">AI Labs Solutions</a></p>
      <p><a href="https://github.com/sarsator/aqa-specification">GitHub</a></p>
    </div>
  </footer>

  <script>
  (function() {
    fetch('/hub/api/v1/stats')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        document.getElementById('stat-updates').textContent = data.totalUpdates || 0;
        document.getElementById('stat-publishers').textContent = data.totalPublishers || 0;
        document.getElementById('stat-24h').textContent = data.last24h || 0;
        var last = data.lastUpdate;
        if (last) {
          var d = new Date(last);
          document.getElementById('stat-last').textContent = d.toLocaleDateString();
        } else {
          document.getElementById('stat-last').textContent = 'None';
        }
      })
      .catch(function() {
        document.getElementById('stat-updates').textContent = '0';
        document.getElementById('stat-publishers').textContent = '0';
        document.getElementById('stat-24h').textContent = '0';
        document.getElementById('stat-last').textContent = 'None';
      });
  })();
  </script>

</body>
</html>
