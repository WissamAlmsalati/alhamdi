// VITE_API_URL: full API address (e.g. https://api.example.com/api).
// VITE_API_HOST: host only, as Render provides it (e.g. mix-backend.onrender.com).
const host = import.meta.env.VITE_API_HOST
export const API_URL = import.meta.env.VITE_API_URL || (host ? `https://${host}/api` : 'http://127.0.0.1:8002/api')
