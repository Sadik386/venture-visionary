/**
 * Venture Visionary Lead Generation Landing Page - Configuration
 * 
 * Set your CRM Backend URL below.
 * - For local development: "http://localhost:8090"
 * - For production: "https://your-crm-domain.com"
 * 
 * Note: You can also override this dynamically in the browser via the
 * "⚙️ CRM Connection" button in the top navigation without editing code.
 */
window.CRM_CONFIG = {
    // Default fallback API URL (change this to your production CRM domain when deploying)
    defaultApiUrl: "http://localhost:8090",

    // Current active API URL (checks localStorage override first, then defaults to above)
    getApiUrl: function () {
        const stored = localStorage.getItem('VENTURE_CRM_API_URL');
        if (stored && stored.trim() !== '') {
            return stored.replace(/\/+$/, '');
        }
        return this.defaultApiUrl.replace(/\/+$/, '');
    },

    setApiUrl: function (url) {
        if (!url || url.trim() === '') {
            localStorage.removeItem('VENTURE_CRM_API_URL');
        } else {
            localStorage.setItem('VENTURE_CRM_API_URL', url.trim().replace(/\/+$/, ''));
        }
    }
};
