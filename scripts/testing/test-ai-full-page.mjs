#!/usr/bin/env node

/**
 * Full AI Settings Page Test - Scroll Check
 * Context7 Compliant
 * Date: 2025-10-12
 */

import puppeteer from "puppeteer";
import { fileURLToPath } from "url";
import { dirname, join } from "path";
import fs from "fs";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const SCREENSHOT_DIR = join(__dirname, "../../screenshots/ai-settings-test");
const AI_SETTINGS_URL = "http://localhost:8000/admin/ai-settings";

// Create screenshot directory
if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

console.log("🔍 AI Settings Full Page Test");
console.log("=".repeat(60));
console.log("");

async function main() {
    const browser = await puppeteer.launch({
        headless: false, // Non-headless to see what's happening
        args: ["--no-sandbox", "--disable-setuid-sandbox"],
        defaultViewport: { width: 1920, height: 1080 },
    });

    try {
        const page = await browser.newPage();

        console.log("🌐 Navigating to AI Settings...");
        await page.goto(AI_SETTINGS_URL, {
            waitUntil: "domcontentloaded",
            timeout: 15000,
        });

        await new Promise((resolve) => setTimeout(resolve, 3000));

        const title = await page.title();
        console.log(`📌 Page Title: ${title}`);
        console.log(`🔗 Current URL: ${page.url()}`);

        // Check if we're on login page
        if (page.url().includes("/login")) {
            console.log("");
            console.log("❌ REDIRECTED TO LOGIN PAGE");
            console.log("");
            console.log("📝 MANUAL LOGIN REQUIRED:");
            console.log("   1. Please login manually in this browser window");
            console.log(
                "   2. Then navigate to: http://localhost:8000/admin/ai-settings"
            );
            console.log("   3. Press Enter when ready...");
            console.log("");

            // Wait for user input
            process.stdin.setRawMode(true);
            process.stdin.resume();
            process.stdin.on("data", async () => {
                process.stdin.setRawMode(false);
                process.stdin.pause();

                console.log("✅ Continuing test...");
                await performFullTest(page);
            });

            // Keep browser open
            await new Promise((resolve) => setTimeout(resolve, 300000)); // 5 minutes
        } else {
            await performFullTest(page);
        }
    } catch (error) {
        console.error("❌ ERROR:", error.message);
    } finally {
        await browser.close();
        console.log("");
        console.log("✅ Test completed!");
    }
}

