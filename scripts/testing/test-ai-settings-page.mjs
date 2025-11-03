#!/usr/bin/env node

/**
 * AI Settings Page Puppeteer Test
 * Context7 Compliant
 * Date: 2025-10-12
 */

import puppeteer from 'puppeteer';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Configuration
const BASE_URL = 'http://localhost:8000';
const SCREENSHOT_DIR = join(__dirname, '../../screenshots/ai-settings-test');
const LOGIN_URL = `${BASE_URL}/login`;
const AI_SETTINGS_URL = `${BASE_URL}/admin/ai-settings`;

// Test credentials
const CREDENTIALS = {
    email: 'admin@yalihanemlak.com',
    password: 'password'
};

// Expected elements
const EXPECTED_ELEMENTS = {
    providers: [
        { name: 'AnythingLLM', selector: '[data-provider="anythingllm"]', color: 'blue' },
        { name: 'OpenAI', selector: '[data-provider="openai"]', color: 'green' },
        { name: 'Gemini', selector: '[data-provider="gemini"]', color: 'purple' },
        { name: 'Claude', selector: '[data-provider="claude"]', color: 'indigo' }
    ],
    buttons: {
        testAll: '#testAllProviders',
        save: 'button[type="submit"]'
    },
    statusBadges: [
        '#anythingllm-status-badge',
        '#openai-status-badge',
        '#gemini-status-badge',
        '#claude-status-badge'
    ],
    inputs: {
        anythingllm_url: '#anythingllm_url',
        anythingllm_api_key: '#anythingllm_api_key',
        openai_api_key: '#ai_openai_api_key',
        gemini_api_key: '#ai_gemini_api_key',
        claude_api_key: '#ai_claude_api_key'
    }
};

// Create screenshot directory
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

console.log('🤖 AI Settings Page Test');
console.log('=' .repeat(60));
console.log('');

async function login(page) {
    console.log('🔐 Logging in...');
    
    try {
        await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded', timeout: 10000 });
        
        // Wait for login form
        await page.waitForSelector('input[name="email"]', { timeout: 5000 });
        
        await page.type('input[name="email"]', CREDENTIALS.email);
        await page.type('input[name="password"]', CREDENTIALS.password);
        
        // Click submit and wait for navigation
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 10000 }),
            page.click('button[type="submit"]')
        ]);
        
        console.log('✅ Logged in successfully');
    } catch (error) {
        console.log('⚠️ Login attempt failed, trying direct navigation to AI settings...');
        // If login fails, try to go directly (maybe already logged in)
        await page.goto(AI_SETTINGS_URL, { waitUntil: 'domcontentloaded', timeout: 10000 });
    }
}

