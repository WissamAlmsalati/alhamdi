import { useLanguage } from '../context/LanguageContext.jsx'

export default function Hero({ className = '' }) {
  const { t, language } = useLanguage()

  return (
    <section
      id="hero"
      className={`relative w-full h-[112vw] max-h-[540px] min-h-[440px] md:h-screen md:max-h-none md:min-h-[600px] overflow-hidden bg-white md:bg-[#f4f6f5] ${className}`}
    >
      {/* Desktop background artwork */}
      <img
        src="/assets/hero-bg.svg"
        alt=""
        className="hidden md:block absolute inset-0 w-full h-full object-cover object-center"
        aria-hidden="true"
      />

      {/* Mobile background artwork — landscape image anchored to the bottom */}
      <img
        src="/assets/hero-bg-mobile.png"
        alt=""
        className="md:hidden absolute inset-0 w-full h-full object-contain object-bottom"
        aria-hidden="true"
      />

      {/* Real text content */}
      <div className="absolute inset-0 flex flex-col items-center justify-start pt-[15%] md:pt-[24vh] px-5 text-center">
        <h1 className="text-3xl md:text-6xl lg:text-7xl font-extrabold text-[var(--text-dark)] leading-tight mb-3">
          {t('heroTitle1')}
        </h1>
        <p className="text-2xl md:text-5xl lg:text-6xl font-bold text-[var(--primary)] mb-5">
          {t('heroTitle2')}
        </p>
        <p className="text-sm md:text-lg text-[var(--text-muted)] max-w-2xl leading-relaxed mb-10 md:mb-16">
          {t('heroDesc')}
        </p>

        <a
          href="#products"
          className="inline-flex items-center justify-center gap-2 bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-white font-semibold px-6 md:px-8 py-3 md:py-3.5 rounded-full transition-colors shadow-lg w-full max-w-[260px] md:w-auto md:max-w-none"
        >
          <svg
            className={`w-5 h-5 ${language === 'ar' ? 'rotate-180' : ''}`}
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
          {t('exploreProducts')}
        </a>
      </div>

      {/* Scroll indicator at the very bottom */}
      <a
        href="#products"
        className="absolute bottom-6 left-1/2 -translate-x-1/2 z-10 text-[var(--text-dark)]/70 hover:text-[var(--primary)] transition-colors animate-bounce"
        aria-label={language === 'ar' ? 'انتقل للأسفل' : 'Scroll down'}
      >
        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </a>
    </section>
  )
}
