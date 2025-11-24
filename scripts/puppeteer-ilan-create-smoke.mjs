import puppeteer from 'puppeteer';

const url = process.env.SMOKE_URL || 'http://127.0.0.1:8000/admin/ilanlar/create';
const out = process.env.SMOKE_OUT || '/tmp/ilan-create-smoke.png';

try {
  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 900 });
  const resp = await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
  const status = resp.status();
  const ok = status >= 200 && status < 400;
  let found = false;
  let requiresAuth = false;
  try {
    await page.waitForSelector('#ilan-create-form', { timeout: 12000 });
    found = true;
  } catch (_) {
    try {
      await page.waitForSelector('input[type="password"]', { timeout: 5000 });
      requiresAuth = true;
    } catch (_) {}
  }
  await page.screenshot({ path: out });
  await browser.close();
  console.log(JSON.stringify({ success: ok && found, url, status, formFound: found, requiresAuth, out }));
  process.exit(0);
} catch (e) {
  console.log(JSON.stringify({ success: false, url, error: String(e) }));
  process.exit(0);
}