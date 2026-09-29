import { useCallback, useEffect, useState } from 'react'
import { API_URL } from '../config/api.js'
import { useLanguage } from '../context/LanguageContext.jsx'

export function assetUrl(path) {
  if (!path) return null
  if (/^https?:\/\//.test(path)) return path
  // API returns "/storage/..." relative to the backend host.
  const base = API_URL.replace(/\/api\/?$/, '')
  return `${base}${path}`
}

export async function getJson(path, params = {}) {
  const url = new URL(`${API_URL}${path}`, window.location.origin)
  for (const [key, value] of Object.entries(params)) {
    if (Array.isArray(value)) value.forEach((v) => url.searchParams.append(`${key}[]`, v))
    else if (value !== undefined && value !== null && value !== '') url.searchParams.set(key, value)
  }
  const res = await fetch(url, { headers: { Accept: 'application/json' } })
  if (!res.ok) {
    const error = new Error(`Request failed: ${res.status}`)
    error.status = res.status
    throw error
  }
  return res.json()
}

export async function postForm(path, data) {
  const body = data instanceof FormData ? data : JSON.stringify(data)
  const headers = { Accept: 'application/json' }
  if (!(data instanceof FormData)) headers['Content-Type'] = 'application/json'
  const res = await fetch(`${API_URL}${path}`, { method: 'POST', headers, body })
  const json = await res.json().catch(() => ({}))
  if (!res.ok) {
    const error = new Error(json.message || `Request failed: ${res.status}`)
    error.status = res.status
    error.errors = json.errors || {}
    throw error
  }
  return json
}

// Loads JSON for the current language; re-fetches when the language or params change.
export function useApi(path, params = {}) {
  const { language } = useLanguage()
  const key = JSON.stringify(params)
  const [state, setState] = useState({ data: null, loading: true, error: null })
  const [attempt, setAttempt] = useState(0)

  useEffect(() => {
    let cancelled = false
    setState((s) => ({ ...s, loading: true, error: null }))
    getJson(path, { ...JSON.parse(key), lang: language })
      .then((data) => !cancelled && setState({ data, loading: false, error: null }))
      .catch((error) => !cancelled && setState({ data: null, loading: false, error }))
    return () => {
      cancelled = true
    }
  }, [path, key, language, attempt])

  const retry = useCallback(() => setAttempt((n) => n + 1), [])
  return { ...state, retry }
}
