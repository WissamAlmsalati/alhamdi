import { useEffect, useRef, useState } from 'react'
import { useLanguage } from '../context/LanguageContext.jsx'
import { assetUrl, useApi } from '../lib/api.js'
import { SITE } from '../config/site.js'
import PageHeader from '../components/PageHeader.jsx'
import CtaBox from '../components/CtaBox.jsx'
import Icon from '../components/Icon.jsx'
import { CountUp, useReveal } from '../components/Effects.jsx'
import { BagsIllustration } from './Home.jsx'

function formatDate(iso, language) {
  if (!iso) return ''
  try {
    return new Intl.DateTimeFormat(language === 'ar' ? 'ar-LY' : 'en-GB', { year: 'numeric', month: 'short', day: 'numeric' }).format(new Date(iso))
  } catch {
    return iso
  }
}

function DocViewer({ doc, onClose }) {
  const { t } = useLanguage()
  const closeRef = useRef(null)
  const url = assetUrl(doc.file)
  useEffect(() => {
    const prev = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    const returnTo = document.activeElement
    closeRef.current?.focus()
    const onKey = (e) => e.key === 'Escape' && onClose()
    document.addEventListener('keydown', onKey)
    return () => { document.body.style.overflow = prev; document.removeEventListener('keydown', onKey); returnTo?.focus?.() }
  }, [onClose])
  return (
    <div className="modal viewer" onMouseDown={(e) => e.target === e.currentTarget && onClose()}>
      <div className="sheet" role="dialog" aria-modal="true" aria-labelledby="doc-title">
        <div className="sheet-head">
          <div><small>{t('officialDoc')}</small><h3 id="doc-title">{doc.title}</h3></div>
          <button type="button" className="x" ref={closeRef} onClick={onClose} aria-label={t('close')}>✕</button>
        </div>
        <div className="frame-wrap">
          {doc.file_type === 'pdf' ? <iframe src={url} title={doc.title} /> : <img src={url} alt={doc.title} />}
        </div>
        <div className="foot">
          <a className="btn fill" href={url} target="_blank" rel="noopener noreferrer"><Icon name="external" style={{ width: 17, height: 17 }} /><span>{t('openInNewTab')}</span></a>
          <button type="button" className="btn" onClick={onClose}><span>{t('close')}</span></button>
        </div>
      </div>
    </div>
  )
}

function Certificates() {
  const { t, language } = useLanguage()
  const { data, loading, error, retry } = useApi('/certificates')
  const [open, setOpen] = useState(null)
  const docs = Array.isArray(data) ? data : []

  const validity = (d) => {
    if (d.status === 'expired') return ['bad', t('certExpired', { date: formatDate(d.expires_at, language) })]
    if (d.status === 'expiring') return ['warn', t('certExpiring', { date: formatDate(d.expires_at, language) })]
    if (d.status === 'valid') return ['', t('certValid', { date: formatDate(d.expires_at, language) })]
    return ['', t('certNoExpiry')]
  }

  return (
    <section className="certs" id="certificates">
      <div className="wrap">
        <div className="sec-head">
          <div><span className="kicker">{t('certsKicker')}</span><h2>{t('certsTitle')}</h2></div>
          <p>{t('certsDesc')}</p>
        </div>
        {error ? (
          <div className="state" role="alert" style={{ color: '#bdbdbd' }}>{t('loadError')}<br /><button type="button" className="btn light" onClick={retry}><span>{t('retry')}</span></button></div>
        ) : loading ? (
          <div className="state" style={{ color: '#bdbdbd' }}>{t('loading')}</div>
        ) : docs.length === 0 ? (
          <div className="empty">{t('certsEmpty')}</div>
        ) : (
          <div className="cert-grid">
            {docs.map((d) => {
              const [cls, label] = validity(d)
              return (
                <article className="cert" key={d.id}>
                  <div className="doc" aria-hidden="true"><span>{d.file_type === 'pdf' ? 'PDF' : 'IMG'}</span></div>
                  <div>
                    <h3>{d.title}</h3>
                    {d.issuer && <p className="by">{d.issuer}</p>}
                    <span className={`valid ${cls}`}><i />{label}</span>
                    <dl>
                      {d.number && <><dt>{t('certNumber')}</dt><dd className="ltr" style={{ textAlign: 'start' }}>{d.number}</dd></>}
                      {d.issued_at && <><dt>{t('certIssued')}</dt><dd>{formatDate(d.issued_at, language)}</dd></>}
                    </dl>
                  </div>
                  {d.file && <div className="row2"><button type="button" onClick={() => setOpen(d)}>{t('viewFile')}</button></div>}
                </article>
              )
            })}
          </div>
        )}
      </div>
      {open && <DocViewer doc={open} onClose={() => setOpen(null)} />}
    </section>
  )
}

