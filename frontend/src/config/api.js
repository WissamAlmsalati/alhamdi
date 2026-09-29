// VITE_API_URL: full API address (e.g. https://api.example.com/api).
// VITE_API_HOST: host only. Render's fromService "host" is the internal name (e.g. "mix-backend-0ile"),
// which a browser can't reach, so a host without a dot gets Render's public ".onrender.com" suffix.
const rawHost = import.meta.env.VITE_API_HOST
const host = rawHost && !rawHost.includes('.') ? `${rawHost}.onrender.com` : rawHost
export const API_URL = import.meta.env.VITE_API_URL || (host ? `https://${host}/api` : 'http://127.0.0.1:8002/api')
