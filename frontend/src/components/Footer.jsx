import { Link } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { useApi } from '../lib/api.js'
import { SITE } from '../config/site.js'

export default function Footer() {
  const { t, toggleLanguage } = useLanguage()
  const { data: categories } = useApi('/categories')

  return (
    <footer className="footer">
      <div className="wrap">
        <div className="cols">
          <div>
            <Link to="/" className="logo">
              <img src="/assets/logo-128.png" alt="" width="56" height="56" className="logo-img" />
              <b>{t('brand')}</b>
            </Link>
            <p style={{ fontSize: 13.5, margin: '14px 0 0', maxWidth: '34ch' }}>{t('footerAbout')}</p>
          </div>
          <div>
            <h4>{t('links')}</h4>
            <ul>
              <li><Link to="/">{t('home')}</Link></li>
              <li><Link to="/products">{t('products')}</Link></li>
              <li><Link to="/about">{t('about')}</Link></li>
              <li><Link to="/contact">{t('contact')}</Link></li>
            </ul>
          </div>
          <div>
            <h4>{t('categories')}</h4>
            <ul>
              {(Array.isArray(categories) ? categories.slice(0, 6) : []).map((c) => (
                <li key={c.id}><Link to={`/products?category=${c.id}`}>{c.name}</Link></li>
              ))}
            </ul>
          </div>
          <div>
            <h4>{t('contactInfo')}</h4>
            <ul>
              {SITE.phones.map((p) => <li key={p} className="ltr" style={{ textAlign: 'start' }}><a href={`tel:${p}`}>{p}</a></li>)}
              <li>{t('workingHoursShort')}</li>
            </ul>
          </div>
        </div>
        <div className="big" aria-hidden="true">{t('brand')}</div>
        <div className="copy">
          <span>{t('rights', { year: new Date().getFullYear() })}</span>
          <button type="button" onClick={toggleLanguage}>{t('switchLanguage')}</button>
        </div>
      </div>
    </footer>
  )
}
