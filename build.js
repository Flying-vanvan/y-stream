#!/usr/bin/env node
/**
 * Build Y-stream → dist/   (EN at /, FR at /fr/)
 *  - injects data/*.json (home texts, SEO, UI strings, legal pages) into the page templates
 *  - hreflang / canonical / sitemap / _headers / 404
 *  - `node build.js --preview` also writes standalone files in apercu/ (CSS inlined, relative paths)
 * No dependency: Node ≥ 18.
 */
const fs = require('fs'), path = require('path');
const SRC = __dirname, OUT = path.join(SRC, 'dist');
const preview = process.argv.includes('--preview');
const SITE = 'https://www.y-stream.fr';

const read = p => fs.readFileSync(path.join(SRC, p), 'utf8');
const json = p => JSON.parse(read(p));
// French typography: no line break before : ; ? ! » and %, none after «
const typo = s => s.replace(/ ([:;?!»%])/g, '\u00a0$1').replace(/« /g, '«\u00a0');
const esc = s => typo(String(s ?? '')).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));
const get = (o, k) => k.split('.').reduce((a, p) => (a || {})[p], o);
const inl = p => p.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\[(.+?)\]\((.+?)\)/g, (m, a, u) => `<a href="${u}"${/^https?:/.test(u) ? ' target="_blank" rel="noopener"' : ''}>${a}</a>`);
const md = t => esc(t).split(/\n\s*\n/).map(p => { p = p.trim(); let out = ''; if (p.startsWith('## ')) { const nl = p.indexOf('\n'); out = '<h2>' + inl(nl < 0 ? p.slice(3) : p.slice(3, nl)) + '</h2>'; p = nl < 0 ? '' : p.slice(nl + 1).trim(); } if (p) out += '<p>' + inl(p).replace(/\n/g, '<br>') + '</p>'; return out; }).join('\n        ');
const ARROW = '<svg class="arrow" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
const FLOW = `<svg class="flow" viewBox="0 0 1600 900" aria-hidden="true" preserveAspectRatio="none">
      <path d="M-50 80 C 400 40, 800 140, 1650 60"/>
      <path d="M-50 220 C 500 180, 900 300, 1650 200"/>
      <path d="M-50 380 C 400 330, 1000 460, 1650 360"/>
      <path d="M-50 540 C 600 500, 900 640, 1650 520"/>
      <path d="M-50 700 C 400 650, 1100 800, 1650 680"/>
      <path d="M-50 850 C 500 810, 900 920, 1650 820"/>
    </svg>`;
const logoSvg = read('assets/logo-y-stream.svg').replace(/<\?xml[^>]*>/, '').replace('<svg ', '<svg class="logo" width="150" height="88" ');

const LANGS = {
  en: {prefix: '', alt: 'fr', ogLocale: 'en_GB'},
  fr: {prefix: '/fr', alt: 'en', ogLocale: 'fr_FR'},
};
// Language auto-detect: first visit only (no stored choice) → go to the browser's language
const redirectScript = (lang, altLang, altUrl) => `(function(){try{if(localStorage.getItem('lang'))return;var b=(navigator.language||'').slice(0,2).toLowerCase();if(b==='${altLang}'&&b!=='${lang}'){localStorage.setItem('lang','${altLang}');location.replace('${altUrl}')}}catch(e){}})();`;

