import { useEffect, useState } from 'react'
import { Link, NavLink, useLocation } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import Icon from './Icon.jsx'

export default function Header() {
  const { t, toggleLanguage, language } = useLanguage()
  const [open, setOpen] = useState(false)
  const { pathname } = useLocation()

  useEffect(() => setOpen(false), [pathname])

  const links = [
    { to: '/', label: t('home'), end: true },
    { to: '/products', label: t('products') },
    { to: '/#categories', label: t('categories'), hash: true },
    { to: '/about', label: t('about') },
    { to: '/contact', label: t('contact') },
  ]

  const renderLink = (l) =>
    l.hash ? (
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
