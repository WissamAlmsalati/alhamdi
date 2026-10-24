import { useEffect, useState } from 'react'
import { Link, NavLink, useLocation } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import Icon from './Icon.jsx'

// Home-page sections (in page order) and the nav link each one lights up while it's on screen.
const SECTION_LINK = { hero: 'home', about: 'about', products: 'products', categories: 'categories', cta: 'contact' }

function useHomeSection(enabled) {
  const [section, setSection] = useState('hero')

  useEffect(() => {
    if (!enabled) return
    const onScroll = () => {
      const line = window.innerHeight * 0.35
      let current = 'hero'
      for (const id of Object.keys(SECTION_LINK)) {
        const el = document.getElementById(id)
        if (el && el.getBoundingClientRect().top <= line) current = id
      }
      const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2
      setSection(atBottom ? 'cta' : current)
    }
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    window.addEventListener('resize', onScroll)
    return () => {
      window.removeEventListener('scroll', onScroll)
      window.removeEventListener('resize', onScroll)
    }
  }, [enabled])

  return SECTION_LINK[section]
}

export default function Header() {
  const { t, toggleLanguage, language } = useLanguage()
  const [open, setOpen] = useState(false)
  const { pathname } = useLocation()

  useEffect(() => setOpen(false), [pathname])

  const onHome = pathname === '/'
  const homeLink = useHomeSection(onHome)

  const links = [
    // Same order as the home-page sections, so the active link moves step by step while scrolling.
    { key: 'home', to: '/', label: t('home'), end: true },
    { key: 'about', to: '/about', label: t('about') },
    { key: 'products', to: '/products', label: t('products') },
    { key: 'categories', to: '/#categories', label: t('categories'), hash: true },
    { key: 'contact', to: '/contact', label: t('contact') },
  ]

  const renderLink = (l) =>
    onHome ? (
      <Link key={l.to} to={l.to} className={homeLink === l.key ? 'active' : ''} aria-current={homeLink === l.key ? 'true' : undefined}>
        {l.label}
      </Link>
    ) : l.hash ? (
      <Link key={l.to} to={l.to}>{l.label}</Link>
    ) : (
      <NavLink key={l.to} to={l.to} end={l.end} className={({ isActive }) => (isActive ? 'active' : '')}>
        {l.label}
      </NavLink>
    )

  return (
    <header className="header">
      <nav className="wrap" aria-label={t('home')}>
        <Link to="/" className="logo" aria-label={t('brand')}>
          <img src="/assets/logo-128.png" alt="" width="46" height="46" className="logo-img" />
          <b>{t('brand')}</b>
        </Link>
        <div className="links">{links.map(renderLink)}</div>
        <div className="nav-cta">
          <Link to="/contact" className="btn orange"><span>{t('askNow')}</span></Link>
          <button type="button" className="lang" onClick={toggleLanguage} lang={language === 'ar' ? 'en' : 'ar'}>
            {t('switchLanguage')}
          </button>
          <button
            type="button"
            className="menu-btn"
            aria-expanded={open}
            aria-controls="mobile-menu"
            aria-label={open ? t('closeMenu') : t('openMenu')}
            onClick={() => setOpen((o) => !o)}
          >
            <Icon name={open ? 'close' : 'menu'} />
          </button>
        </div>
      </nav>
      {open && (
        <div id="mobile-menu" className="mobile-menu">
          {links.map(renderLink)}
          <Link to="/contact" className="btn orange" style={{ marginTop: 12 }}><span>{t('askNow')}</span></Link>
        </div>
      )}
    </header>
  )
}
