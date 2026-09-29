import { Link } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { SITE, whatsappLink } from '../config/site.js'

export default function CtaBox({ title, desc, id }) {
  const { t } = useLanguage()
  return (
    <section className="sec rv" id={id} style={{ paddingTop: 0 }}>
      <div className="wrap">
        <div className="cta-box">
          <div>
            <span className="kicker">{t('ctaKicker')}</span>
            <h2>{title || t('ctaTitle')}</h2>
            <p>{desc || t('ctaDesc')}</p>
          </div>
          <div className="cta-acts">
            {SITE.phones.map((p) => (
              <div className="contact-line" key={p}><small>{t('phoneLabel')}</small><span>{p}</span></div>
            ))}
            <a className="btn fill" href={whatsappLink()} target="_blank" rel="noopener noreferrer"><span>{t('messageOnWhatsapp')}</span></a>
            <Link className="btn" to="/contact"><span>{t('inquiryForm')}</span></Link>
          </div>
        </div>
      </div>
    </section>
  )
}
