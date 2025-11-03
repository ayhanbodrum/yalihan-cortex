#!/usr/bin/env node

/**
 * Simple AI Settings Page Test (No Login Required)
 * Context7 Compliant
 * Date: 2025-10-12
 */

import puppeteer from 'puppeteer';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const SCREENSHOT_DIR = join(__dirname, '../../screenshots/ai-settings-test');
const AI_SETTINGS_URL = 'http://localhost:8000/admin/ai-settings';

// Create screenshot directory
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

console.log('🤖 AI Settings Page Simple Test');
console.log('=' .repeat(60));
console.log('');
console.log('⚠️  NOTE: Please login manually first in your browser!');
console.log('   URL: http://localhost:8000/login');
console.log('   Then run this script again.');
console.log('');
console.log('📝 Or we can take a screenshot of what we can access...');
console.log('');

async function main() {
    const browser = await puppeteer.launch({
        headless: false, // Non-headless to see what's happening
        args: ['--no-sandbox', '--disable-setuid-sandbox'],
        defaultViewport: { width: 1920, height: 1080 }
    });
    
    try {
        const page = await browser.newPage();
        
        console.log('🌐 Navigating to AI Settings...');
        await page.goto(AI_SETTINGS_URL, { 
            waitUntil: 'domcontentloaded',
            timeout: 10000 
        });
        
        await new Promise(resolve => setTimeout(resolve, 2000)); // Wait for page to settle
        
        const title = await page.title();
        console.log(`📌 Page Title: ${title}`);
        
        // Take screenshot
        const screenshotPath = join(SCREENSHOT_DIR, `ai-settings-${Date.now()}.png`);
        await page.screenshot({ 
            path: screenshotPath,
            fullPage: true 
        });
        
        console.log('');
        console.log('✅ Screenshot saved!');
        console.log(`📸 Location: ${screenshotPath}`);
        console.log('');
        
        // Check what's on the page
        const url = page.url();
        console.log(`🔗 Current URL: ${url}`);
        
        if (url.includes('/login')) {
            console.log('');
            console.log('⚠️  REDIRECTED TO LOGIN');
            console.log('');
            console.log('📝 TO TEST AI SETTINGS:');
            console.log('   1. Open browser: http://localhost:8000/login');
            console.log('   2. Login with your credentials');
            console.log('   3. Navigate to: http://localhost:8000/admin/ai-settings');
            console.log('   4. Verify you see:');
            console.log('      ✅ 5 AI Provider cards (AnythingLLM, OpenAI, Gemini, Claude, Ollama)');
            console.log('      ✅ Test buttons on each card');
            console.log('      ✅ "Tümünü Test Et" button');
            console.log('      ✅ Status badges (gray "Henüz Test Edilmedi")');
            console.log('      ✅ Configuration inputs (URL, API keys)');
            console.log('');
        } else {
            console.log('✅ Page loaded! Analyzing...');
            
            // Try to find elements
            const providers = await page.$$('[data-provider]');
            console.log(`  📦 Provider test buttons: ${providers.length}`);
            
            const cards = await page.$$('.neo-card, [class*="card"], [class*="bg-white"], [class*="rounded"]');
            console.log(`  🎴 Card elements: ${cards.length}`);
            
            const buttons = await page.$$('button');
            console.log(`  🔘 Total buttons: ${buttons.length}`);
            
            const inputs = await page.$$('input[type="text"], input[type="url"], input[type="password"]');
            console.log(`  📝 Input fields: ${inputs.length}`);
        }
        
        console.log('');
        console.log('🎯 Manual verification recommended!');
        console.log('   Open screenshot to see actual page state.');
        
        // Keep browser open for 5 seconds
        console.log('');
        console.log('⏳ Browser will close in 5 seconds...');
        await new Promise(resolve => setTimeout(resolve, 5000));
        
    } catch (error) {
        console.error('❌ ERROR:', error.message);
    } finally {
        await browser.close();
        console.log('');
        console.log('✅ Test completed!');
    }
}

main().catch(console.error);

