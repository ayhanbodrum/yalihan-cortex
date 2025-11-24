import puppeteer from 'puppeteer';

const url = process.env.SMOKE_URL || 'http://127.0.0.1:8000/admin';
const out = process.env.SMOKE_OUT || '/tmp/puppeteer-smoke.png';

try {
  const browser = await puppeteer.launch({ headless: true });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });
  await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
  await page.screenshot({ path: out });
  await browser.close();
  console.log(JSON.stringify({ success: true, url, out }));
  process.exit(0);
} catch (e) {
  console.log(JSON.stringify({ success: false, url, error: String(e) }));
  process.exit(0);
}