function buildLang(lang) {
  const L = LANGS[lang];
  const sfx = lang === 'en' ? '' : '.' + lang;
  const home_ = json(`data/home${sfx}.json`), seo = json(`data/seo${sfx}.json`), ui = json(`data/ui${sfx}.json`);
  const homePath = L.prefix + '/', altHome = LANGS[L.alt].prefix + '/';
  const vars = {lang, altLang: L.alt, ogLocale: L.ogLocale, homeUrl: homePath};
  const apply = (html, extra) => html.replace(/\{\{([a-zA-Z0-9.]+)\}\}/g, (_, k) => {
    const root = k.split('.')[0], rest = k.slice(root.length + 1);
    const src = {seo, home: home_, ui}[root];
    if (!src && !(k in {...vars, ...extra})) return _;
    const v = src ? get(src, rest) : {...vars, ...extra}[k];
    return esc(v ?? '');
  });
  const footer = read('partials-footer.html'), cookieJs = read('partials-cookie.js');
  const finish = (html, extra) => apply(html.replace('/*FOOTER*/', footer).replace('/*COOKIE_JS*/', cookieJs).split('/*LOGO_SVG*/').join(logoSvg).split('/*FLOW*/').join(FLOW), extra);
  const phoneTel = home_.contact.phone.replace(/\(0\)/, '').replace(/[^\d]/g, '');
  const h = home_;
  const h1 = esc(h.hero.title).split('\n').map((l, i) => i ? `<span class="line2">${l}</span>` : l).join('<br>');

  let home = finish(read('index.html'), {selfPath: homePath, altUrl: altHome})
    .replace('/*LANG_REDIRECT*/', redirectScript(lang, L.alt, altHome))
    .replace('/*H1*/', h1)
    .replace('/*PROBLEM_TITLE*/', esc(h.problem.title).replace(/\n/g, '<br>'))
    .replace('/*NEXT_TITLE*/', esc(h.next.title).replace(/\n/g, '<br>'))
    .replace('/*FIGURES*/', h.figures.items.map(f => `<div class="item"><strong>${esc(f.number)}</strong><span>${esc(f.label)}</span></div>`).join('\n      '))
    .replace('/*PROBLEM_POINTS*/', h.problem.points.map(p => `<div><strong>${esc(p.number).replace(/[–-]/g, '<span class="dash">–</span>')}</strong><span>${esc(p.label)}</span></div>`).join('\n          '))
    .replace('/*IDEA_POINTS*/', h.idea.points.map(p => `<li><h4>${esc(p.title)}</h4><p>${esc(p.text)}</p></li>`).join('\n          '))
    .replace('/*STEPS*/', h.how.steps.map((s, i) => `<article class="step"><img src="/assets/img/step-${i + 1}.jpg" alt="${esc(s.title)}" width="339" height="218" loading="lazy"><div class="body"><h3>${esc(s.title)}</h3><p>${esc(s.text)}</p></div></article>`).join('\n        '))
    .replace('/*BENEFITS*/', h.benefits.items.map(b => `<article class="benefit"><strong>${esc(b.number)}</strong><h3>${esc(b.title)}</h3><p>${esc(b.text)}</p></article>`).join('\n        '))
    .replace('/*FACTS*/', h.development.facts.map(f => `<div class="fact"><strong>${esc(f.number)}</strong><span>${esc(f.label)}</span></div>`).join('\n        '))
    .replace('/*RESULTS*/', h.development.results.items.map(r => `<li>${esc(r)}</li>`).join(''))
    .replace('/*NEEDS*/', h.next.needs.map((n, i) => `<article class="need"><span class="n">${i + 1}</span><h3>${esc(n.title)}</h3><p>${esc(n.text)}</p></article>`).join('\n        '))
    .replace('/*MEMBERS*/', h.team.members.map(m => {
      const initials = m.name.split(/\s+/).map(w => w[0]).join('');
      const photo = m.photo ? `<img class="photo" src="${esc(m.photo)}" alt="${esc(m.name)}" width="600" height="600" loading="lazy">` : `<div class="photo" aria-hidden="true">${esc(initials)}</div>`;
      return `<article class="member">${photo}<div><h3>${esc(m.name)}</h3><p class="role">${esc(m.role)}</p><p>${esc(m.text)}</p></div></article>`;
    }).join('\n        '))
    .replace('/*PARTNERS*/', h.team.partners.map(p => `<a href="${esc(p.url)}" target="_blank" rel="noopener" title="${esc(p.name)}"><img src="${esc(p.logo)}" alt="${esc(p.name)}" width="562" height="241" loading="lazy"></a>`).join('\n          '))
    .replace('/*SUBJECTS*/', h.contact.subjects.map(s => `<option>${esc(s)}</option>`).join(''))
    .replace('/*ADDRESS*/', esc(h.contact.address).replace(/\n/g, '<br>\n          '))
    .split('/*PHONE_TEL*/').join(phoneTel);

  // ---- legal pages (legal / privacy / cookies)
  const legal = json(`data/legal${sfx}.json`).pages;
  const legalPages = legal.map(pg => {
    const selfPath = `${L.prefix}/${pg.slug}/`, altUrl = `${LANGS[L.alt].prefix}/${pg.slug}/`;
    let html = finish(read('page.html'), {selfPath, altUrl})
      .replace(/\{\{page\.(\w+)\}\}/g, (_, k) => esc(pg[k] ?? ''))
      .replace('/*LANG_REDIRECT*/', redirectScript(lang, L.alt, altUrl))
      .replace('/*PAGE_BODY*/', md(pg.body));
    return {slug: pg.slug, html, altUrl};
  });

  // ---- write dist
  const dir = path.join(OUT, L.prefix.replace(/^\//, ''));
  fs.mkdirSync(dir, {recursive: true});
  fs.writeFileSync(path.join(dir, 'index.html'), home);
  for (const pg of legalPages) { fs.mkdirSync(path.join(dir, pg.slug), {recursive: true}); fs.writeFileSync(path.join(dir, pg.slug, 'index.html'), pg.html); }

  // ---- preview (standalone files in apercu/)
  if (preview) {
    const css = read('assets/site.css').split('url("/assets/').join('url("../assets/');
    const names = {en: 'accueil.html', fr: 'fr-accueil.html'};
    const pfx = lang === 'en' ? '' : 'fr-', opfx = lang === 'en' ? 'fr-' : '';
    const prep = (html, altUrl, altFile) => html
      .replace('<link rel="stylesheet" href="/assets/site.css">', `<style>\n${css}\n</style>`)
      .split('"/assets/').join('"../assets/')
      .split(`href="${homePath}#`).join(`href="${names[lang]}#`)
      .split(`href="${homePath}"`).join(`href="${names[lang]}"`)
      .split(`href="${altUrl}"`).join(`href="${altFile}"`)
      .split(`href="${homePath}legal/"`).join(`href="${pfx}legal.html"`).split(`href="${homePath}privacy/"`).join(`href="${pfx}privacy.html"`).split(`href="${homePath}cookies/"`).join(`href="${pfx}cookies.html"`)
      .replace(`location.replace('${altUrl}')`, `location.replace('${altFile}')`);
    fs.mkdirSync(path.join(SRC, 'apercu'), {recursive: true});
    fs.writeFileSync(path.join(SRC, 'apercu', names[lang]), prep(home, altHome, names[L.alt]));
    for (const pg of legalPages) {
      let h2 = prep(pg.html, pg.altUrl, opfx + pg.slug + '.html');
      for (const s of ['legal', 'privacy', 'cookies']) h2 = h2.split(`href="../${s}/"`).join(`href="${pfx + s}.html"`).split(`href="/fr/${s}/"`).join(`href="fr-${s}.html"`).split(`href="/${s}/"`).join(`href="${s}.html"`);
      fs.writeFileSync(path.join(SRC, 'apercu', pfx + pg.slug + '.html'), h2);
    }
  }
}

fs.rmSync(OUT, {recursive: true, force: true});
fs.mkdirSync(OUT, {recursive: true});
buildLang('en'); buildLang('fr');

// ---- 404 page (bilingual, one file served by Netlify / Cloudflare Pages)
{
  const uiEn = json('data/ui.json'), uiFr = json('data/ui.fr.json');
  const nf = read('404.html').split('/*LOGO_SVG*/').join(logoSvg).replace(/\{\{(en|fr)\.([a-zA-Z0-9.]+)\}\}/g, (_, l, k) => esc(get(l === 'en' ? uiEn : uiFr, k) ?? ''));
  fs.writeFileSync(path.join(OUT, '404.html'), nf);
  if (preview) {
    const css = read('assets/site.css').split('url("/assets/').join('url("../assets/');
    const map = {'/#': 'accueil.html#', '/fr/#': 'fr-accueil.html#', '/fr/': 'fr-accueil.html', '/': 'accueil.html'};
    let h = nf.replace('<link rel="stylesheet" href="/assets/site.css">', `<style>\n${css}\n</style>`).split('"/assets/').join('"../assets/');
    for (const [k, v] of Object.entries(map)) h = h.split(`href="${k}`).join(`href="${v}`);
    fs.writeFileSync(path.join(SRC, 'apercu', '404.html'), h);
  }
}

// ---- static files
for (const d of ['assets', 'admin', 'data']) if (fs.existsSync(path.join(SRC, d))) fs.cpSync(path.join(SRC, d), path.join(OUT, d), {recursive: true});
fs.copyFileSync(path.join(SRC, 'robots.txt'), path.join(OUT, 'robots.txt'));

const today = new Date().toISOString().slice(0, 10);
const url = (loc, en, fr, prio) => `  <url><loc>${loc}</loc><lastmod>${today}</lastmod><changefreq>monthly</changefreq><priority>${prio}</priority>
    <xhtml:link rel="alternate" hreflang="en" href="${en}"/><xhtml:link rel="alternate" hreflang="fr" href="${fr}"/><xhtml:link rel="alternate" hreflang="x-default" href="${en}"/></url>`;
fs.writeFileSync(path.join(OUT, 'sitemap.xml'), `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
${url(SITE + '/', SITE + '/', SITE + '/fr/', '1.0')}
${url(SITE + '/fr/', SITE + '/', SITE + '/fr/', '0.9')}
${['legal', 'privacy', 'cookies'].map(s => url(SITE + '/' + s + '/', SITE + '/' + s + '/', SITE + '/fr/' + s + '/', '0.3') + '\n' + url(SITE + '/fr/' + s + '/', SITE + '/' + s + '/', SITE + '/fr/' + s + '/', '0.3')).join('\n')}
</urlset>
`);
// old Wix URLs → new site
fs.writeFileSync(path.join(OUT, '_redirects'), `/technologie  /fr/#idea  301\n/à-propos  /fr/#team  301\n/%C3%A0-propos  /fr/#team  301\n/politique-de-confidentialité  /fr/privacy/  301\n/politique-de-confidentialit%C3%A9  /fr/privacy/  301\n`);
// Security headers (Netlify / Cloudflare Pages read _headers)
fs.writeFileSync(path.join(OUT, '_headers'), `/*
  Strict-Transport-Security: max-age=63072000; includeSubDomains; preload
  X-Content-Type-Options: nosniff
  X-Frame-Options: DENY
  Referrer-Policy: strict-origin-when-cross-origin
  Permissions-Policy: camera=(), microphone=(), geolocation=()
  Content-Security-Policy: default-src 'self'; img-src 'self' data:; media-src 'self'; style-src 'self' 'unsafe-inline'; font-src 'self'; script-src 'self' 'unsafe-inline'; form-action 'self' https://formspree.io; frame-ancestors 'none'; base-uri 'self'
/admin/*
  X-Frame-Options: DENY
  X-Robots-Tag: noindex
  Content-Security-Policy: default-src 'self' https://unpkg.com https://identity.netlify.com https://api.netlify.com https://*.netlify.com https://api.github.com 'unsafe-inline' 'unsafe-eval' data: blob:; img-src * data: blob:; connect-src *
/assets/*
  Cache-Control: public, max-age=31536000, immutable
`);
console.log(`Built EN + FR → dist/${preview ? ' (+ apercu/)' : ''}`);
