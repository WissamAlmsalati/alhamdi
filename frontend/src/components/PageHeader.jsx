import { Link } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'

// crumbs: [{ to?, label }], last one is the current page.
export default function PageHeader({ title1, title2, desc, crumbs = [], ghost }) {
  const { t } = useLanguage()
  return (
    <section className="phead">
      <div className="wrap">
        <nav className="crumb" aria-label="breadcrumb">
          <Link to="/">{t('breadcrumbHome')}</Link>
          {crumbs.map((c, i) => (
            <span key={i} style={{ display: 'contents' }}>
              <span aria-hidden="true">/</span>
              {c.to ? <Link to={c.to}>{c.label}</Link> : <b aria-current="page">{c.label}</b>}
            </span>
          ))}
        </nav>
        <h1>{title1} <span className="o">{title2}</span></h1>
        {desc && <p>{desc}</p>}
      </div>
      {ghost && <div className="ghost" aria-hidden="true">{ghost}</div>}
    </section>
  )
}
