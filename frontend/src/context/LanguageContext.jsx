import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react'
import translations from '../translations/index.js'

const LanguageContext = createContext(null)
const STORAGE_KEY = 'mix-language'

function readStoredLanguage() {
  try {
    const stored = localStorage.getItem(STORAGE_KEY)
    return stored === 'en' || stored === 'ar' ? stored : 'ar'
  } catch {
    return 'ar'
  }
}

export function LanguageProvider({ children }) {
  const [language, setLanguage] = useState(readStoredLanguage)

  useEffect(() => {
    try {
      localStorage.setItem(STORAGE_KEY, language)
    } catch {
      // Storage can be blocked (private mode); the choice then lasts for this visit only.
    }
    document.documentElement.lang = language
    document.documentElement.dir = language === 'ar' ? 'rtl' : 'ltr'
    document.title = translations[language].siteTitle
  }, [language])

  // t('key', { name: 'x' }) replaces {{name}} placeholders.
  const t = useCallback(
    (key, params) => {
      let text = translations[language]?.[key] ?? translations.ar[key] ?? key
      if (params) {
        for (const [name, value] of Object.entries(params)) {
          text = text.replaceAll(`{{${name}}}`, String(value))
        }
      }
      return text
    },
    [language],
  )

  const value = useMemo(
    () => ({
      language,
      setLanguage,
      toggleLanguage: () => setLanguage((l) => (l === 'ar' ? 'en' : 'ar')),
      isRtl: language === 'ar',
      t,
    }),
    [language, t],
  )

  return <LanguageContext.Provider value={value}>{children}</LanguageContext.Provider>
}

export function useLanguage() {
  const context = useContext(LanguageContext)
  if (!context) {
    throw new Error('useLanguage must be used within LanguageProvider')
  }
  return context
}