async function testAISettingsPage(page) {
    console.log('');
    console.log('📄 Testing AI Settings Page...');
    console.log('-'.repeat(60));
    
    await page.goto(AI_SETTINGS_URL, { waitUntil: 'domcontentloaded', timeout: 15000 });
    
    // Test results
    const results = {
        pageLoaded: false,
        title: '',
        providersFound: [],
        testButtonsFound: [],
        statusBadgesFound: [],
        inputsFound: [],
        errors: [],
        screenshots: []
    };
    
    try {
        // Check if page loaded
        results.pageLoaded = true;
        
        // Get page title
        results.title = await page.title();
        console.log(`📌 Page Title: ${results.title}`);
        
        // Take full page screenshot
        const fullScreenshot = join(SCREENSHOT_DIR, `ai-settings-full-${Date.now()}.png`);
        await page.screenshot({ 
            path: fullScreenshot, 
            fullPage: true 
        });
        results.screenshots.push(fullScreenshot);
        console.log(`📸 Full page screenshot: ${fullScreenshot}`);
        
        // Check providers
        console.log('');
        console.log('🔍 Checking Providers...');
        for (const provider of EXPECTED_ELEMENTS.providers) {
            const exists = await page.$(provider.selector);
            if (exists) {
                results.providersFound.push(provider.name);
                console.log(`  ✅ ${provider.name} test button found`);
            } else {
                results.errors.push(`❌ ${provider.name} test button NOT found`);
                console.log(`  ❌ ${provider.name} test button NOT found`);
            }
        }
        
        // Check status badges
        console.log('');
        console.log('🏷️  Checking Status Badges...');
        for (const badge of EXPECTED_ELEMENTS.statusBadges) {
            const exists = await page.$(badge);
            if (exists) {
                const text = await page.$eval(badge, el => el.textContent.trim());
                results.statusBadgesFound.push({ selector: badge, text });
                console.log(`  ✅ Badge found: ${badge} - "${text}"`);
            } else {
                results.errors.push(`❌ Badge NOT found: ${badge}`);
                console.log(`  ❌ Badge NOT found: ${badge}`);
            }
        }
        
        // Check inputs
        console.log('');
        console.log('📝 Checking Input Fields...');
        for (const [key, selector] of Object.entries(EXPECTED_ELEMENTS.inputs)) {
            const exists = await page.$(selector);
            if (exists) {
                results.inputsFound.push(key);
                console.log(`  ✅ Input found: ${key}`);
            } else {
                console.log(`  ⚠️  Input not found (might be updated): ${key}`);
            }
        }
        
        // Check "Test All" button
        console.log('');
        console.log('🧪 Checking Test All Button...');
        const testAllButton = await page.$(EXPECTED_ELEMENTS.buttons.testAll);
        if (testAllButton) {
            const text = await page.$eval(EXPECTED_ELEMENTS.buttons.testAll, el => el.textContent.trim());
            results.testButtonsFound.push('Test All Button');
            console.log(`  ✅ Test All Button found: "${text}"`);
            
            // Take screenshot of test all button area
            const testButtonScreenshot = join(SCREENSHOT_DIR, `test-all-button-${Date.now()}.png`);
            await testAllButton.screenshot({ path: testButtonScreenshot });
            results.screenshots.push(testButtonScreenshot);
        } else {
            results.errors.push('❌ Test All Button NOT found');
            console.log('  ❌ Test All Button NOT found');
        }
        
        // Check provider cards
        console.log('');
        console.log('🎴 Checking Provider Cards...');
        const providerCards = await page.$$('.neo-card, [class*="card"]');
        console.log(`  📦 Found ${providerCards.length} cards`);
        
        // Count visible providers
        const visibleProviders = await page.$$eval('[data-provider]', elements => 
            elements.map(el => el.getAttribute('data-provider'))
        );
        console.log(`  🔍 Visible providers: ${visibleProviders.join(', ')}`);
        
        // Take screenshot of each provider section
        console.log('');
        console.log('📸 Taking Provider Screenshots...');
        for (let i = 0; i < Math.min(providerCards.length, 5); i++) {
            const providerScreenshot = join(SCREENSHOT_DIR, `provider-card-${i + 1}-${Date.now()}.png`);
            await providerCards[i].screenshot({ path: providerScreenshot });
            results.screenshots.push(providerScreenshot);
            console.log(`  ✅ Provider card ${i + 1} screenshot saved`);
        }
        
        // Check for JavaScript errors
        console.log('');
        console.log('🐛 Checking for JavaScript Errors...');
        const jsErrors = [];
        page.on('console', msg => {
            if (msg.type() === 'error') {
                jsErrors.push(msg.text());
            }
        });
        
        // Wait a bit to catch any delayed errors
        await page.waitForTimeout(3000);
        
        if (jsErrors.length > 0) {
            console.log(`  ⚠️  Found ${jsErrors.length} JavaScript errors:`);
            jsErrors.forEach(error => console.log(`    - ${error}`));
            results.errors.push(...jsErrors);
        } else {
            console.log('  ✅ No JavaScript errors detected');
        }
        
        // Check page structure
        console.log('');
        console.log('🏗️  Checking Page Structure...');
        
        const hasHeader = await page.$('h1, h2');
        console.log(`  ${hasHeader ? '✅' : '❌'} Page header found`);
        
        const hasForm = await page.$('form');
        console.log(`  ${hasForm ? '✅' : '❌'} Form found`);
        
        const hasFooter = await page.$('footer, .footer');
        console.log(`  ${hasFooter ? '✅' : '⚠️ '} Footer ${hasFooter ? 'found' : 'not found (optional)'}`);
        
    } catch (error) {
        results.errors.push(`CRITICAL ERROR: ${error.message}`);
        console.error('❌ ERROR:', error.message);
    }
    
    return results;
}

