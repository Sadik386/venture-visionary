import os
import sys
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        
        # Running Header on pages 2+
        if self._pageNumber > 1:
            self.drawString(54, 804, "VENTURE VISIONARY CRM (v2.0) — COMMERCIAL PROPOSAL & SYSTEM IMPLEMENTATION")
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.5)
            self.line(54, 798, 541, 798)

        # Running Footer on all pages
        self.setStrokeColor(colors.HexColor("#CBD5E1"))
        self.setLineWidth(0.5)
        self.line(54, 45, 541, 45)
        
        footer_text = "Confidential | Prepared for Venture Visionary Visa and Business Consultancy, Qatar"
        self.drawString(54, 32, footer_text)
        page_str = f"Page {self._pageNumber} of {page_count}"
        self.drawRightString(541, 32, page_str)
        self.restoreState()

def build_pdf(filename="/home/sadik/sadik/crm/Venture_Visionary_CRM_Commercial_Proposal.pdf"):
    # Target printable area: A4 is 595.27 x 841.89 points
    # Printable width: 595.27 - 108 = 487.27 pt
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        leftMargin=54,
        rightMargin=54,
        topMargin=46,
        bottomMargin=46
    )

    styles = getSampleStyleSheet()

    # Brand Colors
    primary_color = colors.HexColor("#0F2942")     # Deep Navy
    secondary_color = colors.HexColor("#1E3A8A")   # Steel Blue
    accent_teal = colors.HexColor("#0D9488")       # Teal
    dark_slate = colors.HexColor("#1E293B")        # Charcoal Text
    muted_slate = colors.HexColor("#475569")       # Muted Secondary
    card_bg = colors.HexColor("#F8FAFC")           # Off-white card fill
    border_color = colors.HexColor("#E2E8F0")      # Light border
    highlight_bg = colors.HexColor("#F1F5F9")      # Row highlight

    doc_title_style = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=18,
        leading=22,
        textColor=primary_color,
        spaceAfter=3
    )

    doc_sub_style = ParagraphStyle(
        'DocSub',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=13.5,
        textColor=muted_slate,
        spaceAfter=10
    )

    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=15,
        textColor=primary_color,
        spaceBefore=8,
        spaceAfter=4,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=10,
        leading=13,
        textColor=secondary_color,
        spaceBefore=6,
        spaceAfter=3,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=dark_slate,
        spaceAfter=4
    )

    table_header_style = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=colors.white
    )

    table_cell_style = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=7.8,
        leading=10.5,
        textColor=dark_slate
    )

    table_cell_bold = ParagraphStyle(
        'TableCellBold',
        parent=table_cell_style,
        fontName='Helvetica-Bold'
    )

    table_cell_right = ParagraphStyle(
        'TableCellRight',
        parent=table_cell_style,
        alignment=2
    )

    table_cell_right_bold = ParagraphStyle(
        'TableCellRightBold',
        parent=table_cell_bold,
        alignment=2
    )

    story = []

    # ====================================================
    # PAGE 1: TITLE, META, EXECUTIVE SUMMARY, OPTION A
    # ====================================================
    story.append(Paragraph("COMMERCIAL PROPOSAL & SYSTEM IMPLEMENTATION PLAN", doc_title_style))
    story.append(Paragraph(
        "<b>Project:</b> Venture Visionary CRM & Business Consultancy Platform (Version 2.0)<br/>"
        "<b>Client:</b> Venture Visionary Visa and Business Consultancy, Qatar<br/>"
        "<b>Execution Model:</b> Open-Source Foundations + 50% AI-Assisted Labor Reduction + Incremental Phased Delivery",
        doc_sub_style
    ))
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceBefore=0, spaceAfter=8))

    meta_data = [
        [
            Paragraph("<b>Document Version:</b> 2.0 Commercial", table_cell_style),
            Paragraph("<b>Target Region:</b> Qatar & International Operations", table_cell_style),
            Paragraph("<b>Currency:</b> BDT (Tk.) & USD ($)", table_cell_style)
        ],
        [
            Paragraph("<b>Blueprint Compliance:</b> 100% (29 Sections)", table_cell_style),
            Paragraph("<b>Core Stack:</b> Laravel / FilamentPHP / Chatwoot / n8n", table_cell_style),
            Paragraph("<b>Overall Timeline:</b> 4 – 5 Months (Phased)", table_cell_style)
        ]
    ]
    meta_table = Table(meta_data, colWidths=[155, 175, 157])
    meta_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), card_bg),
        ('BOX', (0,0), (-1,-1), 0.5, border_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(meta_table)
    story.append(Spacer(1, 6))

    story.append(Paragraph("1. EXECUTIVE SUMMARY & VALUE PROPOSITION", h1_style))
    story.append(Paragraph(
        "This proposal defines the commercial implementation for the <b>Venture Visionary CRM & Business Consultancy Platform</b> per developer handover Blueprint v2.0. Operating as a comprehensive operating system rather than a generic contact tool, it unifies multi-channel lead acquisition (Website, Meta Ads, WhatsApp), 193-country visa rules, company formation workflows, dynamic document checklists, QC verification, multi-tenant B2B portals, and financial margin tracking.",
        body_style
    ))
    story.append(Paragraph(
        "<b>Cost Optimization Architecture:</b> By leveraging proven open-source foundations (Laravel Filament multi-panel, Chatwoot unified messaging, n8n automation, and MinIO/S3 private vaults) combined with AI-assisted engineering (Cursor / Claude Code), commodity infrastructure coding is reduced by ~70%. Furthermore, by structuring the build in a steady, incremental developer workflow, development labor cost is reduced by a true <b>50%</b> without sacrificing any blueprint non-negotiable standards.",
        body_style
    ))
    story.append(Spacer(1, 4))

    story.append(Paragraph("2. COMMERCIAL INVESTMENT PACKAGES", h1_style))
    story.append(Paragraph("OPTION A: Pure Development Labor Cost (50% Labor Reduced)", h2_style))
    story.append(Paragraph("<i>Excludes third-party cloud hosting, Meta WhatsApp conversation fees, and SMS gateway charges.</i>", body_style))

    opt_a_data = [
        [
            Paragraph("Milestone Package", table_header_style),
            Paragraph("Core Technical Deliverables (Blueprint v2.0 Scope)", table_header_style),
            Paragraph("Duration", table_header_style),
            Paragraph("Cost (BDT)", table_header_style),
            Paragraph("USD Equiv.", table_header_style)
        ],
        [
            Paragraph("<b>Milestone 1:<br/>Core Operations MVP</b><br/>(Phase 1 + Phase 2)", table_cell_style),
            Paragraph("• <b>Phase 1:</b> Auth (MFA/2FA), Spatie RBAC (12 roles), Lead Engine, Meta/WhatsApp Unified Inbox, Duplicate Detection (QID/Passport), Client 360, CEO KPI Dashboard, Full Audit Logs.<br/>"
                      "• <b>Phase 2:</b> Configurable Service Engine, 193-Country Visa Master DB (Maker-Checker governance), Dynamic JSONB Checklist Snapshot Engine, S3/MinIO Document Vault, QC Lifecycle, SLA Tasks & Escalations.", table_cell_style),
            Paragraph("~2 Months<br/>(Phased)", table_cell_style),
            Paragraph("<b>BDT 85,000 –<br/>BDT 1,12,500</b>", table_cell_bold),
            Paragraph("$700 –<br/>$950", table_cell_style)
        ],
        [
            Paragraph("<b>Milestone 2:<br/>Portals & Finance</b><br/>(Phase 3, 4, 5, 6)", table_cell_style),
            Paragraph("• <b>Phase 3:</b> Client Portal (OTP login, client-safe timeline, document upload slots).<br/>"
                      "• <b>Phase 4:</b> Quotations, Multi-Currency Invoicing, Gov/VAC Margin Split, Refunds.<br/>"
                      "• <b>Phase 5:</b> B2B Partner Portal (tenant isolation, custom net rates, partner wallets).<br/>"
                      "• <b>Phase 6:</b> Advanced Analytics, export audit logs, Scramble OpenAPI documentation.", table_cell_style),
            Paragraph("~3 Months<br/>(Phased)", table_cell_style),
            Paragraph("<b>BDT 80,000 –<br/>BDT 1,20,000</b>", table_cell_bold),
            Paragraph("$650 –<br/>$1,000", table_cell_style)
        ],
        [
            Paragraph("<b>TOTAL FULL PLATFORM<br/>(All 6 Phases Complete)</b>", table_cell_bold),
            Paragraph("<b>Complete 3-Portal Enterprise System</b> matching all 29 Blueprint sections, tested disaster recovery, and developer handover documentation.", table_cell_bold),
            Paragraph("<b>~5 Months<br/>Total</b>", table_cell_bold),
            Paragraph("<b>BDT 1,65,000 –<br/>BDT 2,32,500</b>", table_cell_bold),
            Paragraph("<b>$1,375 –<br/>$1,950</b>", table_cell_bold)
        ]
    ]

    opt_a_table = Table(opt_a_data, colWidths=[105, 197, 55, 80, 50])
    opt_a_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BOX', (0,0), (-1,-1), 0.5, primary_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3.5),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('BACKGROUND', (0,-1), (-1,-1), highlight_bg),
    ]))
    story.append(opt_a_table)

    # Page Break to Page 2
    story.append(PageBreak())

    # ====================================================
    # PAGE 2: OPTION B (TURNKEY) & 4-MONTH OPERATIONAL BUFFER
    # ====================================================
    story.append(Paragraph("OPTION B: Turnkey Cost (Development Labor + 4 Months APIs & Cloud Infrastructure)", h2_style))
    story.append(Paragraph(
        "<i>All-inclusive commercial package bundling pure software development labor with 4 months of pre-funded cloud hosting, secure file vaults, official WhatsApp Business API messaging, and transactional SMS/OTP gateways.</i>",
        body_style
    ))
    story.append(Spacer(1, 2))

    opt_b_data = [
        [
            Paragraph("Turnkey Package Tier", table_header_style),
            Paragraph("Software Scope Included", table_header_style),
            Paragraph("Operational Infrastructure & APIs (4 Months)", table_header_style),
            Paragraph("Total Investment (BDT)", table_header_style),
            Paragraph("USD Equiv.", table_header_style)
        ],
        [
            Paragraph("<b>MVP Turnkey Package</b><br/>(Milestone 1 + 4 Mo. Infra)", table_cell_style),
            Paragraph("• Core Staff CRM & Lead Engine<br/>• Visa Master Database & Governance<br/>• Dynamic Document Checklist Engine<br/>• Document QC Vault & SLA Tracking", table_cell_style),
            Paragraph("• 4 Mo. Dedicated Cloud VPS + Backups<br/>• 4 Mo. Private Object Storage Vault<br/>• WhatsApp Business Cloud API Reserve<br/>• Client/Staff Login SMS/OTP Gateway", table_cell_style),
            Paragraph("<b>BDT 1,35,000 –<br/>BDT 2,16,500</b>", table_cell_bold),
            Paragraph("$1,125 –<br/>$1,800", table_cell_style)
        ],
        [
            Paragraph("<b>Full Enterprise Turnkey</b><br/>(All 6 Phases + 4 Mo. Infra)", table_cell_style),
            Paragraph("• Complete Staff CRM, Client & B2B Portals<br/>• Quotation & Multi-Currency Finance<br/>• B2B Net Rates & Partner Wallets<br/>• Full Trigger Automation & BI Reports", table_cell_style),
            Paragraph("• 4 Mo. High-Spec Cloud VPS (32GB RAM)<br/>• 4 Mo. Encrypted S3/R2 Document Storage<br/>• WhatsApp API Volume Messaging Reserve<br/>• Transactional Email & SMS OTP Gateway", table_cell_style),
            Paragraph("<b>BDT 2,15,000 –<br/>BDT 3,36,500</b>", table_cell_bold),
            Paragraph("$1,800 –<br/>$2,800", table_cell_style)
        ]
    ]

    opt_b_table = Table(opt_b_data, colWidths=[110, 142, 115, 75, 45])
    opt_b_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), secondary_color),
        ('BOX', (0,0), (-1,-1), 0.5, secondary_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('BACKGROUND', (0,1), (-1,1), colors.HexColor("#FFFFFF")),
        ('BACKGROUND', (0,2), (-1,2), card_bg),
    ]))
    story.append(opt_b_table)
    story.append(Spacer(1, 10))

    story.append(Paragraph("3. FOUR-MONTH OPERATIONAL & API OVERHEAD BREAKDOWN", h1_style))
    story.append(Paragraph(
        "For complete financial governance and budget transparency, the table below specifies the itemized operational overhead budgeted for the initial 4-month live production window in Option B:",
        body_style
    ))

    infra_data = [
        [
            Paragraph("Infrastructure / Service Item", table_header_style),
            Paragraph("Technical Specification & Service Provider", table_header_style),
            Paragraph("Monthly Budget (BDT)", table_header_style),
            Paragraph("4-Month Total (BDT)", table_header_style)
        ],
        [
            Paragraph("<b>Cloud Application Server & DB</b>", table_cell_style),
            Paragraph("Hetzner / DigitalOcean Cloud VPS (8 vCPU, 32GB RAM) running Dockerized PostgreSQL, Redis & App containers + Automated Offsite Daily Backups.", table_cell_style),
            Paragraph("BDT 3,500 – BDT 5,000", table_cell_style),
            Paragraph("<b>BDT 14,000 – BDT 20,000</b>", table_cell_bold)
        ],
        [
            Paragraph("<b>Secure Object File Vault</b>", table_cell_style),
            Paragraph("Cloudflare R2 or Self-Hosted MinIO with encrypted bucket storage, zero egress fees, and expiring temporary signed URLs for passports, QIDs & bank records.", table_cell_style),
            Paragraph("BDT 1,500 – BDT 3,000", table_cell_style),
            Paragraph("<b>BDT 6,000 – BDT 12,000</b>", table_cell_bold)
        ],
        [
            Paragraph("<b>Official WhatsApp Cloud API</b>", table_cell_style),
            Paragraph("Meta WhatsApp Business Platform conversation charges for inbound client inquiries, automated lead auto-replies, document QC alerts, and status updates.", table_cell_style),
            Paragraph("BDT 6,000 – BDT 15,000", table_cell_style),
            Paragraph("<b>BDT 24,000 – BDT 60,000</b>", table_cell_bold)
        ],
        [
            Paragraph("<b>Transactional SMS & Portal OTP</b>", table_cell_style),
            Paragraph("Local Qatar / BD SMS Masking Gateway for instant 6-digit login OTPs, urgent case readiness notifications, and interview appointment reminders.", table_cell_style),
            Paragraph("BDT 1,500 – BDT 3,000", table_cell_style),
            Paragraph("<b>BDT 6,000 – BDT 12,000</b>", table_cell_bold)
        ],
        [
            Paragraph("<b>TOTAL 4-MONTH OPERATIONAL BUFFER</b>", table_cell_bold),
            Paragraph("<b>Complete 4-Month Infrastructure & Communications Budget</b>", table_cell_bold),
            Paragraph("<b>BDT 12,500 – BDT 26,000 / mo</b>", table_cell_bold),
            Paragraph("<b>BDT 50,000 – BDT 1,04,000</b>", table_cell_bold)
        ]
    ]

    infra_table = Table(infra_data, colWidths=[130, 182, 85, 90])
    infra_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BOX', (0,0), (-1,-1), 0.5, primary_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('BACKGROUND', (0,-1), (-1,-1), highlight_bg),
    ]))
    story.append(infra_table)

    # Page Break to Page 3
    story.append(PageBreak())

    # ====================================================
    # PAGE 3: PHASE-BY-PHASE TECHNICAL SCOPE MATRIX
    # ====================================================
    story.append(Paragraph("4. PHASE-BY-PHASE TECHNICAL SCOPE MATRIX", h1_style))
    story.append(Paragraph(
        "Implementation strictly follows the 6-phase development roadmap prescribed in Section 25 of Blueprint v2.0. Open-source packages are embedded to replace repetitive boilerplate, freeing developer effort to concentrate strictly on custom visa and consultancy business logic.",
        body_style
    ))
    story.append(Spacer(1, 2))

    phase_data = [
        [
            Paragraph("Phase", table_header_style),
            Paragraph("Blueprint Deliverables", table_header_style),
            Paragraph("Open-Source Component", table_header_style),
            Paragraph("AI Custom Build Scope", table_header_style),
            Paragraph("Reduced Cost (BDT)", table_header_style)
        ],
        [
            Paragraph("<b>Phase 1:<br/>Core CRM & Leads</b>", table_cell_style),
            Paragraph("• Auth (MFA/2FA), 12-Role RBAC<br/>• Lead Capture (Meta Ads/Web)<br/>• Unified WhatsApp Inbox<br/>• Duplicate Detection (Passport/QID)<br/>• Round Robin Assignment, CEO KPI", table_cell_style),
            Paragraph("• Spatie Permission<br/>• Laravel Auditing<br/>• Chatwoot Core<br/>• Filament Dashboard", table_cell_style),
            Paragraph("• Passport/QID duplicate logic<br/>• Lead scoring & routing rules<br/>• Client 360 unified layout<br/>• Activity timeline integration", table_cell_style),
            Paragraph("<b>BDT 70k – 95k</b><br/>(10–12 Days)", table_cell_style)
        ],
        [
            Paragraph("<b>Phase 2:<br/>Service & Visa Ops</b>", table_cell_style),
            Paragraph("• No-code Service Engine<br/>• 193-Country Visa Master DB<br/>• Research Maker-Checker Flow<br/>• Dynamic JSONB Checklists<br/>• Document QC Vault & Expiry Alerts<br/>• Task & SLA Escalation Timers", table_cell_style),
            Paragraph("• Filament Builder/Repeater<br/>• Spatie MediaLibrary<br/>• MinIO / S3 Driver<br/>• Spatie Model States", table_cell_style),
            Paragraph("• Dynamic eligibility rule engine<br/>• Immutable checklist snapshots<br/>• Signed temporary URL vault<br/>• SLA breach trigger jobs", table_cell_style),
            Paragraph("<b>BDT 95k – 130k</b><br/>(15–18 Days)", table_cell_style)
        ],
        [
            Paragraph("<b>Phase 3:<br/>Client Portal</b>", table_cell_style),
            Paragraph("• Secure OTP authentication<br/>• Client-safe case status timeline<br/>• Document upload against request<br/>• Payment status & portal messaging", table_cell_style),
            Paragraph("• Filament /client Panel<br/>• Laravel Notification Hub<br/>• OTP Auth Package", table_cell_style),
            Paragraph("• Client-safe data filtering<br/>• Restricted doc upload slots<br/>• Mobile-responsive client UI<br/>• Status notification push", table_cell_style),
            Paragraph("<b>BDT 35k – 50k</b><br/>(5–7 Days)", table_cell_style)
        ],
        [
            Paragraph("<b>Phase 4:<br/>Finance & Ledger</b>", table_cell_style),
            Paragraph("• Quotations (Gov, VAC & Service fee)<br/>• Invoices & multi-currency snapshots<br/>• Case margin & profitability tracking<br/>• Refund request/approval workflow", table_cell_style),
            Paragraph("• Filament Table Builder<br/>• DomPDF Invoicing<br/>• Currency Conversion", table_cell_style),
            Paragraph("• Gov fee / profit margin split<br/>• Multi-currency historical locks<br/>• Refund authorization chain<br/>• Revenue & expense reports", table_cell_style),
            Paragraph("<b>BDT 45k – 65k</b><br/>(7–9 Days)", table_cell_style)
        ],
        [
            Paragraph("<b>Phase 5:<br/>B2B Partner Portal</b>", table_cell_style),
            Paragraph("• Multi-tenant B2B partner isolation<br/>• Partner client/case submissions<br/>• Partner-specific net rate cards<br/>• Wallet / credit limit ledger system<br/>• Advanced trigger-action automation", table_cell_style),
            Paragraph("• Filament Multi-Tenancy<br/>• bavix/laravel-wallet<br/>• n8n Workflow Engine", table_cell_style),
            Paragraph("• Strict tenant scoping rules<br/>• Net rate historical snapshotting<br/>• Wallet top-up approval queue<br/>• Automated alert webhooks", table_cell_style),
            Paragraph("<b>BDT 55k – 80k</b><br/>(8–10 Days)", table_cell_style)
        ],
        [
            Paragraph("<b>Phase 6:<br/>Analytics & Expansion</b>", table_cell_style),
            Paragraph("• Operations, Sales & B2B reports<br/>• Sensitive data export logging<br/>• Auto-generated OpenAPI / Swagger<br/>• Mobile API readiness", table_cell_style),
            Paragraph("• Metabase / Superset<br/>• Scramble OpenAPI v3", table_cell_style),
            Paragraph("• Read-replica SQL views<br/>• Export audit log middleware<br/>• REST API endpoint hardening", table_cell_style),
            Paragraph("<b>BDT 30k – 45k</b><br/>(5 Days)", table_cell_style)
        ],
        [
            Paragraph("<b>TOTALS</b>", table_cell_bold),
            Paragraph("<b>Full 6-Phase System (Phases 1 to 6)</b>", table_cell_bold),
            Paragraph("<b>Unified Open Stack</b>", table_cell_bold),
            Paragraph("<b>~420 – 550 Engineering Hours</b>", table_cell_bold),
            Paragraph("<b>BDT 1,65,000 – 2,32,500</b>", table_cell_bold)
        ]
    ]

    phase_table = Table(phase_data, colWidths=[65, 125, 95, 115, 87])
    phase_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BOX', (0,0), (-1,-1), 0.5, primary_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 2.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2.5),
        ('LEFTPADDING', (0,0), (-1,-1), 4),
        ('RIGHTPADDING', (0,0), (-1,-1), 4),
        ('BACKGROUND', (0,-1), (-1,-1), highlight_bg),
    ]))
    story.append(phase_table)

    # Page Break to Page 4
    story.append(PageBreak())

    # ====================================================
    # PAGE 4: GUARANTEES, MILESTONES & SIGN-OFF
    # ====================================================
    story.append(Paragraph("5. BLUEPRINT COMPLIANCE & NON-NEGOTIABLE GUARANTEES", h1_style))
    story.append(Paragraph(
        "The platform architecture strictly guarantees compliance with all 15 Developer Non-Negotiable Rules stated in Section 26 of Blueprint v2.0:",
        body_style
    ))
    story.append(Spacer(1, 2))

    guarantee_data = [
        [
            Paragraph("Blueprint Rule (Sec 26)", table_header_style),
            Paragraph("Architectural Implementation in Delivered Platform", table_header_style)
        ],
        [
            Paragraph("<b>Rules 1 & 2: Zero Hard-Coded Logic & Admin Workflows</b>", table_cell_style),
            Paragraph("All 193 country visa rules, dynamic document checklists, SLAs, and approval workflows are stored as configurable JSON schemas in PostgreSQL. Admins configure rules via UI with zero developer code edits.", table_cell_style)
        ],
        [
            Paragraph("<b>Rule 3: Full Action Auditability</b>", table_cell_style),
            Paragraph("Global ORM auditing automatically records actor ID, timestamp, IP address, user agent, old value, and new value for every record creation, update, approval, and soft deletion.", table_cell_style)
        ],
        [
            Paragraph("<b>Rules 4 & 5: Client ≠ Case Separation</b>", table_cell_style),
            Paragraph("Strict relational model where one Client profile supports multiple historic or concurrent Cases, each independently holding documents, tasks, appointments, payments, and timeline logs.", table_cell_style)
        ],
        [
            Paragraph("<b>Rules 6, 7 & 8: Strict Tenant & Role Isolation</b>", table_cell_style),
            Paragraph("Field-level RBAC protects sensitive QID/passports. Client Portal isolates client-safe views (hiding internal QC notes). B2B Partner Portal enforces strict multi-tenant database row-level security (RLS).", table_cell_style)
        ],
        [
            Paragraph("<b>Rule 9: Documented APIs & No Direct DB Writes</b>", table_cell_style),
            Paragraph("Third-party channels (Meta, WhatsApp, Webforms) communicate strictly through documented REST endpoints and signed idempotent webhooks. OpenAPI/Swagger v3 docs are automatically maintained.", table_cell_style)
        ],
        [
            Paragraph("<b>Rule 11: Historical Snapshot Integrity</b>", table_cell_style),
            Paragraph("When a case is initialized or quotation approved, an immutable snapshot of active pricing, document checklists, and visa source rules is permanently frozen in JSONB columns.", table_cell_style)
        ],
        [
            Paragraph("<b>Rule 13: Automated Backups & Disaster Recovery</b>", table_cell_style),
            Paragraph("Automated daily snapshots of the PostgreSQL database and private file buckets to offsite cloud storage, backed by documented RTO/RPO targets and verified restore scripts.", table_cell_style)
        ]
    ]

    guarantee_table = Table(guarantee_data, colWidths=[165, 322])
    guarantee_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), secondary_color),
        ('BOX', (0,0), (-1,-1), 0.5, secondary_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 2.8),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2.8),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(guarantee_table)
    story.append(Spacer(1, 8))

    story.append(Paragraph("6. PAYMENT MILESTONES & GOVERNANCE", h1_style))
    story.append(Paragraph(
        "Project billing is tied directly to verified milestone deliverables per Section 27 (QA Requirements) and Section 29 (Handover Checklist):",
        body_style
    ))
    story.append(Spacer(1, 2))

    milestone_data = [
        [
            Paragraph("Payment Stage", table_header_style),
            Paragraph("Trigger Milestone & Deliverables", table_header_style),
            Paragraph("Share (%)", table_header_style),
            Paragraph("Amount (BDT)", table_header_style)
        ],
        [
            Paragraph("<b>Milestone 1:<br/>Project Kickoff</b>", table_cell_style),
            Paragraph("Signing of agreement, system architecture signoff, PostgreSQL migration schema approval, and Docker development environment deployment.", table_cell_style),
            Paragraph("<b>25%</b>", table_cell_style),
            Paragraph("BDT 41,250 – BDT 58,125", table_cell_style)
        ],
        [
            Paragraph("<b>Milestone 2:<br/>Core MVP Delivery</b>", table_cell_style),
            Paragraph("Delivery of <b>Phase 1 & Phase 2</b>: Working Staff CRM, Lead Engine with WhatsApp/Meta inbox, Visa Master DB, Dynamic Checklists, Document Vault, and UAT approval.", table_cell_style),
            Paragraph("<b>35%</b>", table_cell_style),
            Paragraph("BDT 57,750 – BDT 81,375", table_cell_style)
        ],
        [
            Paragraph("<b>Milestone 3:<br/>Portals & Finance</b>", table_cell_style),
            Paragraph("Delivery of <b>Phase 3, 4 & 5</b>: Client Self-Service Portal, Quotations & Finance ledger, Multi-tenant B2B Partner Portal with wallet and net rates.", table_cell_style),
            Paragraph("<b>25%</b>", table_cell_style),
            Paragraph("BDT 41,250 – BDT 58,125", table_cell_style)
        ],
        [
            Paragraph("<b>Milestone 4:<br/>Final Handover</b>", table_cell_style),
            Paragraph("Completion of Security/Role QA, Backup/Restore test, Swagger API documentation, Admin & User Guides, and Production Server deployment.", table_cell_style),
            Paragraph("<b>15%</b>", table_cell_style),
            Paragraph("BDT 24,750 – BDT 34,875", table_cell_style)
        ],
        [
            Paragraph("<b>TOTAL</b>", table_cell_bold),
            Paragraph("<b>Full Turnkey Software Delivery (Option A Pure Development)</b>", table_cell_bold),
            Paragraph("<b>100%</b>", table_cell_bold),
            Paragraph("<b>BDT 1,65,000 – BDT 2,32,500</b>", table_cell_bold)
        ]
    ]

    milestone_table = Table(milestone_data, colWidths=[95, 232, 60, 100])
    milestone_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BOX', (0,0), (-1,-1), 0.5, primary_color),
        ('INNERGRID', (0,0), (-1,-1), 0.5, border_color),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('BACKGROUND', (0,-1), (-1,-1), highlight_bg),
    ]))
    story.append(milestone_table)
    story.append(Spacer(1, 10))

    # Sign-off Block
    sign_data = [
        [
            Paragraph("<b>Prepared By:</b>", table_cell_bold),
            Paragraph("<b>Accepted & Approved By:</b>", table_cell_bold)
        ],
        [
            Paragraph("Lead Systems Architect & Development Team<br/>Antigravity Engineering Solutions", table_cell_style),
            Paragraph("Executive Management / Authorized Signatory<br/>Venture Visionary Visa & Business Consultancy, Qatar", table_cell_style)
        ],
        [
            Paragraph("<br/>Signature: __________________________<br/>Date: ______________________________", table_cell_style),
            Paragraph("<br/>Signature: __________________________<br/>Date: ______________________________", table_cell_style)
        ]
    ]
    sign_table = Table(sign_data, colWidths=[240, 247])
    sign_table.setStyle(TableStyle([
        ('BOX', (0,0), (-1,-1), 0.5, border_color),
        ('BACKGROUND', (0,0), (-1,-1), card_bg),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(sign_table)

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Refined PDF built successfully at {filename}")

if __name__ == '__main__':
    build_pdf()
