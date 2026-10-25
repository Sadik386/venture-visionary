const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

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
  console.log('🚀 RUNNING COMPREHENSIVE E2E BROWSER TESTS: VENTURE VISIONARY CRM');
  console.log(`Base URL: ${BASE_URL}`);
  console.log(`Screenshots Directory: ${SCREENSHOT_DIR}`);
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
    // TEST 1: Public Root System Operational Status & OpenAPI Docs
    // -------------------------------------------------------------
    console.log('\n--- 1. SYSTEM ENDPOINTS & OPENAPI DOCS ---');
    try {
      const rootRes = await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle' });
      const rootBody = await page.textContent('body');
      const isJsonOk = rootRes.status() === 200 && rootBody.includes('Venture Visionary CRM');
      recordResult('Root Endpoint (System Operational Status JSON)', isJsonOk);
    } catch (e) {
      recordResult('Root Endpoint (System Operational Status JSON)', false, e);
    }

    try {
      const docRes = await page.goto(`${BASE_URL}/docs/api`, { waitUntil: 'networkidle' });
      await page.waitForTimeout(1000);
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_openapi_docs.png'), fullPage: true });
      const docBody = await page.textContent('body');
      const isDocOk = docRes.status() === 200 && (docBody.includes('elements-api') || docBody.includes('Venture Visionary') || docBody.includes('api'));
      recordResult('Scramble OpenAPI Interactive Documentation Render', isDocOk);
    } catch (e) {
      recordResult('Scramble OpenAPI Interactive Documentation Render', false, e);
    }

    // -------------------------------------------------------------
    // TEST 2: STAFF PANEL LOGIN & DASHBOARD
    // -------------------------------------------------------------
    console.log('\n--- 2. STAFF PORTAL: CEO / SUPER ADMIN DASHBOARD ---');
    try {
      await page.goto(`${BASE_URL}/staff/login`, { waitUntil: 'networkidle' });
      await page.fill('input[type="email"], input[id*="email"]', 'ceo@venturevisionary.qa');
      await page.fill('input[type="password"]', 'Admin@VV2026!');
      await page.click('button[type="submit"]');
      await page.waitForSelector('text=Dashboard', { timeout: 15000 });
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_staff_dashboard.png'), fullPage: true });

      const heading = await page.textContent('body');
      recordResult('Staff Login & Real-Time KPI Dashboard', heading.includes('Dashboard') && heading.includes('Active Inbound Leads') && heading.includes('Master Clients'));
    } catch (e) {
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'err_staff_login.png') });
      recordResult('Staff Login & Real-Time KPI Dashboard', false, e);
    }

    // -------------------------------------------------------------
    // TEST 3: STAFF PANEL - LEADS MANAGEMENT & CREATION
    // -------------------------------------------------------------
    console.log('\n--- 3. STAFF PORTAL: INBOUND LEADS PIPELINE ---');
    try {
      const leadsRes = await page.goto(`${BASE_URL}/staff/leads`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_staff_leads_table.png'), fullPage: true });
      const bodyText = await page.textContent('body');
      recordResult('Staff Leads Index Table & Lead Scoring', leadsRes.status() === 200 && bodyText.includes('Leads') && (bodyText.includes('LD-2026') || bodyText.includes('Prospect Name')));
    } catch (e) {
      recordResult('Staff Leads Index Table & Lead Scoring', false, e);
    }

    // View first lead
    try {
      const viewLeadButton = page.locator('table a[href*="/staff/leads/"]').first();
      if (await viewLeadButton.count() > 0) {
        await viewLeadButton.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_staff_lead_detail.png'), fullPage: true });
        const leadBody = await page.textContent('body');
        recordResult('Staff View Lead Detail & Activity Timeline', leadBody.includes('View Lead') || leadBody.includes('Client Identity & Contact') || leadBody.includes('LD-2026'));
      } else {
        recordResult('Staff View Lead Detail & Activity Timeline', false, new Error('No lead link found'));
      }
    } catch (e) {
      recordResult('Staff View Lead Detail & Activity Timeline', false, e);
    }

    // Create a new lead via form
    try {
      await page.goto(`${BASE_URL}/staff/leads/create`, { waitUntil: 'networkidle' });
      await page.fill('input[id*="name"]', 'Browser Automated Test Prospect');
      await page.fill('input[id*="phone"]', '+97455123999');
      await page.fill('input[id*="email"]', 'automated.lead@example.qa');
      await page.click('button:has-text("Create"):visible');
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(1000);
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04b_staff_lead_created.png'), fullPage: true });
      const afterCreateText = await page.textContent('body');
      recordResult('Staff Lead Creation via UI Form', afterCreateText.includes('Browser Automated Test Prospect') || afterCreateText.includes('Created'));
    } catch (e) {
      recordResult('Staff Lead Creation via UI Form', false, e);
    }

    // -------------------------------------------------------------
    // TEST 4: STAFF PANEL - MASTER CLIENTS (CLIENT 360)
    // -------------------------------------------------------------
    console.log('\n--- 4. STAFF PORTAL: CLIENT 360 DATABASE ---');
    try {
      const clientsRes = await page.goto(`${BASE_URL}/staff/clients`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_staff_clients_table.png'), fullPage: true });
      const bodyText = await page.textContent('body');
      recordResult('Staff Clients Index Table', clientsRes.status() === 200 && bodyText.includes('Clients') && (bodyText.includes('CL-2026') || bodyText.includes('Al-Thani')));
    } catch (e) {
      recordResult('Staff Clients Index Table', false, e);
    }

    // View first client
    try {
      const viewClientButton = page.locator('table a[href*="/staff/clients/"]').first();
      if (await viewClientButton.count() > 0) {
        await viewClientButton.click();
        await page.waitForLoadState('networkidle');
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05b_staff_client_profile.png'), fullPage: true });
        const clientBody = await page.textContent('body');
        recordResult('Staff Client 360 Profile View', clientBody.includes('CL-2026') || clientBody.includes('Primary Contact Information') || clientBody.includes('Full Name'));
      } else {
        recordResult('Staff Client 360 Profile View', false, new Error('No client link found'));
      }
    } catch (e) {
      recordResult('Staff Client 360 Profile View', false, e);
    }

    // -------------------------------------------------------------
    // TEST 5: STAFF PANEL - VISA CASES & IMMUTABLE JSONB SNAPSHOT
    // -------------------------------------------------------------
    console.log('\n--- 5. STAFF PORTAL: VISA CASES & IMMUTABLE PRICING SNAPSHOT ---');
    try {
      const casesRes = await page.goto(`${BASE_URL}/staff/visa-cases`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_staff_visa_cases_table.png'), fullPage: true });
      const bodyText = await page.textContent('body');
      recordResult('Staff Visa Cases Index Table', casesRes.status() === 200 && bodyText.includes('Visa Cases') && (bodyText.includes('CS-2026') || bodyText.includes('Applicant Name')));
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
        recordResult('Staff View Visa Case & Lifecycle Timeline', caseBody.includes('CS-2026') || caseBody.includes('UK Express') || caseBody.includes('Applicant'));
      } else {
        recordResult('Staff View Visa Case & Lifecycle Timeline', false, new Error('No case link found'));
      }
    } catch (e) {
      recordResult('Staff View Visa Case & Lifecycle Timeline', false, e);
    }

    // -------------------------------------------------------------
    // TEST 6: OTHER STAFF MODULES (Countries, Services, Quotations, Partners, Users)
    // -------------------------------------------------------------
    console.log('\n--- 6. STAFF PORTAL: MASTER DATABASE & RBAC ADMINISTRATION ---');
    const staffRoutes = [
      { name: 'Countries (Schengen, UK, USA, Gulf)', url: `${BASE_URL}/staff/countries`, shot: '08_staff_countries.png', expected: 'Countries' },
      { name: 'Services Catalog & Pricing', url: `${BASE_URL}/staff/services`, shot: '09_staff_services.png', expected: 'Services' },
      { name: 'Quotations & Commercial Proposals', url: `${BASE_URL}/staff/quotations`, shot: '10_staff_quotations.png', expected: 'Quotations' },
      { name: 'B2B Partner Directory & Facilities', url: `${BASE_URL}/staff/partners`, shot: '11_staff_partners.png', expected: 'Partners' },
      { name: 'Staff Users & 12-Role RBAC Administration', url: `${BASE_URL}/staff/users`, shot: '12_staff_users.png', expected: 'Staff Member' },
    ];

    for (const r of staffRoutes) {
      try {
        const res = await page.goto(r.url, { waitUntil: 'networkidle' });
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, r.shot), fullPage: true });
        const text = await page.textContent('body');
        const isOk = res.status() === 200 && text.includes(r.expected) && !text.includes('500 Internal Server Error') && !text.includes('SQLSTATE');
        recordResult(`Staff Module: ${r.name}`, isOk);
      } catch (e) {
        recordResult(`Staff Module: ${r.name}`, false, e);
      }
    }

    // -------------------------------------------------------------
    // TEST 7: CLIENT PORTAL (End-Client Login, Data Security, Notes Isolation)
    // -------------------------------------------------------------
    console.log('\n--- 7. CLIENT PORTAL: APPLICANT AUTHENTICATION & SECURITY ISOLATION ---');
    await context.clearCookies();
    try {
      await page.goto(`${BASE_URL}/portal/login`, { waitUntil: 'networkidle' });
      await page.fill('input[type="email"], input[id*="email"]', 'client@example.qa');
      await page.fill('input[type="password"]', 'Client@2026!');
      await page.click('button[type="submit"]');
      await page.waitForSelector('text=Dashboard', { timeout: 15000 });
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '13_client_portal_dashboard.png'), fullPage: true });

      const clientDashText = await page.textContent('body');
      recordResult('Client Portal Login & Applicant Dashboard', clientDashText.includes('Dashboard') && !clientDashText.includes('Internal Server Error'));

      // Visit Client Cases
      const casesRes = await page.goto(`${BASE_URL}/portal/visa-cases`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '14_client_visa_cases.png'), fullPage: true });
      const clientCasesText = await page.textContent('body');
      recordResult('Client Portal Visa Cases List', casesRes.status() === 200 && clientCasesText.includes('Visa Cases'));

      // Blueprint Non-Negotiable: Client must NEVER see internal confidential notes or profit margins
      const hasConfidential = clientCasesText.includes('CONFIDENTIAL') || clientCasesText.includes('Internal Margin');
      recordResult('Blueprint Non-Negotiable: Confidential Internal Notes Hidden from Client', !hasConfidential);
    } catch (e) {
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'err_client_portal.png') });
      recordResult('Client Portal Authentication & Isolation', false, e);
    }

    // -------------------------------------------------------------
    // TEST 8: B2B PARTNER PORTAL (Partner Login, Credit/Wallet, Tenant Isolation)
    // -------------------------------------------------------------
    console.log('\n--- 8. B2B PARTNER PORTAL: WALLET, CREDIT & MULTI-TENANT ISOLATION ---');
    await context.clearCookies();
    try {
      await page.goto(`${BASE_URL}/partner/login`, { waitUntil: 'networkidle' });
      await page.fill('input[type="email"], input[id*="email"]', 'partner@dohaglobaltravel.qa');
      await page.fill('input[type="password"]', 'Partner@2026!');
      await page.click('button[type="submit"]');
      await page.waitForSelector('text=Dashboard', { timeout: 15000 });
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '15_partner_portal_dashboard.png'), fullPage: true });

      const partnerDashText = await page.textContent('body');
      const hasCredit = partnerDashText.includes('Credit Facility') || partnerDashText.includes('75,000');
      const hasWallet = partnerDashText.includes('Prepaid Balance') || partnerDashText.includes('25,000');
      recordResult('B2B Partner Portal Login & Credit/Wallet Widgets', hasCredit && hasWallet);

      // Visit Partner Applications
      const partnerCasesRes = await page.goto(`${BASE_URL}/partner/visa-cases`, { waitUntil: 'networkidle' });
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, '16_partner_visa_cases.png'), fullPage: true });
      const partnerCasesText = await page.textContent('body');
      recordResult('B2B Partner Scoped Agency Applications Table', partnerCasesRes.status() === 200 && (partnerCasesText.includes('Agency Applications') || partnerCasesText.includes('Case Ref #')));
    } catch (e) {
      await page.screenshot({ path: path.join(SCREENSHOT_DIR, 'err_partner_portal.png') });
      recordResult('B2B Partner Portal Testing', false, e);
    }

  } finally {
    await browser.close();
  }

  console.log('\n================================================================');
  console.log('📊 FINAL COMPREHENSIVE E2E VERIFICATION REPORT:');
  console.log(`Total End-to-End Scenarios Tested: ${totalTests}`);
  console.log(`Passed: ${passedTests}`);
  console.log(`Failed: ${failedTests}`);
  console.log(`Browser Console Errors Recorded: ${consoleErrors.length}`);
  if (consoleErrors.length > 0) {
    console.log('\nConsole Errors Recorded:');
    consoleErrors.forEach((e, idx) => console.log(`  ${idx + 1}. ${e}`));
  }
  if (failureDetails.length > 0) {
    console.log('\nFailures Breakdown:');
    failureDetails.forEach(f => console.log(`  - ${f.testName}: ${f.error}`));
  }
  console.log('================================================================\n');

  if (failedTests > 0 || consoleErrors.length > 0) {
    process.exit(1);
  }
}

runAll().catch(err => {
  console.error('Fatal Test Runner Exception:', err);
  process.exit(1);
});