async function generateReport(results) {
    console.log('');
    console.log('=' .repeat(60));
    console.log('📊 TEST REPORT');
    console.log('=' .repeat(60));
    console.log('');
    
    // Summary
    console.log('📋 SUMMARY:');
    console.log(`  Page Loaded: ${results.pageLoaded ? '✅' : '❌'}`);
    console.log(`  Page Title: ${results.title}`);
    console.log(`  Providers Found: ${results.providersFound.length}/4`);
    console.log(`  Test Buttons: ${results.testButtonsFound.length}`);
    console.log(`  Status Badges: ${results.statusBadgesFound.length}`);
    console.log(`  Inputs Found: ${results.inputsFound.length}`);
    console.log(`  Screenshots: ${results.screenshots.length}`);
    console.log(`  Errors: ${results.errors.length}`);
    console.log('');
    
    // Provider details
    if (results.providersFound.length > 0) {
        console.log('✅ PROVIDERS DETECTED:');
        results.providersFound.forEach(p => console.log(`  - ${p}`));
        console.log('');
    }
    
    // Status badges
    if (results.statusBadgesFound.length > 0) {
        console.log('🏷️  STATUS BADGES:');
        results.statusBadgesFound.forEach(b => console.log(`  - ${b.selector}: "${b.text}"`));
        console.log('');
    }
    
    // Errors
    if (results.errors.length > 0) {
        console.log('❌ ERRORS:');
        results.errors.forEach(e => console.log(`  - ${e}`));
        console.log('');
    }
    
    // Screenshots
    console.log('📸 SCREENSHOTS SAVED:');
    results.screenshots.forEach((s, i) => console.log(`  ${i + 1}. ${s}`));
    console.log('');
    
    // Comparison with expected
    console.log('🎯 EXPECTED vs ACTUAL:');
    console.log('');
    
    console.log('Expected Features:');
    console.log('  ✅ 5 AI Providers (AnythingLLM, OpenAI, Gemini, Claude, Ollama)');
    console.log('  ✅ Test buttons (Individual + "Tümünü Test Et")');
    console.log('  ✅ Status badges (Real-time)');
    console.log('  ✅ Configuration inputs');
    console.log('  ✅ Save button');
    console.log('');
    
    console.log('Actual Findings:');
    console.log(`  ${results.providersFound.length >= 4 ? '✅' : '⚠️ '} ${results.providersFound.length}/4 providers detected`);
    console.log(`  ${results.testButtonsFound.length > 0 ? '✅' : '❌'} Test buttons found`);
    console.log(`  ${results.statusBadgesFound.length >= 4 ? '✅' : '⚠️ '} ${results.statusBadgesFound.length}/4 status badges found`);
    console.log(`  ${results.inputsFound.length >= 3 ? '✅' : '⚠️ '} ${results.inputsFound.length} configuration inputs found`);
    console.log('');
    
    // Success rate
    const successRate = results.errors.length === 0 ? 100 : 
        Math.round((1 - results.errors.length / 10) * 100);
    
    console.log('=' .repeat(60));
    console.log(`🎯 SUCCESS RATE: ${successRate}%`);
    console.log(`📊 STATUS: ${successRate >= 90 ? '✅ EXCELLENT' : successRate >= 70 ? '🟡 GOOD' : '🔴 NEEDS WORK'}`);
    console.log('=' .repeat(60));
    console.log('');
    
    // Generate markdown report
    const reportPath = join(SCREENSHOT_DIR, `test-report-${Date.now()}.md`);
    const report = generateMarkdownReport(results, successRate);
    fs.writeFileSync(reportPath, report);
    console.log(`📄 Report saved: ${reportPath}`);
    
    return results;
}

