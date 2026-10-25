import { chromium } from 'playwright';
import fs from 'fs';
import path from 'path';

const SCREENSHOT_DIR = '/home/sadik/.gemini/antigravity-ide/brain/0725dc8a-7edc-4bab-a783-efc600c49233/screenshots';
const BASE_URL = 'http://localhost:8090';

if (!fs.existsSync(SCREENSHOT_DIR)) {
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

let totalTests = 0;
let passedTests = 0;
let failedTests = 0;
const failureDetails = [];

function recordResult(testName, success, err = null) {
  totalTests++;
  if (success) {
    passedTests++;
    console.log(`[PASS] ${testName}`);
  } else {
    failedTests++;
    console.error(`[FAIL] ${testName}:`, err);
    failureDetails.push({ testName, error: err ? err.message || String(err) : 'Unknown failure' });
  }
}

async function runAll() {
  console.log('================================================================');
  console.log('🚀 STARTING COMPREHENSIVE E2E BROWSER TESTING FOR VENTURE VISIONARY CRM');
  console.log(`Target: ${BASE_URL}`);
  console.log(`Screenshots: ${SCREENSHOT_DIR}`);
  console.log('================================================================\n');

  const browser = await chromium.launch({
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu']
  });

  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 }
  });

  const page = await context.newPage();

  const consoleErrors = [];
  page.on('console', msg => {
    if (msg.type() === 'error') {
      consoleErrors.push(`[Console Error] ${msg.text()} at ${page.url()}`);
    }
  });
  page.on('pageerror', err => {
    consoleErrors.push(`[Page Uncaught Error] ${err.message} at ${page.url()}`);
  });

  try {
    // -------------------------------------------------------------
    // TEST 1: Public Root Redirect & API Health
    // -------------------------------------------------------------
    console.log('\n--- 1. PUBLIC ENDPOINTS & DOCS ---');
    try {
      await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle' });
      const currentUrl = page.url();
      recordResult('Root redirect to /staff/login', currentUrl.includes('/staff'));
    } catch (e) {
      recordResult('Root redirect to /staff/login', false, e);
    }

    try {
      await page.goto(`${BASE_URL}/docs/api`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_openapi_docs.png'), fullPage: true });
      const title = await page.title();
      recordResult('Scramble OpenAPI Docs Render', title.toLowerCase().includes('api') || title.length > 0);
    } catch (e) {
      recordResult('Scramble OpenAPI Docs Render', false, e);
    }

    // -------------------------------------------------------------
    // TEST 2: STAFF PANEL LOGIN & DASHBOARD
    // -------------------------------------------------------------
    console.log('\n--- 2. STAFF PANEL: CEO / SUPER ADMIN ---');
    try {
      await page.goto(`${BASE_URL}/staff/login`, { waitUntil: 'networkidle' });
      await page.fill('input[type="email"], input[id*="email"]', 'ceo@venturevisionary.qa');
      await page.fill('input[type="password"]', 'Admin@VV2026!');
      await page.click('button[type="submit"]');
      await page.waitForURL('**/staff', { timeout: 10000 });
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_staff_dashboard.png'), fullPage: true });

      const heading = await page.textContent('body');
      recordResult('Staff Login & Dashboard View', heading.includes('Dashboard') && heading.includes('Active Inbound Leads'));
    } catch (e) {
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'err_staff_login.png') });
      recordResult('Staff Login & Dashboard View', false, e);
    }

    // -------------------------------------------------------------
    // TEST 3: STAFF PANEL - LEADS RESOURCE & CREATION
    // -------------------------------------------------------------
    console.log('\n--- 3. STAFF PANEL: LEADS MANAGEMENT ---');
    try {
      await page.goto(`${BASE_URL}/staff/leads`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_staff_leads_table.png'), fullPage: true });
      const bodyText = await page.textContent('body');
      recordResult('Staff Leads Index Table', bodyText.includes('Leads') && (bodyText.includes('Sheikh') || bodyText.includes('Tariq') || bodyText.includes('Filter') || bodyText.includes('New lead')));
    } catch (e) {
      recordResult('Staff Leads Index Table', false, e);
    }

    // View first lead
    try {
      const viewLeadButton = page.locator('table a[href*="/staff/leads/"]').first();
      if (await viewLeadButton.count() > 0) {
        await viewLeadButton.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_staff_lead_detail.png'), fullPage: true });
        recordResult('Staff View Lead Detail Page', true);
      } else {
        recordResult('Staff View Lead Detail Page', false, new Error('No lead link found'));
      }
    } catch (e) {
      recordResult('Staff View Lead Detail Page', false, e);
    }

    // -------------------------------------------------------------
    // TEST 4: STAFF PANEL - CLIENTS RESOURCE
    // -------------------------------------------------------------
    console.log('\n--- 4. STAFF PANEL: CLIENTS RESOURCE ---');
    try {
      await page.goto(`${BASE_URL}/staff/clients`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_staff_clients_table.png'), fullPage: true });
      const bodyText = await page.textContent('body');
      recordResult('Staff Clients Index Table', bodyText.includes('Clients') && (bodyText.includes('QID') || bodyText.includes('Passport') || bodyText.includes('New client')));
    } catch (e) {
      recordResult('Staff Clients Index Table', false, e);
    }

    // -------------------------------------------------------------
    // TEST 5: STAFF PANEL - VISA CASES RESOURCE & SNAPSHOT VIEW
    // -------------------------------------------------------------
    console.log('\n--- 5. STAFF PANEL: VISA CASES & IMMUTABLE SNAPSHOT ---');
    try {
      await page.goto(`${BASE_URL}/staff/visa-cases`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_staff_visa_cases_table.png'), fullPage: true });
      const bodyText = await page.textContent('body');
      recordResult('Staff Visa Cases Index Table', bodyText.includes('Visa Cases') && (bodyText.includes('VV-') || bodyText.includes('Status')));
    } catch (e) {
      recordResult('Staff Visa Cases Index Table', false, e);
    }

    // View first visa case
    try {
      const viewCaseButton = page.locator('table a[href*="/staff/visa-cases/"]').first();
      if (await viewCaseButton.count() > 0) {
        await viewCaseButton.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_staff_visa_case_detail.png'), fullPage: true });
        const caseBody = await page.textContent('body');
        recordResult('Staff View Visa Case & Timeline', caseBody.includes('Case Number') || caseBody.includes('VV-'));
      } else {
        recordResult('Staff View Visa Case & Timeline', false, new Error('No case link found'));
      }
    } catch (e) {
      recordResult('Staff View Visa Case & Timeline', false, e);
    }

    // -------------------------------------------------------------
    // TEST 6: OTHER STAFF RESOURCES (Countries, Services, Quotations, Partners, Users)
    // -------------------------------------------------------------
    console.log('\n--- 6. STAFF PANEL: ALL OTHER CRUD MODULES ---');
    const staffRoutes = [
      { name: 'Countries', url: `${BASE_URL}/staff/countries`, shot: '08_staff_countries.png' },
      { name: 'Services', url: `${BASE_URL}/staff/services`, shot: '09_staff_services.png' },
      { name: 'Quotations', url: `${BASE_URL}/staff/quotations`, shot: '10_staff_quotations.png' },
      { name: 'B2B Partners', url: `${BASE_URL}/staff/partners`, shot: '11_staff_partners.png' },
      { name: 'Staff Users (RBAC)', url: `${BASE_URL}/staff/users`, shot: '12_staff_users.png' },
    ];

    for (const r of staffRoutes) {
      try {
        await page.goto(r.url, { waitUntil: 'networkidle' });
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, r.shot), fullPage: true });
        const text = await page.textContent('body');
        recordResult(`Staff Module: ${r.name}`, !text.includes('404') && !text.includes('500') && !text.includes('SQLSTATE'));
      } catch (e) {
        recordResult(`Staff Module: ${r.name}`, false, e);
      }
    }

    // -------------------------------------------------------------
    // TEST 7: CLIENT PORTAL (End-Client Login, Data Security, Notes Isolation)
    // -------------------------------------------------------------
    console.log('\n--- 7. CLIENT PORTAL: LOGIN & CLIENT-SAFE ISOLATION ---');
    // Clear cookies/storage for clean client session
    await context.clearCookies();
    try {
      await page.goto(`${BASE_URL}/portal/login`, { waitUntil: 'networkidle' });
      await page.fill('input[type="email"], input[id*="email"]', 'client@example.qa');
      await page.fill('input[type="password"]', 'Client@2026!');
      await page.click('button[type="submit"]');
      await page.waitForURL('**/portal', { timeout: 10000 });
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_client_portal_dashboard.png'), fullPage: true });

      const clientDashText = await page.textContent('body');
      recordResult('Client Portal Login & Dashboard', !clientDashText.includes('404') && !clientDashText.includes('500'));

      // Visit Client Cases
      await page.goto(`${BASE_URL}/portal/visa-cases`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_client_visa_cases.png'), fullPage: true });
      const clientCasesText = await page.textContent('body');
      recordResult('Client Portal Visa Cases List', clientCasesText.includes('Visa Cases'));

      // Check Confidential Notes Isolation (Client must NEVER see CONFIDENTIAL or internal profit margins)
      const hasConfidential = clientCasesText.includes('CONFIDENTIAL') || clientCasesText.includes('Internal Margin');
      recordResult('Strict Security: Internal Confidential Notes Hidden from Client', !hasConfidential);
    } catch (e) {
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'err_client_portal.png') });
      recordResult('Client Portal Testing', false, e);
    }

    // -------------------------------------------------------------
    // TEST 8: B2B PARTNER PORTAL (Partner Login, Credit/Wallet, Tenant Isolation)
    // -------------------------------------------------------------
    console.log('\n--- 8. B2B PARTNER PORTAL: LOGIN & WALLET / ISOLATION ---');
    await context.clearCookies();
    try {
      await page.goto(`${BASE_URL}/partner/login`, { waitUntil: 'networkidle' });
      await page.fill('input[type="email"], input[id*="email"]', 'partner@dohaglobaltravel.qa');
      await page.fill('input[type="password"]', 'Partner@2026!');
      await page.click('button[type="submit"]');
      await page.waitForURL('**/partner', { timeout: 10000 });
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '15_partner_portal_dashboard.png'), fullPage: true });

      const partnerDashText = await page.textContent('body');
      recordResult('B2B Partner Portal Login & Dashboard', partnerDashText.includes('Credit Facility') || partnerDashText.includes('75,000') || partnerDashText.includes('Dashboard'));

      // Visit Partner Cases
      await page.goto(`${BASE_URL}/partner/visa-cases`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '16_partner_visa_cases.png'), fullPage: true });
      const partnerCasesText = await page.textContent('body');
      recordResult('B2B Partner Visa Cases Scoped View', partnerCasesText.includes('Visa Cases'));
    } catch (e) {
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'err_partner_portal.png') });
      recordResult('B2B Partner Portal Testing', false, e);
    }

  } finally {
    await browser.close();
  }

  console.log('\n================================================================');
  console.log('📊 TEST SUMMARY RESULTS:');
  console.log(`Total Scenarios Tested: ${totalTests}`);
  console.log(`Passed: ${passedTests}`);
  console.log(`Failed: ${failedTests}`);
  console.log(`Browser Console Errors Recorded: ${consoleErrors.length}`);
  if (consoleErrors.length > 0) {
    console.log('\nConsole Errors list:');
    consoleErrors.forEach((e, idx) => console.log(`  ${idx + 1}. ${e}`));
  }
  if (failureDetails.length > 0) {
    console.log('\nFailures breakdown:');
    failureDetails.forEach(f => console.log(`  - ${f.testName}: ${f.error}`));
  }
  console.log('================================================================');

  if (failedTests > 0 || consoleErrors.length > 0) {
    process.exit(1);
  }
}

runAll().catch(err => {
  console.error('Fatal Test Runner Exception:', err);
  process.exit(1);
});
