import puppeteer from 'puppeteer';

const loginCandidates = [
  '/admin/login',
  '/login'
];

const baseUrl = process.env.BASE_URL || 'http://127.0.0.1:8000';
const email = process.env.ADMIN_EMAIL || '';
const password = process.env.ADMIN_PASSWORD || '';
const screenshotPath = process.env.SMOKE_OUT || '/tmp/ilan-create-auth.png';

function full(u) { return u.startsWith('http') ? u : (baseUrl + u); }

async function run() {
  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 900 });

  let loginUrl = null;
  for (const candidate of loginCandidates) {
    try {
      const r = await page.goto(full(candidate), { waitUntil: 'networkidle2', timeout: 20000 });
      const st = r.status();
      if (st >= 200 && st < 400) { loginUrl = full(candidate); break; }
    } catch (_) {}
  }

  if (!loginUrl) {
    await browser.close();
    console.log(JSON.stringify({ success: false, error: 'login_url_not_found' }));
    return;
  }

  if (!email || !password) {
    await page.screenshot({ path: screenshotPath });
    await browser.close();
    console.log(JSON.stringify({ success: false, requiresCredentials: true, loginUrl, out: screenshotPath }));
    return;
  }

  try {
    const emailSel = 'input[type="email"], input[name="email"]';
    const passSel = 'input[type="password"], input[name="password"]';
    await page.waitForSelector(emailSel, { timeout: 15000 });
    await page.waitForSelector(passSel, { timeout: 15000 });
    await page.type(emailSel, email, { delay: 10 });
    await page.type(passSel, password, { delay: 10 });
    const submitBtnSel = 'button[type="submit"], input[type="submit"]';
    const hasBtn = await page.$(submitBtnSel);
    if (hasBtn) { await Promise.all([page.click(submitBtnSel), page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 30000 })]); }
  } catch (e) {
    await page.screenshot({ path: screenshotPath });
    await browser.close();
    console.log(JSON.stringify({ success: false, error: 'login_failed', detail: String(e), out: screenshotPath }));
    return;
  }

  try {
    const createUrl = full('/admin/ilanlar/create');
    const r = await page.goto(createUrl, { waitUntil: 'networkidle2', timeout: 30000 });
    const st = r.status();
    let found = false;
    try { await page.waitForSelector('#ilan-create-form', { timeout: 15000 }); found = true; } catch (_) {}
    await page.screenshot({ path: screenshotPath });
    await browser.close();
    console.log(JSON.stringify({ success: (st >= 200 && st < 400) && found, url: createUrl, status: st, formFound: found, out: screenshotPath }));
  } catch (e) {
    await page.screenshot({ path: screenshotPath });
    await browser.close();
    console.log(JSON.stringify({ success: false, error: 'create_page_failed', detail: String(e), out: screenshotPath }));
  }
}

run();