export default function AboutPage() {
  const { t } = useLanguage()
  const products = useApi('/products', { page: 1, per_page: 1 })
  useReveal([])
  const years = new Date().getFullYear() - SITE.foundedYear

  const milestones = [['2020', 'm1'], ['36888', 'm2'], ['ISO', 'm3'], ['✓', 'm4'], ['LY', 'm5']]
  const steps = [
    ['s1', ['s1a', 's1b']],
    ['s2', ['s2a', 's2b', 's2c']],
    ['s3', ['s3a', 's3b']],
  ]
  const serve = [['store', 'serveSupermarkets'], ['cake', 'serveSweets'], ['cup', 'serveCafes'], ['hotel', 'serveHotels'], ['home', 'serveHomes']]

  return (
    <>
      <PageHeader title1={t('aboutPageTitle1')} title2={t('aboutPageTitle2')} desc={t('aboutPageDesc')} crumbs={[{ label: t('about') }]} ghost={t('brand')} />

      <section className="wrap ab-intro rv">
        <div>
          <span className="kicker">{t('ourStory')}</span>
          <p className="big">{t('storyStatement1')} <span className="o">{t('storyStatement2')}</span>{t('storyStatement3')}</p>
          <p>{t('storyP1')}</p>
          <p>{t('storyP2')}</p>
          <p>{t('storyP3')}</p>
          <div className="sig"><img src="/assets/logo-128.png" alt="" width="52" height="52" /><div><b>{t('signName')}</b><small>{t('signRole')}</small></div></div>
        </div>
        <div className="frame"><BagsIllustration /></div>
      </section>

      <section className="stats">
        <div className="wrap">
          <div className="stat"><CountUp value={years} /><span>{t('statYears')}</span></div>
          <div className="stat"><b className="num">{SITE.foundedYear}</b><span>{t('statShops')}</span></div>
          <div className="stat"><b className="num">{SITE.licenseNumber}</b><span>{t('statCities')}</span></div>
          <div className="stat"><CountUp value={typeof products.data?.total === 'number' ? products.data.total : '–'} /><span>{t('statProducts')}</span></div>
        </div>
      </section>

      <section className="tl rv">
        <div className="wrap">
          <div className="sec-head"><div><span className="kicker">{t('journeyKicker')}</span><h2>{t('journeyTitle')}</h2></div><p>{t('journeyDesc')}</p></div>
          <ol className="tl-list">
            {milestones.map(([year, k]) => (
              <li className="tl-item" key={k}><b>{year}</b><h3>{t(`${k}Title`)}</h3><p>{t(`${k}Text`)}</p></li>
            ))}
          </ol>
        </div>
      </section>

      <section className="how rv">
        <div className="wrap">
          <div className="sec-head"><div><span className="kicker">{t('howKicker')}</span><h2>{t('howTitle')}</h2></div><p>{t('howDesc')}</p></div>
          <div className="how-grid">
            {steps.map(([k, bullets], i) => (
              <div className="how-step" key={k}>
                <span className="n">{t('stepN', { n: i + 1 })}</span>
                <h3>{t(`${k}Title`)}</h3>
                <p>{t(`${k}Text`)}</p>
                <ul>{bullets.map((b) => <li key={b}>{t(b)}</li>)}</ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Certificates />

      <section className="serve rv">
        <div className="wrap">
          <div className="sec-head"><div><span className="kicker">{t('serveKicker')}</span><h2>{t('serveTitle')}</h2></div></div>
          <div className="serve-grid">
            {serve.map(([icon, k]) => (
              <div className="serve-item" key={k}>
                <span className="ic"><Icon name={icon} /></span>
                <b>{t(k)}</b>
                <small>{t(`${k}Text`)}</small>
              </div>
            ))}
          </div>
        </div>
      </section>

      <CtaBox title={t('aboutCtaTitle')} desc={t('aboutCtaDesc')} />
    </>
  )
}
