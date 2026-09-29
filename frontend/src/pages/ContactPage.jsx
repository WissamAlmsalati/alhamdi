import { useState } from 'react'
import { Link } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { postForm } from '../lib/api.js'
import { SITE, whatsappLink } from '../config/site.js'
import PageHeader from '../components/PageHeader.jsx'
import Icon from '../components/Icon.jsx'

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
const MAX_FILE = 5 * 1024 * 1024

function CopyButton({ value }) {
  const { t } = useLanguage()
  const [copied, setCopied] = useState(false)
  const copy = async (e) => {
    try {
      await navigator.clipboard.writeText(value)
      setCopied(true)
      setTimeout(() => setCopied(false), 1500)
    } catch {
      // Clipboard blocked: select the value so the visitor can copy it manually.
      const el = e.currentTarget.parentElement.querySelector('b')
      const range = document.createRange()
      range.selectNodeContents(el)
      const sel = window.getSelection()
      sel.removeAllRanges()
      sel.addRange(range)
    }
  }
  return <button type="button" className="copy-btn" onClick={copy}>{copied ? t('copied') : t('copy')}</button>
}

export default function ContactPage() {
  const { t } = useLanguage()
  const types = ['typeProduct', 'typeBulk', 'typeOther']
  const [form, setForm] = useState({ name: '', phone: '', email: '', type: 'typeProduct', subject: '', message: '' })
  const [file, setFile] = useState(null)
  const [errors, setErrors] = useState({})
  const [status, setStatus] = useState('idle')
  const set = (k) => (e) => setForm((f) => ({ ...f, [k]: e.target.value }))

  const onFile = (e) => {
    const f = e.target.files?.[0] || null
    if (f && f.size > MAX_FILE) {
      setErrors((x) => ({ ...x, attachment: t('fileTooBig') }))
      e.target.value = ''
      setFile(null)
      return
    }
    setErrors((x) => ({ ...x, attachment: undefined }))
    setFile(f)
  }

  const submit = async (e) => {
    e.preventDefault()
    const next = {}
    if (!form.name.trim()) next.name = t('requiredName')
    if (!form.phone.trim()) next.phone = t('requiredPhone')
    if (form.email.trim() && !EMAIL_RE.test(form.email.trim())) next.email = t('invalidEmail')
    if (!form.message.trim()) next.message = t('requiredMessage')
    setErrors(next)
    if (Object.keys(next).length) {
      document.getElementById(`c-${Object.keys(next)[0]}`)?.focus()
      return
    }
    const fd = new FormData()
    fd.append('name', form.name.trim())
    fd.append('phone', form.phone.trim())
    if (form.email.trim()) fd.append('email', form.email.trim())
    fd.append('subject', [t(form.type), form.subject.trim()].filter(Boolean).join(' · '))
    fd.append('message', form.message.trim())
    if (file) fd.append('attachment', file)

    setStatus('sending')
    try {
      await postForm('/contact', fd)
      setStatus('sent')
    } catch (err) {
      if (err.status === 422 && err.errors) {
        setErrors({
          name: err.errors.name && t('requiredName'),
          phone: err.errors.phone && t('requiredPhone'),
          email: err.errors.email && t('invalidEmail'),
          message: err.errors.message && t('requiredMessage'),
          attachment: err.errors.attachment && t('fileTooBig'),
        })
        setStatus('idle')
      } else {
        setStatus('failed')
      }
    }
  }

  const field = (key, label, input, required = false, full = false) => (
    <div className={`field ${full ? 'full' : ''} ${errors[key] ? 'err' : ''}`}>
      <label htmlFor={`c-${key}`}>{label} {required && <em>*</em>}</label>
      {input}
      {errors[key] && <span className="emsg" id={`c-${key}-e`}>{errors[key]}</span>}
    </div>
  )
  const aria = (key) => ({ 'aria-invalid': !!errors[key], 'aria-describedby': errors[key] ? `c-${key}-e` : undefined })

  return (
    <>
      <PageHeader title1={t('contactPageTitle1')} title2={t('contactPageTitle2')} desc={t('contactPageDesc')} crumbs={[{ label: t('contact') }]} ghost={t('contactPageTitle1')} />
      <div className="wrap ct">
        <div className="ways">
          <div className="way">
            <span className="ic"><Icon name="whatsapp" /></span>
            <div><small>{t('whatsapp')}</small><b className="ltr">{SITE.whatsapp}</b></div>
            <CopyButton value={SITE.whatsapp} />
          </div>
          {SITE.phones.map((p) => (
            <div className="way" key={p}>
              <span className="ic"><Icon name="phone" /></span>
              <div><small>{t('phoneLabel')}</small><b className="ltr">{p}</b></div>
              <CopyButton value={p} />
            </div>
          ))}
          <div className="way">
            <span className="ic"><Icon name="pin" /></span>
            <div><small>{t('addressLabel')}</small><b>{t('addressValue')}</b></div>
          </div>
          <div className="hours">
            <h3>{t('workingHours')}</h3>
            <div><span>{t('satThu')}</span><span>{t('morningShift')}</span></div>
            <div><span>{t('satThu')}</span><span>{t('eveningShift')}</span></div>
            <div><span>{t('friday')}</span><span className="closed">{t('closed')}</span></div>
          </div>
          <div className="mapbox">
            <svg viewBox="0 0 400 250" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
              <rect width="400" height="250" fill="#161616" />
              <g stroke="#2A2A2A" strokeWidth="10" fill="none"><path d="M-10 70 L410 110" /><path d="M-10 190 L410 160" /><path d="M120 -10 L150 260" /><path d="M290 -10 L260 260" /></g>
              <g stroke="#222" strokeWidth="4" fill="none"><path d="M-10 130 L410 135" /><path d="M60 -10 L80 260" /><path d="M350 -10 L340 260" /></g>
              <circle cx="205" cy="128" r="30" fill="#F26B1D" opacity=".18" /><circle cx="205" cy="128" r="9" fill="#F26B1D" />
            </svg>
            <a className="btn orange" href={SITE.mapsUrl} target="_blank" rel="noopener noreferrer"><span>{t('openMaps')}</span></a>
          </div>
        </div>

        <div className="form-card">
          {status === 'sent' ? (
            <div className="done" role="status">
              <span className="ok" aria-hidden="true">✓</span>
              <h3>{t('contactSentTitle')}</h3>
              <p>{t('contactSentText')}</p>
              <Link className="btn" to="/products"><span>{t('browseProducts')}</span><span className="arrow">→</span></Link>
            </div>
          ) : (
            <>
              <h2>{t('sendYourInquiry')}</h2>
              <p>{t('formIntro')}</p>
              <form onSubmit={submit} noValidate>
                <div className="fields">
                  {field('name', t('fullName'), <input id="c-name" value={form.name} onChange={set('name')} autoComplete="name" placeholder={t('namePlaceholder')} {...aria('name')} />, true)}
                  {field('phone', t('phone'), <input id="c-phone" type="tel" dir="ltr" value={form.phone} onChange={set('phone')} autoComplete="tel" placeholder="09X XXX XXXX" {...aria('phone')} />, true)}
                  {field('email', t('emailOptional'), <input id="c-email" type="email" dir="ltr" value={form.email} onChange={set('email')} autoComplete="email" {...aria('email')} />, false, true)}
                  <div className="field full">
                    <span className="label" id="c-type-l">{t('inquiryType')}</span>
                    <div className="pills" role="radiogroup" aria-labelledby="c-type-l">
                      {types.map((k) => (
                        <button key={k} type="button" role="radio" aria-checked={form.type === k} className="pill" onClick={() => setForm((f) => ({ ...f, type: k }))}>{t(k)}</button>
                      ))}
                    </div>
                  </div>
                  {field('subject', t('subject'), <input id="c-subject" value={form.subject} onChange={set('subject')} placeholder={t('subjectPlaceholder')} />, false, true)}
                  {field('message', t('message'), <textarea id="c-message" value={form.message} onChange={set('message')} placeholder={t('messagePlaceholder')} {...aria('message')} />, true, true)}
                  <div className={`field full ${errors.attachment ? 'err' : ''}`}>
                    <label className="drop" htmlFor="c-file">
                      <Icon name="clip" style={{ width: 20, height: 20, flex: 'none' }} />
                      <span>{file ? <><b>{file.name}</b> · {t('fileReady')}</> : <><b>{t('attachFile')}</b> · {t('attachHint')}</>}</span>
                    </label>
                    <input id="c-file" type="file" className="sr-only" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" onChange={onFile} />
                    {errors.attachment && <span className="emsg">{errors.attachment}</span>}
                  </div>
                </div>
                {status === 'failed' && <div className="form-error" role="alert">{t('sendError')}</div>}
                <button type="submit" className="btn fill block" disabled={status === 'sending'}>
                  <span>{status === 'sending' ? t('sending') : t('sendInquiry')}</span><span className="arrow">→</span>
                </button>
                <p className="note" style={{ fontSize: 12.5, color: 'var(--muted)', marginTop: 12 }}>
                  <a href={whatsappLink()} target="_blank" rel="noopener noreferrer" style={{ color: 'var(--orange)', fontWeight: 700 }}>{t('messageOnWhatsapp')}</a>
                </p>
              </form>
            </>
          )}
        </div>
      </div>
    </>
  )
}
