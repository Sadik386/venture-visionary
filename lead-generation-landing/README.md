# Venture Visionary Standalone Lead Generation Page

This folder contains the **standalone, decoupled Lead Generation Landing Page** for Venture Visionary.

It is completely decoupled from Laravel/PHP and can be deployed to:
- **Vercel**
- **Netlify**
- **Cloudflare Pages / GitHub Pages**
- **Subdomain on your cPanel / Nginx / Apache** (e.g. `https://apply.venturevisionary.qa` or `https://leads.yourdomain.com`)
- **Any static web server or CDN**

---

## 📁 File Structure

```
lead-generation-landing/
├── index.html        # Standalone, production-ready landing page with lead capture form
├── config.js         # CRM backend API configuration
└── README.md         # Deployment & connection instructions
```

---

## ⚙️ How It Connects to Your CRM

The landing page communicates with your CRM backend via REST API (`POST /api/v1/leads`).

### 1. Configure the CRM URL in `config.js`
Open [`config.js`](file:///home/sadik/sadik/crm/lead-generation-landing/config.js) and set your target CRM backend address:

```javascript
window.CRM_CONFIG = {
    // For local testing:
    defaultApiUrl: "http://localhost:8090",

    // When deploying CRM to production, change to:
    // defaultApiUrl: "https://crm.venturevisionary.qa",
};
```

### 2. Dynamic Browser-Based Override (No Redeploy Required)
In the top-right header of the page, there is a **"⚙️ CRM Settings"** button:
- You can test connectivity with **1 click**.
- You can change the CRM URL on the fly (saved to browser `localStorage`).
- The live indicator turns **🟢 CRM Connected** when healthy, or **🔴 CRM Unreachable** if the CRM server is offline.

---

## 🚀 Deployment Options

### Option 1: Vercel / Netlify (Instant 1-Click)
1. Drag and drop the `lead-generation-landing` folder into [Netlify Drop](https://app.netlify.com/drop).
2. Or push this folder to a GitHub repository and connect to Vercel/Netlify.
3. Configure your custom domain or subdomain (e.g. `apply.venturevisionary.com`).

### Option 2: Nginx / Apache on a Subdomain
Upload `index.html` and `config.js` to your web root (e.g. `/var/www/leads`):
```nginx
server {
    listen 80;
    server_name apply.venturevisionary.qa;
    root /var/www/leads;
    index index.html;

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

---

## 🔒 Cross-Origin (CORS) Support
Your CRM backend already has CORS configured (`config/cors.php`):
- `paths`: `api/*`
- `allowed_origins`: `*` (Allows any external domain or subdomain to submit leads)
- When a lead is submitted from this landing page, it automatically triggers:
  1. Duplicate detection check (by phone/whatsapp/email)
  2. Round-Robin sales agent assignment
  3. Lead activity logging
  4. Returns the official Lead Number (`LD-2026-XXXX`) to the visitor.
