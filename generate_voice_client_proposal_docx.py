import os
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, hex_color):
    shading_elm = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    cell._tc.get_or_add_tcPr().append(shading_elm)

def set_cell_margins(cell, top=100, bottom=100, left=140, right=140):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def set_table_borders(table, color="CBD5E1", sz="4", val="single"):
    tblPr = table._tbl.tblPr
    borders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>'
        f'<w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:insideV w:val="none"/>'
        f'<w:left w:val="none"/>'
        f'<w:right w:val="none"/>'
        f'</w:tblBorders>'
    )
    tblPr.append(borders)

def build_proposal_document():
    doc = Document()

    # 0.75-inch margins
    for section in doc.sections:
        section.top_margin = Inches(0.75)
        section.bottom_margin = Inches(0.75)
        section.left_margin = Inches(0.75)
        section.right_margin = Inches(0.75)

    # Color Palette
    NAVY = RGBColor(15, 41, 66)       # #0F2942 - Primary Headings
    ROYAL = RGBColor(30, 58, 138)     # #1E3A8A - Subheadings & Accents
    CHARCOAL = RGBColor(30, 41, 59)   # #1E293B - Body text
    MUTED = RGBColor(100, 116, 139)   # #64748B - Metadata & Subtitles
    EMERALD = RGBColor(16, 122, 60)   # #107A3C - Highlights & Savings

    # Helper Typography
    def add_title(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(20)
        run.font.bold = True
        run.font.color.rgb = NAVY
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(2)
        return p

    def add_subtitle(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(11)
        run.font.color.rgb = MUTED
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(12)
        return p

    def add_heading1(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(13)
        run.font.bold = True
        run.font.color.rgb = NAVY
        p.paragraph_format.space_before = Pt(14)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        return p

    def add_heading2(text):
        p = doc.add_paragraph()
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(11)
        run.font.bold = True
        run.font.color.rgb = ROYAL
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.keep_with_next = True
        return p

    def add_body(text, bold_prefix=None, italic=False):
        p = doc.add_paragraph()
        if bold_prefix:
            run_b = p.add_run(bold_prefix)
            run_b.font.name = 'Calibri'
            run_b.font.size = Pt(10)
            run_b.font.bold = True
            run_b.font.color.rgb = CHARCOAL
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(10)
        run.font.italic = italic
        run.font.color.rgb = CHARCOAL
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.line_spacing = 1.15
        return p

    def add_bullet(title, text):
        p = doc.add_paragraph(style='List Bullet')
        run_t = p.add_run(title + ": ")
        run_t.font.name = 'Calibri'
        run_t.font.size = Pt(10)
        run_t.font.bold = True
        run_t.font.color.rgb = CHARCOAL

        run_d = p.add_run(text)
        run_d.font.name = 'Calibri'
        run_d.font.size = Pt(10)
        run_d.font.color.rgb = CHARCOAL
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.line_spacing = 1.15
        return p

    # -------------------------------------------------------------------------
    # DOCUMENT HEADER
    # -------------------------------------------------------------------------
    add_title("COMMERCIAL CLIENT PROPOSAL")
    add_subtitle("Custom Management Platform for Venture Visionary Visa & Business Consultancy (Qatar)")

    # Overview Metadata Table
    info_table = doc.add_table(rows=2, cols=3)
    info_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(info_table, color="CBD5E1", sz="4")

    info_data = [
        [("Client Name:", " Venture Visionary Consultancy"), ("Prepared For:", " Executive Management, Qatar"), ("Date:", " October 2026")],
        [("Project:", " Centralized Visa CRM & Portals"), ("Pricing Structure:", " 3 Commercial Options (TCO Analyzed)"), ("Currency:", " BDT (Bangladeshi Taka)")]
    ]

    for r_idx, row in enumerate(info_data):
        for c_idx, (label, val) in enumerate(row):
            cell = info_table.cell(r_idx, c_idx)
            set_cell_background(cell, "F8FAFC")
            set_cell_margins(cell, top=100, bottom=100, left=120, right=120)
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            run_lbl = p.add_run(label)
            run_lbl.font.name = 'Calibri'
            run_lbl.font.size = Pt(9)
            run_lbl.font.bold = True
            run_lbl.font.color.rgb = NAVY
            run_v = p.add_run(val)
            run_v.font.name = 'Calibri'
            run_v.font.size = Pt(9)
            run_v.font.color.rgb = CHARCOAL

    for row in info_table.rows:
        row.cells[0].width = Inches(2.3)
        row.cells[1].width = Inches(2.6)
        row.cells[2].width = Inches(2.1)

    # -------------------------------------------------------------------------
    # 1. EXECUTIVE SUMMARY & BUSINESS VALUE
    # -------------------------------------------------------------------------
    add_heading1("1. EXECUTIVE SUMMARY & BUSINESS VALUE")
    add_body(
        "This commercial proposal presents the complete plan to build and deploy a dedicated management system for Venture Visionary Visa & Business Consultancy in Qatar. "
        "The system replaces fragmented spreadsheets, manual WhatsApp tracking, and scattered emails with a single unified operational hub tailored specifically to your visa consulting and business setup workflows."
    )
    add_body(
        "Key Business Problems Solved by the Platform:",
        bold_prefix="Direct Business Impact: "
    )
    add_bullet("Stop Losing Inbound Leads", "All customer inquiries from WhatsApp, Facebook Ads, Instagram, your website, and office visits flow automatically into one central team inbox with instant staff assignment.")
    add_bullet("Eliminate Manual Document Mistakes", "Instant dynamic checklists for 193 destination countries and Qatar resident categories ensure your staff never submit incomplete files or expired passports/QIDs.")
    add_bullet("Stop Repetitive Client Phone Inquiries", "Clients receive their own 24/7 online tracking portal where they can check their application status, upload requested documents, and download approved visas directly from their phone.")
    add_bullet("Streamline B2B Sub-Agents & Travel Partners", "Partner agencies get a secure portal to submit client files, view negotiated wholesale net rates, and track prepaid wallet balances or credit limits.")
    add_bullet("Track Real Profits Per Case", "Quickly generate professional itemized invoices showing embassy fees, biometric fees, and your consultancy fee, with real-time profit margin tracking for management.")

    # -------------------------------------------------------------------------
    # 2. CORE SYSTEM DELIVERABLES (WHAT WE BUILD)
    # -------------------------------------------------------------------------
    add_heading1("2. CORE SYSTEM DELIVERABLES (WHAT WE BUILD FOR YOU)")
    add_body("The custom platform provides 5 primary functional hubs designed to run your day-to-day operations seamlessly:")

    add_bullet(
        "1. Central Staff Operations & CRM Hub",
        "Centralized lead intake, automated lead assignment to sales officers (Round Robin), client 360 database, 193-country visa master requirements, document quality control (QC) verification, and step-by-step visa case lifecycle management."
    )
    add_bullet(
        "2. Client Self-Service Tracking Portal",
        "A secure, mobile-friendly portal where your applicants log in via their phone number to view their live application progress, upload missing documents, and download completed forms and receipts. Internal staff notes remain 100% private."
    )
    add_bullet(
        "3. B2B Travel Partner & Affiliate Portal",
        "A dedicated login for external travel agencies and corporate partners to submit visa cases, view negotiated net prices, track submitted files, and manage their assigned credit limit."
    )
    add_bullet(
        "4. Quotation, Invoicing & Financial Ledger",
        "Instant generation of professional branded PDF quotations and invoices in multiple currencies. Automatically splits government embassy fees from consultancy fees and tracks profit margins per case."
    )
    add_bullet(
        "5. Executive Management Dashboard",
        "A real-time overview for business owners and managers showing daily active leads, ongoing visa files, monthly revenue, pending staff tasks, and downloadable performance reports."
    )

    # -------------------------------------------------------------------------
    # 3. COMMERCIAL OPTIONS & TOTAL FIRST-YEAR COST ANALYSIS
    # -------------------------------------------------------------------------
    add_heading1("3. COMMERCIAL OPTIONS & TOTAL FIRST-YEAR COST ANALYSIS")
    add_body(
        "To provide full commercial transparency, we present three straightforward options. "
        "Notably, when our agency bundles and manages the infrastructure (Option B), your total first-year cost is significantly lower than procuring retail vendor accounts on your own (Option C):"
    )

    # Summary Pricing Comparison Table
    sum_table = doc.add_table(rows=4, cols=4)
    sum_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(sum_table, color="CBD5E1", sz="4")

    sum_headers = ["Option", "Paid to Development Team", "Third-Party Tools / APIs", "Total First-Year Cost to Venture Visionary"]
    for idx, text in enumerate(sum_headers):
        cell = sum_table.cell(0, idx)
        set_cell_background(cell, "0F2942")
        set_cell_margins(cell, top=100, bottom=100, left=90, right=90)
        p = cell.paragraphs[0]
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(0)
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(9)
        run.font.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)

    sum_rows = [
        ("Option A: Pure Development", "BDT 1,80,000", "Client provides & pays own servers/APIs", "BDT 1,80,000\nplus own running costs"),
        ("Option B: Turnkey (We Handle Services & API)\n[RECOMMENDED]", "BDT 3,11,000", "INCLUDED\n(Borne & managed by us for Full 1st Year)", "BDT 3,11,000\n(Fixed, All-Inclusive First Year)\n★ Saves ~BDT 42,000+ vs Option C"),
        ("Option C: Client Pays Providers Directly", "BDT 1,80,000", "About BDT 1,73,000\n(Paid directly to vendors at retail rates)", "About BDT 3,53,000\n(BDT 1,80,000 + ~BDT 1,73,000 to providers)"),
    ]

    for r_idx, row in enumerate(sum_rows, start=1):
        for c_idx, text in enumerate(row):
            cell = sum_table.cell(r_idx, c_idx)
            if r_idx == 2:
                set_cell_background(cell, "ECFDF5")  # Highlight Best Value Option B
            else:
                set_cell_background(cell, "FFFFFF" if r_idx % 2 == 1 else "F8FAFC")
            set_cell_margins(cell, top=90, bottom=90, left=90, right=90)
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            run = p.add_run(text)
            run.font.name = 'Calibri'
            run.font.size = Pt(8.5)
            if r_idx == 2 or c_idx == 0:
                run.font.bold = True
            if r_idx == 2 and c_idx == 3:
                run.font.color.rgb = EMERALD
            else:
                run.font.color.rgb = CHARCOAL

    for row in sum_table.rows:
        row.cells[0].width = Inches(2.2)
        row.cells[1].width = Inches(1.5)
        row.cells[2].width = Inches(1.6)
        row.cells[3].width = Inches(1.7)

    doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # -------------------------------------------------------------------------
    # DETAILED OPTION BRIEFS & BENEFITS
    # -------------------------------------------------------------------------
    # OPTION A
    add_heading2("OPTION A: Pure Development (Codebase & Architecture Only)")
    add_body(
        "Option A covers 100% of the custom software engineering, database schemas, business logic, and portal design. "
        "The completed platform is delivered as a thoroughly tested, production-ready codebase package with deployment documentation. "
        "Cloud servers, domain setup, hosting accounts, and third-party API procurements are excluded from our scope and handled independently by your team.",
        bold_prefix="Concise Brief: "
    )
    add_bullet("Paid to You", "BDT 1,80,000 fixed one-time development fee.")
    add_bullet("Total First-Year Cost", "BDT 1,80,000 plus your own internal running costs.")
    add_bullet("Key Benefits", "Lowest upfront outlay; zero vendor bundling; client receives complete source code ownership to deploy on any server of their choice.")

    doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # OPTION B
    add_heading2("OPTION B: Turnkey, You Handle Services & API (All-Inclusive & Lowest Total Cost) — RECOMMENDED")
    add_body(
        "Option B is the complete, worry-free turnkey solution. We handle 100% of custom software development PLUS we procure, configure, and manage high-speed dedicated cloud hosting, automated database backups, SSL security certificates, Meta WhatsApp Business API conversation credits, and transactional SMS verification for the ENTIRE first year.",
        bold_prefix="Concise Brief: "
    )
    add_bullet("Paid to You", "BDT 3,11,000 all-inclusive package price.")
    add_bullet("Total First-Year Cost", "BDT 3,11,000 fixed for the entire first year (no extra vendor bills).")
    add_bullet("Why Option B Costs LESS Than Option C", "Because we leverage our agency bulk infrastructure, shared high-speed server clusters, and developer tier API discounts, we pass the savings directly to you. This saves Venture Visionary approximately BDT 42,000 in the first year compared to purchasing tools individually at retail rates (BDT 3,11,000 vs. BDT 3,53,000).")
    add_bullet("Key Benefits", "Maximum financial savings; zero technical burden; no multiple credit card subscriptions; guaranteed 99.9% server uptime and message delivery under a single point of responsibility.")

    doc.add_paragraph().paragraph_format.space_after = Pt(4)

    # OPTION C
    add_heading2("OPTION C: Client Pays Providers Directly (Co-Managed / Retail Billing)")
    add_body(
        "In Option C, we build 100% of the custom software platform, deploy it directly onto your own cloud server, and integrate your official WhatsApp API keys. Your company creates the accounts and pays cloud and messaging providers directly with your company card at retail rates.",
        bold_prefix="Concise Brief: "
    )
    add_bullet("Paid to You", "BDT 1,80,000 custom development fee.")
    add_bullet("Third-Party Vendor Costs", "About BDT 1,73,000 in Year 1 (Cloud VPS & storage ~BDT 54,000/yr + Meta WhatsApp conversations ~BDT 85,000/yr + SMS verification, domain & SSL ~BDT 34,000/yr).")
    add_bullet("Total First-Year Cost", "About BDT 3,53,000 (BDT 1,80,000 to us + about BDT 1,73,000 to third-party providers).")
    add_bullet("Key Benefits & Trade-Offs", "Direct account ownership with vendors from day one, but results in a higher total first-year expense (~BDT 42,000 higher than Option B) and requires your team to manage multiple international monthly credit card invoices.")

    doc.add_paragraph().paragraph_format.space_after = Pt(8)

    # -------------------------------------------------------------------------
    # 4. DELIVERY SCHEDULE & IMPLEMENTATION PHASES
    # -------------------------------------------------------------------------
    add_heading1("4. DELIVERY TIMELINE & IMPLEMENTATION PHASES")
    add_body("The project is structured into 3 clear, manageable stages so your team can test each module as it is delivered:")

    add_bullet(
        "Stage 1: Core Operations & Lead Hub (Weeks 1 – 6)",
        "Deploy the main Staff CRM, connect website and social media leads, set up WhatsApp messaging, build the 193-country visa master database, and configure document checklists."
    )
    add_bullet(
        "Stage 2: Client Portal & Quotation Invoicing (Weeks 7 – 11)",
        "Launch client self-service tracking and document uploads, build the quotation and invoicing engine with automatic profit margin calculations."
    )
    add_bullet(
        "Stage 3: B2B Partner Portal & Go-Live (Weeks 12 – 15)",
        "Deliver the B2B Travel Partner portal with wholesale net rates, conduct staff training sessions, provide video documentation, and perform final production rollout."
    )

    # -------------------------------------------------------------------------
    # 5. PAYMENT SCHEDULE & MILESTONES
    # -------------------------------------------------------------------------
    add_heading1("5. PAYMENT SCHEDULE (LINKED TO DELIVERABLES)")
    add_body("Payments are tied directly to verified milestone deliverables to ensure complete financial safety:")

    pay_table = doc.add_table(rows=5, cols=4)
    pay_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(pay_table, color="CBD5E1", sz="4")

    pay_headers = ["Milestone", "Deliverable Condition", "Option A & C (Dev Fee)", "Option B (Turnkey All-Inclusive)"]
    for idx, text in enumerate(pay_headers):
        cell = pay_table.cell(0, idx)
        set_cell_background(cell, "0F2942")
        set_cell_margins(cell, top=100, bottom=100, left=90, right=90)
        p = cell.paragraphs[0]
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(0)
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(9)
        run.font.bold = True
        run.font.color.rgb = RGBColor(255, 255, 255)

    pay_rows = [
        ("Milestone 1: Project Kickoff (25%)", "Agreement signing, architecture blueprint setup, and visual layout approval.", "BDT 45,000", "BDT 77,750"),
        ("Milestone 2: Core CRM Demo (35%)", "Delivery of Staff CRM, Lead Engine, 193-Country Visa Directory, and Document QC.", "BDT 63,000", "BDT 1,08,850"),
        ("Milestone 3: Portals & Billing (25%)", "Delivery of Client Tracking Portal, Invoicing engine, and B2B Partner Portal.", "BDT 45,000", "BDT 77,750"),
        ("Milestone 4: Handover & Launch (15%)", "Final testing, staff training, handover of administrative access, and live launch.", "BDT 27,000", "BDT 46,650"),
    ]

    for r_idx, row in enumerate(pay_rows, start=1):
        for c_idx, text in enumerate(row):
            cell = pay_table.cell(r_idx, c_idx)
            set_cell_background(cell, "FFFFFF" if r_idx % 2 == 1 else "F8FAFC")
            set_cell_margins(cell, top=90, bottom=90, left=90, right=90)
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            run = p.add_run(text)
            run.font.name = 'Calibri'
            run.font.size = Pt(8.5)
            if c_idx == 0 or c_idx == 2 or c_idx == 3:
                run.font.bold = True
            run.font.color.rgb = CHARCOAL

    for row in pay_table.rows:
        row.cells[0].width = Inches(1.8)
        row.cells[1].width = Inches(2.6)
        row.cells[2].width = Inches(1.3)
        row.cells[3].width = Inches(1.3)

    # -------------------------------------------------------------------------
    # 6. POST-LAUNCH WARRANTY & SUPPORT
    # -------------------------------------------------------------------------
    add_heading1("6. WARRANTY & SUPPORT COMMITMENT")
    add_body("To ensure a smooth transition and reliable everyday operation:")
    add_bullet("60 Days Complimentary Technical Warranty", "Immediate bug-fixing and technical adjustments following live deployment at no additional cost.")
    add_bullet("Staff Training & Video Guides", "Live interactive training sessions for your management and operations staff, plus recorded walkthroughs for new hires.")
    add_bullet("100% System Ownership", "Upon project completion and final payment, your company holds complete ownership of your database, client records, and software system with no vendor lock-in.")

    # -------------------------------------------------------------------------
    # 7. ACCEPTANCE & SIGN-OFF
    # -------------------------------------------------------------------------
    add_heading1("7. AGREEMENT & SIGN-OFF")
    add_body("Please select your preferred commercial option and sign below to initiate project onboarding:")

    choice_p = doc.add_paragraph()
    run_ch = choice_p.add_run(
        "[   ] Option A: Pure Development (Paid to us: BDT 1,80,000)\n"
        "[   ] Option B: Turnkey, You Handle Services & API (Paid to us: BDT 3,11,000) — RECOMMENDED (Lowest TCO)\n"
        "[   ] Option C: Client Pays Providers Directly (Paid to us: BDT 1,80,000 + ~BDT 1,73,000 to providers = ~BDT 3,53,000 Total)"
    )
    run_ch.font.name = 'Calibri'
    run_ch.font.size = Pt(9.5)
    run_ch.font.bold = True
    run_ch.font.color.rgb = ROYAL
    choice_p.paragraph_format.space_before = Pt(4)
    choice_p.paragraph_format.space_after = Pt(10)

    sign_table = doc.add_table(rows=3, cols=2)
    sign_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    set_table_borders(sign_table, color="CBD5E1", sz="4")

    sign_cells = [
        [("Prepared By (Development Team):", ""), ("Accepted & Approved By (Client):", "")],
        [("Software Solutions & Development Lead\nVenture Visionary CRM Project Team", ""), ("Executive Management / Authorized Signatory\nVenture Visionary Visa & Business Consultancy, Qatar", "")],
        [("Signature: ___________________________\n\nDate: _______________________________", ""), ("Signature: ___________________________\n\nDate: _______________________________", "")]
    ]

    for r_idx, row in enumerate(sign_cells):
        for c_idx, (text, _) in enumerate(row):
            cell = sign_table.cell(r_idx, c_idx)
            set_cell_background(cell, "F8FAFC")
            set_cell_margins(cell, top=120, bottom=120, left=150, right=150)
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(0)
            p.paragraph_format.space_after = Pt(0)
            run = p.add_run(text)
            run.font.name = 'Calibri'
            run.font.size = Pt(9.5)
            if r_idx == 0:
                run.font.bold = True
                run.font.color.rgb = NAVY
            else:
                run.font.color.rgb = CHARCOAL

    for row in sign_table.rows:
        row.cells[0].width = Inches(3.5)
        row.cells[1].width = Inches(3.5)

    output_path = "/home/sadik/sadik/crm/Venture_Visionary_CRM_Client_Proposal.docx"
    doc.save(output_path)
    print(f"Proposal DOCX successfully saved at: {output_path}")

    backup_path = "/home/sadik/sadik/crm/Venture_Visionary_CRM_Simplified_Client_Proposal.docx"
    doc.save(backup_path)
    print(f"Proposal DOCX backup successfully saved at: {backup_path}")

if __name__ == '__main__':
    build_proposal_document()