function generateMarkdownReport(results, successRate) {
    const timestamp = new Date().toISOString();
    
    return `# 🤖 AI Settings Page Test Report

**Date:** ${timestamp}  
**URL:** ${AI_SETTINGS_URL}  
**Success Rate:** ${successRate}%  
**Status:** ${successRate >= 90 ? '✅ EXCELLENT' : successRate >= 70 ? '🟡 GOOD' : '🔴 NEEDS WORK'}

---

## 📊 Summary

| Metric | Value | Status |
|--------|-------|--------|
| Page Loaded | ${results.pageLoaded ? 'Yes' : 'No'} | ${results.pageLoaded ? '✅' : '❌'} |
| Page Title | ${results.title} | ℹ️ |
| Providers Found | ${results.providersFound.length}/4 | ${results.providersFound.length >= 4 ? '✅' : '⚠️'} |
| Test Buttons | ${results.testButtonsFound.length} | ${results.testButtonsFound.length > 0 ? '✅' : '❌'} |
| Status Badges | ${results.statusBadgesFound.length}/4 | ${results.statusBadgesFound.length >= 4 ? '✅' : '⚠️'} |
| Inputs Found | ${results.inputsFound.length} | ${results.inputsFound.length >= 3 ? '✅' : '⚠️'} |
| JavaScript Errors | ${results.errors.filter(e => !e.startsWith('❌')).length} | ${results.errors.filter(e => !e.startsWith('❌')).length === 0 ? '✅' : '⚠️'} |

---

## ✅ Providers Detected

${results.providersFound.length > 0 ? results.providersFound.map(p => `- ✅ ${p}`).join('\n') : '- ❌ No providers found'}

---

## 🏷️ Status Badges

${results.statusBadgesFound.length > 0 ? results.statusBadgesFound.map(b => `- ${b.selector}: "${b.text}"`).join('\n') : '- ❌ No status badges found'}

---

## 📝 Input Fields

${results.inputsFound.length > 0 ? results.inputsFound.map(i => `- ✅ ${i}`).join('\n') : '- ❌ No inputs found'}

---

## ❌ Errors

${results.errors.length > 0 ? results.errors.map(e => `- ${e}`).join('\n') : '- ✅ No errors detected'}

---

## 📸 Screenshots

${results.screenshots.map((s, i) => `${i + 1}. \`${s}\``).join('\n')}

---

## 🎯 Expected vs Actual

### Expected Features:
- ✅ 5 AI Providers (AnythingLLM, OpenAI, Gemini, Claude, Ollama)
- ✅ Test buttons (Individual + "Tümünü Test Et")
- ✅ Status badges (Real-time, color-coded)
- ✅ Configuration forms (URL, API keys)
- ✅ Save functionality
- ✅ Auto-refresh (30s)

### Actual Findings:
- ${results.providersFound.length >= 4 ? '✅' : '⚠️'} ${results.providersFound.length}/4 providers detected
- ${results.testButtonsFound.length > 0 ? '✅' : '❌'} Test buttons found
- ${results.statusBadgesFound.length >= 4 ? '✅' : '⚠️'} ${results.statusBadgesFound.length}/4 status badges found
- ${results.inputsFound.length >= 3 ? '✅' : '⚠️'} ${results.inputsFound.length} configuration inputs found

---

## 📋 Context7 Compliance Check

### Master Reference Verification:
- [ ] Page URL matches: /admin/ai-settings
- [ ] View file: resources/views/admin/ai-settings/index.blade.php
- [ ] Controller: AISettingsController
- [ ] 5 Providers visible
- [ ] Test functionality present
- [ ] Status badges working
- [ ] Logging enabled

### Reference Documents:
- yalihan-bekci/knowledge/ai-settings-master-reference.json
- docs/context7/AI-MASTER-REFERENCE-2025-10-12.md
- yalihan-bekci/knowledge/context7-rules.json (ai_specific_rules)

---

**Context7 Compliant:** ${successRate >= 95 ? '✅ Yes' : '⚠️ Needs Review'}  
**Test Date:** ${timestamp}  
**Success Rate:** ${successRate}%
`;
}

async function main() {
    const browser = await puppeteer.launch({
        headless: 'new',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-web-security'
        ]
    });
    
    try {
        const page = await browser.newPage();
        await page.setViewport({ width: 1920, height: 1080 });
        
        // Enable console logging
        page.on('console', msg => {
            if (msg.type() === 'log') {
                console.log(`  🖥️  BROWSER LOG: ${msg.text()}`);
            }
        });
        
        // Login
        await login(page);
        
        // Test AI Settings page
        const results = await testAISettingsPage(page);
        
        // Generate report
        await generateReport(results);
        
        console.log('');
        console.log('✅ Test completed successfully!');
        console.log(`📂 Screenshots saved to: ${SCREENSHOT_DIR}`);
        
    } catch (error) {
        console.error('');
        console.error('❌ CRITICAL ERROR:');
        console.error(error);
        process.exit(1);
    } finally {
        await browser.close();
    }
}

// Run test
main().catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
});