async function performFullTest(page) {
    console.log("");
    console.log("🔍 PERFORMING FULL PAGE TEST...");
    console.log("-".repeat(60));

    // Get page height
    const pageHeight = await page.evaluate(() => document.body.scrollHeight);
    console.log(`📏 Page Height: ${pageHeight}px`);

    // Take full page screenshot
    const fullScreenshot = join(SCREENSHOT_DIR, `full-page-${Date.now()}.png`);
    await page.screenshot({
        path: fullScreenshot,
        fullPage: true,
    });
    console.log(`📸 Full page screenshot: ${fullScreenshot}`);

    // Check for all expected providers
    console.log("");
    console.log("🔍 CHECKING ALL PROVIDERS...");

    const expectedProviders = [
        "anythingllm",
        "openai",
        "gemini",
        "claude",
        "ollama",
    ];

    const foundProviders = [];
    const missingProviders = [];

    for (const provider of expectedProviders) {
        const selector = `[data-provider="${provider}"]`;
        const element = await page.$(selector);

        if (element) {
            foundProviders.push(provider);
            console.log(`  ✅ ${provider.toUpperCase()} found`);

            // Get provider card info
            const cardText = await page.$eval(
                element.closest('[class*="card"], .neo-card'),
                (el) => el.textContent?.substring(0, 100) + "..." || "No text"
            );
            console.log(`     Card preview: ${cardText}`);
        } else {
            missingProviders.push(provider);
            console.log(`  ❌ ${provider.toUpperCase()} NOT found`);
        }
    }

    // Check for test buttons
    console.log("");
    console.log("🧪 CHECKING TEST BUTTONS...");

    const testButtons = await page.$$(".btn-test, [data-provider]");
    console.log(`  📦 Found ${testButtons.length} test buttons`);

    const testAllButton = await page.$("#testAllProviders");
    if (testAllButton) {
        const text = await page.$eval(
            testAllButton,
            (el) => el.textContent?.trim() || ""
        );
        console.log(`  ✅ Test All Button: "${text}"`);
    } else {
        console.log(`  ❌ Test All Button NOT found`);
    }

    // Check for status badges
    console.log("");
    console.log("🏷️  CHECKING STATUS BADGES...");

    const statusBadges = await page.$$('[id*="status-badge"]');
    console.log(`  📦 Found ${statusBadges.length} status badges`);

    for (let i = 0; i < statusBadges.length; i++) {
        const badge = statusBadges[i];
        const id = await page.$eval(badge, (el) => el.id || "no-id");
        const text = await page.$eval(
            badge,
            (el) => el.textContent?.trim() || ""
        );
        console.log(`  ${i + 1}. ${id}: "${text}"`);
    }

    // Check for configuration inputs
    console.log("");
    console.log("📝 CHECKING CONFIGURATION INPUTS...");

    const inputs = await page.$$(
        'input[type="text"], input[type="url"], input[type="password"], select'
    );
    console.log(`  📦 Found ${inputs.length} input fields`);

    for (let i = 0; i < Math.min(inputs.length, 10); i++) {
        const input = inputs[i];
        const name = await page.$eval(
            input,
            (el) => el.name || el.id || "no-name"
        );
        const type = await page.$eval(
            input,
            (el) => el.type || el.tagName.toLowerCase()
        );
        const value = await page.$eval(input, (el) => el.value || "empty");
        console.log(
            `  ${i + 1}. ${name} (${type}): "${value.substring(0, 30)}${
                value.length > 30 ? "..." : ""
            }"`
        );
    }

    // Scroll to bottom to check for more content
    console.log("");
    console.log("📜 SCROLLING TO CHECK FULL CONTENT...");

    await page.evaluate(() => {
        window.scrollTo(0, document.body.scrollHeight);
    });

    await new Promise((resolve) => setTimeout(resolve, 2000));

    // Take screenshot of bottom
    const bottomScreenshot = join(
        SCREENSHOT_DIR,
        `bottom-content-${Date.now()}.png`
    );
    await page.screenshot({
        path: bottomScreenshot,
        fullPage: true,
    });
    console.log(`📸 Bottom screenshot: ${bottomScreenshot}`);

    // Check what's at the bottom
    const bottomContent = await page.evaluate(() => {
        const rect = document.body.getBoundingClientRect();
        const elements = document.elementsFromPoint(
            window.innerWidth / 2,
            window.innerHeight - 100
        );
        return elements.map((el) => ({
            tag: el.tagName,
            class: el.className,
            text: el.textContent?.substring(0, 50) || "",
            id: el.id || "",
        }));
    });

    console.log("📋 Bottom content preview:");
    bottomContent.slice(0, 5).forEach((el, i) => {
        console.log(`  ${i + 1}. ${el.tag} (${el.class}): "${el.text}"`);
    });

    // Final report
    console.log("");
    console.log("=".repeat(60));
    console.log("📊 FINAL REPORT");
    console.log("=".repeat(60));
    console.log("");

    console.log(`📏 Page Height: ${pageHeight}px`);
    console.log(`✅ Providers Found: ${foundProviders.length}/5`);
    console.log(`❌ Providers Missing: ${missingProviders.length}`);
    console.log(`🧪 Test Buttons: ${testButtons.length}`);
    console.log(`🏷️  Status Badges: ${statusBadges.length}`);
    console.log(`📝 Input Fields: ${inputs.length}`);
    console.log("");

    if (foundProviders.length > 0) {
        console.log("✅ FOUND PROVIDERS:");
        foundProviders.forEach((p) => console.log(`  - ${p.toUpperCase()}`));
        console.log("");
    }

    if (missingProviders.length > 0) {
        console.log("❌ MISSING PROVIDERS:");
        missingProviders.forEach((p) => console.log(`  - ${p.toUpperCase()}`));
        console.log("");
    }

    // Check if page is complete
    const isComplete =
        foundProviders.length >= 4 &&
        testButtons.length >= 4 &&
        statusBadges.length >= 4;

    console.log("=".repeat(60));
    console.log(
        `🎯 PAGE STATUS: ${isComplete ? "✅ COMPLETE" : "⚠️  INCOMPLETE"}`
    );
    console.log(
        `📊 SUCCESS RATE: ${Math.round((foundProviders.length / 5) * 100)}%`
    );
    console.log("=".repeat(60));

    if (!isComplete) {
        console.log("");
        console.log("🔧 TROUBLESHOOTING:");
        console.log("   1. Check if all provider cards are rendered");
        console.log("   2. Verify JavaScript is loading correctly");
        console.log("   3. Check browser console for errors");
        console.log("   4. Ensure view file is complete");
        console.log("");
    }
}

main().catch(console.error);
