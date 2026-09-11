import { useEffect, useState } from 'react'
import { API_URL } from '../config/api.js'
import { useLanguage } from '../context/LanguageContext.jsx'

function LeafIcon({ active = false }) {
  return (
    <svg
      className={`w-3.5 h-3.5 ${active ? 'text-white' : 'text-[#2FAB4B]'}`}
      viewBox="0 0 24 24"
      fill="currentColor"
      aria-hidden="true"
    >
      <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z" />
    </svg>
  )
}

export default function Specialties({ selectedCategory, onSelectCategory, className = '' }) {
  const { t, language } = useLanguage()
  const isAr = language === 'ar'
  const [specialties, setSpecialties] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetch(`${API_URL}/categories`)
      .then((res) => res.json())
      .then((data) => setSpecialties(Array.isArray(data) ? data : []))
      .catch(() => setSpecialties([]))
      .finally(() => setLoading(false))
  }, [])

  const handleSelect = (id) => {
    onSelectCategory(selectedCategory === id ? null : id)
  }

  return (
    <section id="specialties" className={`relative py-16 md:py-20 px-5 lg:px-10 bg-white overflow-hidden ${className}`}>
      {/* Decorative leaves */}
      <img
        src="/assets/leaf-decor-1.svg"
        alt=""
        className="absolute -bottom-10 -left-16 w-32 md:w-48 h-auto opacity-80 pointer-events-none"
        aria-hidden="true"
      />
      <img
        src="/assets/leaf-decor-2.svg"
        alt=""
        className="absolute bottom-10 -right-10 w-28 md:w-40 h-auto opacity-80 pointer-events-none"
        aria-hidden="true"
      />

      <div className="relative z-10 max-w-6xl mx-auto">
        {/* Heading */}
        <div className="flex items-center justify-center gap-3 mb-8 md:mb-10">
          <h2 className="text-2xl md:text-3xl font-bold text-[#2FAB4B]">
            {t('browseBySpecialty')}
          </h2>
          <LeafIcon />
        </div>

        {loading ? (
          <p className="text-center text-gray-500">{t('loading')}</p>
        ) : specialties.length === 0 ? (
          <p className="text-center text-gray-500">{t('noSpecialties')}</p>
        ) : (
          <div className="grid grid-cols-2 gap-3 md:flex md:flex-wrap md:items-center md:justify-center md:gap-4">
            {specialties.map((specialty) => {
              const isActive = selectedCategory === specialty.id
              return (
                <button
                  key={specialty.id}
                  type="button"
                  onClick={() => handleSelect(specialty.id)}
                  className={`flex items-center justify-center gap-2 px-4 md:px-5 py-3 rounded-full border text-sm md:text-base font-medium transition-colors w-full md:w-auto ${
                    isActive
                      ? 'bg-[#2FAB4B] border-[#2FAB4B] text-white'
                      : 'border-gray-200 bg-white text-[#01526D] hover:border-[#2FAB4B] hover:text-[#2FAB4B]'
                  }`}
                >
                  {specialty.name}
                  <LeafIcon active={isActive} />
                </button>
              )
            })}

            <button
              type="button"
              onClick={() => onSelectCategory(null)}
              className="flex items-center justify-center gap-2 px-6 py-3.5 rounded-full border border-[#2FAB4B] bg-[#e8f5e9] text-[#2FAB4B] text-sm md:text-base font-semibold hover:bg-[#2FAB4B] hover:text-white transition-colors w-full md:w-auto col-span-2 md:col-span-1 justify-self-center"
            >
              <svg
                className={`w-4 h-4 ${isAr ? 'rotate-180' : ''}`}
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M17 8l4 4m0 0l-4 4m4-4H3"
                />
              </svg>
              {t('showAllSpecialties')}
            </button>
          </div>
        )}
      </div>
    </section>
  )
}
