<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venture Visionary | Global Visa Facilitation & Corporate Advisory Services</title>
    <meta name="description" content="Official lead generation and visa consultation portal for Venture Visionary Business Consultancy. Apply for UK, Schengen, USA, Canada visas and Qatar corporate formation.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #070d1e;
            --bg-card: rgba(15, 23, 42, 0.75);
            --bg-card-hover: rgba(30, 41, 59, 0.85);
            --border-glass: rgba(255, 255, 255, 0.1);
            --border-focus: #3b82f6;
            --primary: #2563eb;
            --primary-glow: rgba(37, 99, 235, 0.35);
            --accent-emerald: #10b981;
            --accent-emerald-glow: rgba(16, 185, 129, 0.25);
            --accent-gold: #f59e0b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
            --font-display: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(at 15% 15%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 85% 20%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(245, 158, 11, 0.08) 0px, transparent 50%);
            background-attachment: fixed;
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        /* Top Navigation Header */
        header.site-header {
            background: rgba(7, 13, 30, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-glass);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 1rem 1.5rem;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, #1e3a8a, #0f2942);
            border: 1px solid rgba(245, 158, 11, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
            color: var(--accent-gold);
            font-size: 1.35rem;
            font-weight: 800;
            font-family: var(--font-display);
        }

        .brand-text h1 {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--text-muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pill-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.12);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.25);
        }

        .pill-badge .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-glow 2s infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .portal-nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-portal-btn {
            padding: 0.45rem 0.9rem;
            border-radius: var(--radius-sm);
            font-size: 0.825rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--text-muted);
            border: 1px solid var(--border-glass);
            background: rgba(255, 255, 255, 0.03);
            transition: var(--transition);
        }

        .nav-portal-btn:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }

        .nav-portal-btn.primary {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .nav-portal-btn.primary:hover {
            background: #1d4ed8;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        /* Hero Banner */
        .hero-section {
            padding: 3rem 1.5rem 2rem;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 1rem;
            border-radius: 9999px;
            background: rgba(37, 99, 235, 0.12);
            color: #60a5fa;
            border: 1px solid rgba(96, 165, 250, 0.25);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 1.25rem;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 1rem;
            background: linear-gradient(180deg, #ffffff 30%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 720px;
            margin: 0 auto 1.75rem;
        }

        .trust-metrics {
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            flex-wrap: wrap;
            padding-top: 0.5rem;
        }

        .metric-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .metric-item .icon {
            color: var(--accent-gold);
            font-size: 1rem;
        }

        /* Main Workspace Container */
        main.main-content {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto 4rem;
            padding: 0 1.5rem;
        }

        /* Nav Tabs */
        .tab-switcher {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: rgba(15, 23, 42, 0.6);
            padding: 0.4rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-glass);
            max-width: 620px;
            margin-left: auto;
            margin-right: auto;
        }

        .tab-btn {
            flex: 1;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-family: var(--font-display);
            font-weight: 600;
            font-size: 0.925rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .tab-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.04);
        }

        .tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Lead Generation Form Layout */
        .lead-grid {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 2rem;
            align-items: start;
        }

        @media (max-width: 992px) {
            .lead-grid {
                grid-template-columns: 1fr;
            }
        }

        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 2.25rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.6), transparent);
        }

        .card-header-box {
            margin-bottom: 2rem;
        }

        .card-header-box h2 {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.4rem;
        }

        .card-header-box p {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .form-section-title {
            font-family: var(--font-display);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent-gold);
            margin: 1.75rem 0 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title:first-of-type {
            margin-top: 0;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label.field-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        label.field-label span.req {
            color: #f87171;
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: var(--text-dim);
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        input.form-control,
        select.form-control,
        textarea.form-control {
            width: 100%;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-sm);
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            color: #ffffff;
            font-family: var(--font-body);
            font-size: 0.925rem;
            transition: var(--transition);
            outline: none;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
            cursor: pointer;
        }

        select.form-control option {
            background-color: #0f172a;
            color: #ffffff;
        }

        textarea.form-control {
            padding: 0.8rem 1rem;
            min-height: 90px;
            resize: vertical;
        }

        input.form-control:focus,
        select.form-control:focus,
        textarea.form-control:focus {
            border-color: var(--border-focus);
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .input-phone-group {
            display: grid;
            grid-template-columns: 140px 1fr;
            gap: 0.5rem;
        }

        .input-phone-group select {
            padding-left: 0.75rem;
        }

        .input-phone-group input {
            padding-left: 1rem;
        }

        .sync-checkbox-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.35rem;
            font-size: 0.8rem;
            color: var(--text-muted);
            cursor: pointer;
            user-select: none;
        }

        .sync-checkbox-box input[type="checkbox"] {
            accent-color: var(--primary);
            cursor: pointer;
        }

        /* Preset buttons for date */
        .preset-pills {
            display: flex;
            gap: 0.4rem;
            margin-top: 0.35rem;
            flex-wrap: wrap;
        }

        .preset-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            color: var(--text-muted);
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .preset-btn:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.1);
        }

        .submit-btn-box {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-glass);
        }

        .btn-submit {
            width: 100%;
            padding: 1rem 1.75rem;
            border-radius: var(--radius-sm);
            border: none;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-family: var(--font-display);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 18px var(--primary-glow);
        }

        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 6px 24px rgba(37, 99, 235, 0.5);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        /* Sidebar Info Card */
        .sidebar-panel {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .preview-box {
            background: rgba(15, 23, 42, 0.7);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            position: relative;
        }

        .preview-title {
            font-family: var(--font-display);
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .preview-destination-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.85rem;
            background: rgba(37, 99, 235, 0.15);
            color: #93c5fd;
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            width: 100%;
        }

        .preview-details-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .preview-details-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .preview-details-list li .check-icon {
            color: var(--accent-emerald);
            font-size: 0.95rem;
            margin-top: 2px;
        }

        .advisory-guarantee-card {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(6, 78, 59, 0.15));
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: var(--radius-md);
            padding: 1.25rem;
        }

        .advisory-guarantee-card h4 {
            font-family: var(--font-display);
            color: #34d399;
            font-size: 0.925rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .advisory-guarantee-card p {
            font-size: 0.8rem;
            color: #cbd5e1;
            line-height: 1.5;
        }

        /* Lead Lookup / Tracking Tab */
        .lookup-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .lookup-search-box {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .lookup-search-box input {
            padding-left: 1rem;
        }

        .lookup-search-btn {
            padding: 0 1.75rem;
            background: var(--primary);
            border: none;
            border-radius: var(--radius-sm);
            color: #ffffff;
            font-family: var(--font-display);
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            white-space: nowrap;
        }

        .lookup-search-btn:hover {
            background: #1d4ed8;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .lookup-result-card {
            display: none;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
            padding: 2rem;
            animation: fadeIn 0.3s ease;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-glass);
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .result-lead-id {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-badge.new {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-badge.contacted {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-badge.qualified,
        .status-badge.converted {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .result-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        @media (max-width: 640px) {
            .result-grid {
                grid-template-columns: 1fr;
            }
        }

        .result-item {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .result-item-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-dim);
            font-weight: 600;
        }

        .result-item-value {
            font-size: 0.95rem;
            color: #ffffff;
            font-weight: 600;
        }

        /* Tab 3: Live Feed & API Hub */
        .api-hub-container {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .hub-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 1.25rem 1.75rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .hub-banner-text h3 {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
        }

        .hub-banner-text p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .btn-refresh-feed {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid var(--border-glass);
            color: #ffffff;
            padding: 0.55rem 1.15rem;
            border-radius: var(--radius-sm);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-refresh-feed:hover {
            background: rgba(255, 255, 255, 0.14);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .feed-table-container {
            overflow-x: auto;
            background: var(--bg-card);
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-lg);
        }

        table.feed-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.875rem;
        }

        table.feed-table th {
            background: rgba(15, 23, 42, 0.95);
            padding: 1rem 1.25rem;
            font-family: var(--font-display);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-glass);
        }

        table.feed-table td {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: #e2e8f0;
        }

        table.feed-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .badge-channel {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
        }

        .badge-channel.website { background: rgba(59, 130, 246, 0.15); color: #93c5fd; }
        .badge-channel.whatsapp { background: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
        .badge-channel.meta_ads { background: rgba(168, 85, 247, 0.15); color: #d8b4fe; }

        /* Code Block */
        .code-box {
            background: #090e1c;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.825rem;
            color: #cbd5e1;
            overflow-x: auto;
            position: relative;
        }

        .code-box .badge-method {
            color: #10b981;
            font-weight: 700;
        }

        /* Success Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
            animation: modalFade 0.25s ease;
        }

        @keyframes modalFade {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-card {
            background: #0f172a;
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: var(--radius-lg);
            max-width: 520px;
            width: 100%;
            padding: 2.5rem;
            text-align: center;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        }

        .modal-icon-box {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid var(--accent-emerald);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: var(--accent-emerald);
            font-size: 2.25rem;
            animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes bounceIn {
            0% { transform: scale(0.4); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .modal-title {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .modal-lead-number-box {
            background: rgba(37, 99, 235, 0.12);
            border: 1px dashed rgba(59, 130, 246, 0.5);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-lead-number {
            font-family: var(--font-display);
            font-size: 1.4rem;
            font-weight: 800;
            color: #93c5fd;
            letter-spacing: 0.05em;
        }

        .btn-copy {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-glass);
            color: #ffffff;
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-copy:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-officer-info {
            background: rgba(255, 255, 255, 0.03);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
            text-align: left;
        }

        .modal-officer-info strong {
            color: #ffffff;
        }

        .modal-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .btn-whatsapp-chat {
            background: #10b981;
            color: #ffffff;
            font-family: var(--font-display);
            font-size: 0.95rem;
            font-weight: 700;
            padding: 0.85rem 1.25rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-whatsapp-chat:hover {
            background: #059669;
            box-shadow: 0 0 20px var(--accent-emerald-glow);
        }

        .btn-modal-dismiss {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-glass);
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-modal-dismiss:hover {
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Footer */
        footer.site-footer {
            background: rgba(7, 13, 30, 0.95);
            border-top: 1px solid var(--border-glass);
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.85rem;
            margin-top: auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: #ffffff;
        }

        /* Notifications / Toast */
        .toast-msg {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #1e293b;
            color: #ffffff;
            border: 1px solid var(--border-glass);
            border-radius: var(--radius-md);
            padding: 0.9rem 1.4rem;
            font-size: 0.875rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            gap: 0.75rem;
            z-index: 2000;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="site-header">
        <div class="header-container">
            <a href="/" class="brand-box">
                <div class="brand-logo-icon">VV</div>
                <div class="brand-text">
                    <h1>Venture Visionary</h1>
                    <p>Global Visa & Corporate Consultancy</p>
                </div>
            </a>

            <div class="header-right">
                <div class="pill-badge">
                    <span class="pulse-dot"></span>
                    <span>Doha HQ • Desk Active</span>
                </div>

                <div class="portal-nav-links">
                    <a href="/staff" class="nav-portal-btn">Staff CRM</a>
                    <a href="/partner" class="nav-portal-btn">B2B Partner</a>
                    <a href="/portal" class="nav-portal-btn">Client Portal</a>
                    <a href="/docs/api" class="nav-portal-btn" target="_blank">API Docs</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-tag">
            <span>✨ Official Inbound Lead & Consultation Desk</span>
        </div>
        <h2 class="hero-title">Global Visa Facilitation & Corporate Structuring</h2>
        <p class="hero-desc">
            Fast, confidential immigration and company formation services in Doha. Complete the consultation request below to be automatically assigned a dedicated senior visa officer.
        </p>

        <div class="trust-metrics">
            <div class="metric-item">
                <span class="icon">⚡</span>
                <span>Average Assignment: <strong>&lt; 5 Minutes</strong></span>
            </div>
            <div class="metric-item">
                <span class="icon">🛡️</span>
                <span>Compliant with <strong>Qatar MOCI & Visa Rules</strong></span>
            </div>
            <div class="metric-item">
                <span class="icon">🌍</span>
                <span>Access to <strong>193 Destination Checklists</strong></span>
            </div>
            <div class="metric-item">
                <span class="icon">📱</span>
                <span>Direct <strong>WhatsApp & SMS Notifications</strong></span>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <main class="main-content">

        <!-- Navigation Tabs -->
        <div class="tab-switcher">
            <button class="tab-btn active" onclick="switchTab('inquiry-tab', this)">
                <span>📝 Request Consultation</span>
            </button>
            <button class="tab-btn" onclick="switchTab('lookup-tab', this)">
                <span>🔍 Track Lead Status</span>
            </button>
            <button class="tab-btn" onclick="switchTab('feed-tab', this)">
                <span>⚡ Live Feed & API Hub</span>
            </button>
        </div>

        <!-- TAB 1: Lead Generation Form -->
        <div id="inquiry-tab" class="tab-content active">
            <div class="lead-grid">
                
                <!-- Inbound Form -->
                <div class="glass-card">
                    <div class="card-header-box">
                        <h2>Client Consultation Inquiry</h2>
                        <p>Fill in your details to generate your official inquiry reference number.</p>
                    </div>

                    <form id="leadGenerationForm" onsubmit="handleLeadSubmit(event)">
                        
                        <!-- Contact Details -->
                        <div class="form-section-title">
                            <span>1. Personal & Contact Information</span>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="field-label" for="client_name">Full Legal Name <span class="req">*</span></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">👤</span>
                                    <input type="text" id="client_name" name="name" class="form-control" placeholder="e.g. Sheikh Khalid Al-Kuwari" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="field-label" for="phone_number">Phone Number <span class="req">*</span></label>
                                <div class="input-phone-group">
                                    <select id="phone_code" class="form-control" onchange="syncPhoneCode()">
                                        <option value="+974" selected>🇶🇦 +974 (QA)</option>
                                        <option value="+880">🇧🇩 +880 (BD)</option>
                                        <option value="+44">🇬🇧 +44 (UK)</option>
                                        <option value="+1">🇺🇸 +1 (US)</option>
                                        <option value="+971">🇦🇪 +971 (AE)</option>
                                        <option value="+966">🇸🇦 +966 (SA)</option>
                                        <option value="+90">🇹🇷 +90 (TR)</option>
                                        <option value="+91">🇮🇳 +91 (IN)</option>
                                    </select>
                                    <input type="tel" id="phone_number" class="form-control" placeholder="5511 2233" required oninput="autoSyncWhatsApp()">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="field-label" for="whatsapp_number">WhatsApp Number</label>
                                <div class="input-wrapper">
                                    <span class="input-icon">💬</span>
                                    <input type="tel" id="whatsapp_number" name="whatsapp" class="form-control" placeholder="+97455112233">
                                </div>
                                <label class="sync-checkbox-box">
                                    <input type="checkbox" id="sync_wa" checked onchange="toggleWhatsAppSync()">
                                    <span>Same as Phone Number</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="field-label" for="client_email">Email Address</label>
                                <div class="input-wrapper">
                                    <span class="input-icon">✉️</span>
                                    <input type="email" id="client_email" name="email" class="form-control" placeholder="client@example.qa">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="field-label" for="client_nationality">Current Nationality</label>
                                <div class="input-wrapper">
                                    <span class="input-icon">🏳️</span>
                                    <input type="text" id="client_nationality" name="nationality" class="form-control" placeholder="e.g. Qatari, Bangladeshi, British">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="field-label" for="qatar_residence_status">Qatar Residence Status</label>
                                <select id="qatar_residence_status" name="qatar_residence_status" class="form-control">
                                    <option value="citizen">Qatari Citizen (National Passport)</option>
                                    <option value="resident" selected>Qatar Resident (Valid QID Holder)</option>
                                    <option value="visit_visa">Tourist / On Arrival Visit Visa in Qatar</option>
                                    <option value="outside_qatar">Applying from Abroad (GCC / Overseas)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Service Details -->
                        <div class="form-section-title">
                            <span>2. Visa & Destination Requirements</span>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="field-label" for="destination_country">Destination Country <span class="req">*</span></label>
                                <select id="destination_country" name="destination_country" class="form-control" required onchange="handleDestinationChange()">
                                    <option value="">-- Choose Target Country --</option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($country->name); ?>" data-vac="<?php echo e($country->official_vac_name ?? 'Official Embassy / VAC in Doha'); ?>">
                                            <?php echo e($country->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <option value="Schengen Multi-Country">Schengen Multi-Country</option>
                                    <option value="Other">Other Global Destination</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="field-label" for="visa_or_service_type">Requested Visa / Service <span class="req">*</span></label>
                                <select id="visa_or_service_type" name="visa_or_service_type" class="form-control" required>
                                    <option value="Tourist Visa" selected>Tourist / Leisure Visa</option>
                                    <option value="Business / Commercial Visa">Business & Investor Visa</option>
                                    <option value="Student Visa">Student / University Entry Visa</option>
                                    <option value="Work Permit / Relocation">Work Permit & Employment Visa</option>
                                    <option value="Qatar 100% LLC Formation">Qatar 100% Foreign Ownership LLC</option>
                                    <option value="Document Attestation / MOFA">Document Legalization / MOFA</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="field-label" for="planned_travel_date">Intended Travel Date</label>
                                <input type="date" id="planned_travel_date" name="planned_travel_date" class="form-control">
                                <div class="preset-pills">
                                    <button type="button" class="preset-btn" onclick="setDatePreset(30)">Next 30 Days</button>
                                    <button type="button" class="preset-btn" onclick="setDatePreset(60)">Next 60 Days</button>
                                    <button type="button" class="preset-btn" onclick="setDatePreset(90)">Next 3 Months</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="field-label" for="passport_no">Passport Number <small style="color:var(--text-dim)">(Optional)</small></label>
                                <div class="input-wrapper">
                                    <span class="input-icon">📘</span>
                                    <input type="text" id="passport_no" name="passport_no" class="form-control" placeholder="e.g. A12345678">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group full-width">
                                <label class="field-label" for="case_notes">Special Requirements or Notes</label>
                                <textarea id="case_notes" name="notes" class="form-control" placeholder="Mention if you require expedited appointment booking, family grouping, flight/hotel reservations, or translated documents..."></textarea>
                            </div>
                        </div>

                        <input type="hidden" name="source_code" value="website">
                        <input type="hidden" name="campaign" value="Official Lead Generation Portal">

                        <div class="submit-btn-box">
                            <button type="submit" id="submitBtn" class="btn-submit">
                                <span id="submitSpinner" style="display:none">⏳</span>
                                <span>Submit Consultation Request &amp; Generate Lead</span>
                                <span>→</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Advisory Sidebar -->
                <div class="sidebar-panel">
                    
                    <!-- Destination Preview Card -->
                    <div class="preview-box">
                        <div class="preview-title">
                            <span>📋 Application Snapshot</span>
                        </div>
                        
                        <div class="preview-destination-badge" id="previewDestination">
                            <span>🌍 Destination: Select a country</span>
                        </div>

                        <ul class="preview-details-list">
                            <li>
                                <span class="check-icon">✓</span>
                                <span><strong>VAC Center:</strong> <span id="previewVAC">VFS Global / TLS / Embassy</span></span>
                            </li>
                            <li>
                                <span class="check-icon">✓</span>
                                <span><strong>Average Turnaround:</strong> 10 to 15 Business Days</span>
                            </li>
                            <li>
                                <span class="check-icon">✓</span>
                                <span><strong>Basic Dossier:</strong> Passport (6mo+), QID copy, 3mo bank statement, NOC letter</span>
                            </li>
                            <li>
                                <span class="check-icon">✓</span>
                                <span><strong>Round-Robin Officer:</strong> Automatic assignment to Senior Consultant</span>
                            </li>
                        </ul>

                        <div class="advisory-guarantee-card">
                            <h4>🛡️ Venture Visionary Promise</h4>
                            <p>
                                100% audit-compliant filing. We pre-vet all documents before submission to minimize visa appointment rejections.
                            </p>
                        </div>
                    </div>

                    <!-- Quick Support Contact -->
                    <div class="preview-box" style="text-align: center;">
                        <h4 style="font-family: var(--font-display); margin-bottom: 0.5rem; color:#ffffff;">Need Urgent Visa Filing?</h4>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                            Direct emergency desk in West Bay, Doha. Speak to our on-duty immigration officer.
                        </p>
                        <a href="https://wa.me/97455112233" target="_blank" class="btn-whatsapp-chat">
                            <span>💬 Chat on WhatsApp (+974 5511 2233)</span>
                        </a>
                    </div>

                </div>

            </div>
        </div>

        <!-- TAB 2: Lead Tracking & Lookup -->
        <div id="lookup-tab" class="tab-content">
            <div class="lookup-container">
                <div class="glass-card" style="margin-bottom: 2rem;">
                    <div class="card-header-box" style="text-align: center;">
                        <h2>Track Existing Consultation Lead</h2>
                        <p>Enter your Lead Reference Number or registered phone number to fetch real-time status.</p>
                    </div>

                    <form onsubmit="handleLeadLookup(event)">
                        <div class="lookup-search-box">
                            <input type="text" id="lookupQuery" class="form-control" placeholder="e.g. LD-2026-0001 or +97455112233" required>
                            <button type="submit" id="lookupBtn" class="lookup-search-btn">
                                <span>Fetch Status 🔍</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Result Card -->
                <div id="lookupResultCard" class="lookup-result-card">
                    <div class="result-header">
                        <div class="result-lead-id">
                            <span>📄</span>
                            <span id="resLeadNumber">LD-2026-0001</span>
                        </div>
                        <span id="resStatusBadge" class="status-badge new">New Inquiry</span>
                    </div>

                    <div class="result-grid">
                        <div class="result-item">
                            <span class="result-item-label">Client Name</span>
                            <span class="result-item-value" id="resClientName">-</span>
                        </div>
                        <div class="result-item">
                            <span class="result-item-label">Destination</span>
                            <span class="result-item-value" id="resDestination">-</span>
                        </div>
                        <div class="result-item">
                            <span class="result-item-label">Service Type</span>
                            <span class="result-item-value" id="resServiceType">-</span>
                        </div>
                        <div class="result-item">
                            <span class="result-item-label">Assigned Consultant</span>
                            <span class="result-item-value" id="resConsultant" style="color:#60a5fa">-</span>
                        </div>
                        <div class="result-item">
                            <span class="result-item-label">Lead Source</span>
                            <span class="result-item-value" id="resSource">-</span>
                        </div>
                        <div class="result-item">
                            <span class="result-item-label">Submitted On</span>
                            <span class="result-item-value" id="resCreatedAt">-</span>
                        </div>
                    </div>

                    <div style="display:flex; justify-content: flex-end; gap:0.75rem;">
                        <a id="resWhatsAppBtn" href="#" target="_blank" class="btn-whatsapp-chat" style="padding:0.6rem 1rem; font-size:0.85rem;">
                            <span>💬 Message Assigned Officer</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Live Inbound Leads Feed & API Ingestion Hub -->
        <div id="feed-tab" class="tab-content">
            <div class="api-hub-container">
                
                <div class="hub-banner">
                    <div class="hub-banner-text">
                        <h3>Inbound Lead Ingestion &amp; Fetch Monitor</h3>
                        <p>Real-time feed showing where Venture Visionary CRM fetches inbound leads from.</p>
                    </div>
                    <button class="btn-refresh-feed" onclick="fetchRecentLeads()">
                        <span id="refreshIcon">🔄</span>
                        <span>Fetch Latest Leads from CRM</span>
                    </button>
                </div>

                <div class="feed-table-container">
                    <table class="feed-table">
                        <thead>
                            <tr>
                                <th>Lead Number</th>
                                <th>Client Name</th>
                                <th>Channel / Source</th>
                                <th>Destination &amp; Service</th>
                                <th>Assigned Agent</th>
                                <th>Status</th>
                                <th>Received</th>
                            </tr>
                        </thead>
                        <tbody id="feedTableBody">
                            <tr>
                                <td colspan="7" style="text-align:center; padding: 2.5rem; color: var(--text-dim);">
                                    Click "Fetch Latest Leads" or wait for automatic stream...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- API Integration Guide for External Lead Generation -->
                <div class="glass-card">
                    <div class="card-header-box">
                        <h2>Developer &amp; Webhook Integration</h2>
                        <p>How external marketing landing pages, Meta Lead Ads, and website forms push leads to this CRM.</p>
                    </div>

                    <div class="code-box">
                        <span class="badge-method">POST</span> /api/v1/leads<br>
                        <strong>Content-Type:</strong> application/json<br><br>
                        {<br>
                        &nbsp;&nbsp;"name": "Sheikh Nasser Al-Khelaifi",<br>
                        &nbsp;&nbsp;"phone": "+97455112233",<br>
                        &nbsp;&nbsp;"whatsapp": "+97455112233",<br>
                        &nbsp;&nbsp;"email": "nasser@example.qa",<br>
                        &nbsp;&nbsp;"nationality": "Qatari",<br>
                        &nbsp;&nbsp;"destination_country": "United Kingdom",<br>
                        &nbsp;&nbsp;"visa_or_service_type": "Tourist Visa",<br>
                        &nbsp;&nbsp;"planned_travel_date": "2026-12-15",<br>
                        &nbsp;&nbsp;"source_code": "website",<br>
                        &nbsp;&nbsp;"campaign": "Autumn 2026 UK Visa Campaign"<br>
                        }
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Success Modal -->
    <div id="successModal" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-icon-box">✓</div>
            <h3 class="modal-title">Consultation Registered!</h3>
            <p style="color: var(--text-muted); font-size: 0.95rem;">
                Your inquiry has been successfully ingested into Venture Visionary CRM.
            </p>

            <div class="modal-lead-number-box">
                <span id="modalLeadNum" class="modal-lead-number">LD-2026-0000</span>
                <button type="button" class="btn-copy" onclick="copyLeadNumber()">Copy Ref</button>
            </div>

            <div class="modal-officer-info">
                <div>🎯 <strong>Assigned Officer:</strong> <span id="modalAssignedOfficer">Senior Consultant</span></div>
                <div style="margin-top: 0.35rem;">⚡ <strong>Status:</strong> New Case Dossier Opened</div>
            </div>

            <div class="modal-actions">
                <a id="modalWhatsAppBtn" href="#" target="_blank" class="btn-whatsapp-chat">
                    <span>💬 Chat Directly on WhatsApp with Your Officer</span>
                </a>
                <button type="button" class="btn-modal-dismiss" onclick="closeSuccessModal()">
                    Close &amp; Submit Another Inquiry
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Alert -->
    <div id="toastMsg" class="toast-msg">
        <span id="toastIcon">ℹ️</span>
        <span id="toastText">Notification text</span>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-links">
            <a href="/">Platform Home</a>
            <a href="/lead-generation">Lead Generation Portal</a>
            <a href="/staff">Staff CRM Portal</a>
            <a href="/partner">B2B Partner Portal</a>
            <a href="/portal">Client Self-Service Portal</a>
            <a href="/docs/api">OpenAPI Documentation</a>
        </div>
        <p>© 2026 Venture Visionary Business Consultancy LLC. West Bay, Doha, State of Qatar. All rights reserved.</p>
    </footer>

    <!-- Interactive Script -->
    <script>
        // Tab Switcher
        function switchTab(tabId, btn) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            btn.classList.add('active');

            if (tabId === 'feed-tab') {
                fetchRecentLeads();
            }
        }

        // Auto Sync Phone and WhatsApp
        function autoSyncWhatsApp() {
            if (document.getElementById('sync_wa').checked) {
                const code = document.getElementById('phone_code').value;
                const phone = document.getElementById('phone_number').value.trim();
                document.getElementById('whatsapp_number').value = phone ? (code + phone) : '';
            }
        }

        function syncPhoneCode() {
            autoSyncWhatsApp();
        }

        function toggleWhatsAppSync() {
            const isChecked = document.getElementById('sync_wa').checked;
            const waInput = document.getElementById('whatsapp_number');
            if (isChecked) {
                autoSyncWhatsApp();
            } else {
                waInput.focus();
            }
        }

        // Set Travel Date Presets
        function setDatePreset(daysAhead) {
            const target = new Date();
            target.setDate(target.getDate() + daysAhead);
            const dateStr = target.toISOString().split('T')[0];
            document.getElementById('planned_travel_date').value = dateStr;
        }

        // Destination Change Handler
        function handleDestinationChange() {
            const select = document.getElementById('destination_country');
            const selectedOpt = select.options[select.selectedIndex];
            const countryName = select.value;
            const vac = selectedOpt.getAttribute('data-vac') || 'Official Embassy / VAC in Doha';

            const previewDest = document.getElementById('previewDestination');
            const previewVAC = document.getElementById('previewVAC');

            if (countryName) {
                previewDest.innerHTML = `🌍 Destination: <strong>${countryName}</strong>`;
                previewVAC.textContent = vac;
            } else {
                previewDest.innerHTML = '🌍 Destination: Select a country';
                previewVAC.textContent = 'VFS Global / TLS / Embassy';
            }
        }

        // Submit Lead Form via AJAX
        async function handleLeadSubmit(event) {
            event.preventDefault();
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('submitSpinner');

            const phoneCode = document.getElementById('phone_code').value;
            const rawPhone = document.getElementById('phone_number').value.trim();
            const fullPhone = rawPhone.startsWith('+') ? rawPhone : (phoneCode + rawPhone);

            const payload = {
                name: document.getElementById('client_name').value.trim(),
                phone: fullPhone,
                whatsapp: document.getElementById('whatsapp_number').value.trim() || fullPhone,
                email: document.getElementById('client_email').value.trim() || null,
                nationality: document.getElementById('client_nationality').value.trim() || null,
                qatar_residence_status: document.getElementById('qatar_residence_status').value,
                destination_country: document.getElementById('destination_country').value,
                visa_or_service_type: document.getElementById('visa_or_service_type').value,
                planned_travel_date: document.getElementById('planned_travel_date').value || null,
                passport_no: document.getElementById('passport_no').value.trim() || null,
                notes: document.getElementById('case_notes').value.trim() || null,
                source_code: 'website',
                campaign: 'Lead Generation Page'
            };

            try {
                submitBtn.disabled = true;
                spinner.style.display = 'inline';

                const response = await fetch('/api/v1/leads', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    showSuccessModal(data.lead_number, data.assigned_agent, payload.name, payload.destination_country);
                    document.getElementById('leadGenerationForm').reset();
                    document.getElementById('sync_wa').checked = true;
                    handleDestinationChange();
                } else {
                    const errorMsg = data.message || 'Failed to submit inquiry. Please review your details.';
                    showToast('⚠️ ' + errorMsg);
                }
            } catch (err) {
                console.error(err);
                showToast('❌ Network error. Please check your connection.');
            } finally {
                submitBtn.disabled = false;
                spinner.style.display = 'none';
            }
        }

        // Success Modal Controls
        function showSuccessModal(leadNumber, agentName, clientName, destination) {
            document.getElementById('modalLeadNum').textContent = leadNumber;
            document.getElementById('modalAssignedOfficer').textContent = agentName || 'Senior Visa Officer';

            const waText = encodeURIComponent(`Hello Venture Visionary, my inquiry reference is ${leadNumber} for ${destination}. I have just submitted my details online.`);
            document.getElementById('modalWhatsAppBtn').href = `https://wa.me/97455112233?text=${waText}`;

            document.getElementById('successModal').classList.add('active');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
        }

        function copyLeadNumber() {
            const leadNum = document.getElementById('modalLeadNum').textContent;
            navigator.clipboard.writeText(leadNum).then(() => {
                showToast('✓ Lead Number copied: ' + leadNum);
            });
        }

        // Lead Status Lookup Handler
        async function handleLeadLookup(event) {
            event.preventDefault();
            const query = document.getElementById('lookupQuery').value.trim();
            const btn = document.getElementById('lookupBtn');
            const resultCard = document.getElementById('lookupResultCard');

            if (!query) return;

            try {
                btn.disabled = true;
                btn.textContent = 'Fetching... ⏳';

                const res = await fetch(`/api/v1/leads/lookup?query=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (res.ok && data.status === 'success') {
                    const lead = data.lead;
                    document.getElementById('resLeadNumber').textContent = lead.lead_number;
                    document.getElementById('resClientName').textContent = lead.name;
                    document.getElementById('resDestination').textContent = lead.destination_country;
                    document.getElementById('resServiceType').textContent = lead.visa_or_service_type;
                    document.getElementById('resConsultant').textContent = lead.assigned_consultant;
                    document.getElementById('resSource').textContent = lead.source;
                    document.getElementById('resCreatedAt').textContent = lead.created_at;

                    const badge = document.getElementById('resStatusBadge');
                    badge.textContent = lead.status;
                    badge.className = 'status-badge ' + lead.status.toLowerCase();

                    const waMsg = encodeURIComponent(`Hello, regarding my inquiry ${lead.lead_number}: I would like an update on my visa filing.`);
                    document.getElementById('resWhatsAppBtn').href = `https://wa.me/97455112233?text=${waMsg}`;

                    resultCard.style.display = 'block';
                } else {
                    resultCard.style.display = 'none';
                    showToast('🔍 ' + (data.message || 'No record found.'));
                }
            } catch (err) {
                console.error(err);
                showToast('❌ Error connecting to server.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Fetch Status 🔍';
            }
        }

        // Live Feed Fetcher
        async function fetchRecentLeads() {
            const tableBody = document.getElementById('feedTableBody');
            const icon = document.getElementById('refreshIcon');

            try {
                icon.style.animation = 'spin 0.6s linear infinite';
                const res = await fetch('/api/v1/leads?limit=15', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (res.ok && data.leads) {
                    if (data.leads.length === 0) {
                        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 2rem;">No inbound leads recorded yet.</td></tr>`;
                        return;
                    }

                    tableBody.innerHTML = data.leads.map(lead => `
                        <tr>
                            <td><strong style="color:#93c5fd; font-family:var(--font-display);">${lead.lead_number}</strong></td>
                            <td>${lead.name}</td>
                            <td>
                                <span class="badge-channel ${lead.source_code}">
                                    ${lead.source}
                                </span>
                            </td>
                            <td>${lead.destination_country || 'General'} • <small style="color:var(--text-dim);">${lead.visa_or_service_type || 'Consultation'}</small></td>
                            <td style="color:#60a5fa;">${lead.assigned_agent}</td>
                            <td>
                                <span class="status-badge ${lead.status}">
                                    ${lead.status}
                                </span>
                            </td>
                            <td style="color:var(--text-muted); font-size:0.8rem;">${lead.created_at_human}</td>
                        </tr>
                    `).join('');
                }
            } catch (err) {
                console.error(err);
            } finally {
                icon.style.animation = 'none';
            }
        }

        // Toast Helper
        function showToast(text) {
            const toast = document.getElementById('toastMsg');
            document.getElementById('toastText').textContent = text;
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3500);
        }

        // Initialize default presets
        document.addEventListener('DOMContentLoaded', () => {
            setDatePreset(30);
            autoSyncWhatsApp();
        });
    </script>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/lead-generation.blade.php ENDPATH**/ ?>