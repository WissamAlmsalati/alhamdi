import { createContext, useCallback, useContext, useEffect, useRef, useState } from 'react'
import { useLanguage } from '../context/LanguageContext.jsx'
import { postForm } from '../lib/api.js'
import ProductImage from './ProductImage.jsx'

const InquiryContext = createContext({ openInquiry: () => {} })
export const useInquiry = () => useContext(InquiryContext)

const WEIGHTS = ['250 g', '500 g', '1 kg', '5 kg', '10 kg']
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]+$/

export function InquiryProvider({ children }) {
  const [product, setProduct] = useState(null)
  const openInquiry = useCallback((p) => setProduct(p), [])
  return (
    <InquiryContext.Provider value={{ openInquiry }}>
      {children}
      {product && <InquiryModal product={product} onClose={() => setProduct(null)} />}
    </InquiryContext.Provider>
  )
}

function InquiryModal({ product, onClose }) {
  const { t } = useLanguage()
  const [form, setForm] = useState({ name: '', phone: '', email: '', weight: '', quantity: '', notes: '' })
  const [errors, setErrors] = useState({})
  const [status, setStatus] = useState('idle') // idle | sending | sent | failed
  const returnFocus = useRef(document.activeElement)
  const nameRef = useRef(null)
  const sheetRef = useRef(null)

  useEffect(() => {
    const prevOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    nameRef.current?.focus()
    const onKey = (e) => {
      if (e.key === 'Escape') onClose()
      if (e.key === 'Tab' && sheetRef.current) {
        const f = sheetRef.current.querySelectorAll('button, input, textarea, a[href]')
        const first = f[0], last = f[f.length - 1]
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus() }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus() }
      }
    }
    document.addEventListener('keydown', onKey)
    const toRestore = returnFocus.current
    return () => {
      document.body.style.overflow = prevOverflow
      document.removeEventListener('keydown', onKey)
      toRestore?.focus?.()
    }
  }, [onClose])

  const set = (key) => (e) => setForm((f) => ({ ...f, [key]: e.target.value }))

  const submit = async (e) => {
    e.preventDefault()
    const next = {}
    if (!form.name.trim()) next.name = t('requiredName')
    if (!form.phone.trim()) next.phone = t('requiredPhone')
    if (form.email.trim() && !EMAIL_RE.test(form.email.trim())) next.email = t('invalidEmail')
    setErrors(next)
    if (Object.keys(next).length) return

    const message = [
      form.weight && `${t('weightLabel')}: ${form.weight}`,
      form.quantity.trim() && `${t('quantityLabel')}: ${form.quantity.trim()}`,
      form.notes.trim(),
    ].filter(Boolean).join('\n')

    setStatus('sending')
    try {
      await postForm('/orders', {
        product_id: product.id,
        name: form.name.trim(),
        phone: form.phone.trim(),
        email: form.email.trim() || null,
        message: message || null,
      })
      setStatus('sent')
    } catch (err) {
      if (err.status === 422 && err.errors) {
        setErrors({
          name: err.errors.name && t('requiredName'),
          phone: err.errors.phone && t('requiredPhone'),
          email: err.errors.email && t('invalidEmail'),
        })
        setStatus('idle')
      } else {
        setStatus('failed')
      }
    }
  }

  return (
    <div className="modal" onMouseDown={(e) => e.target === e.currentTarget && onClose()}>
      <div className="sheet" role="dialog" aria-modal="true" aria-labelledby="inq-title" ref={sheetRef}>
        <div className="sheet-head">
          <div className="mini"><ProductImage product={product} /></div>
          <div>
            <small>{t('inquiryAbout')}</small>
            <h3 id="inq-title">{product.name}</h3>
          </div>
          <button type="button" className="x" onClick={onClose} aria-label={t('close')}>✕</button>
        </div>

        {status === 'sent' ? (
          <div className="done" role="status">
            <span className="ok" aria-hidden="true">✓</span>
            <h3>{t('inquirySentTitle')}</h3>
            <p>{t('inquirySentText')}</p>
            <button type="button" className="btn" onClick={onClose}><span>{t('done')}</span></button>
          </div>
        ) : (
          <form onSubmit={submit} noValidate>
            <div className="fields">
              <div className={`field ${errors.name ? 'err' : ''}`}>
                <label htmlFor="inq-name">{t('fullName')} <em>*</em></label>
                <input id="inq-name" ref={nameRef} value={form.name} onChange={set('name')} autoComplete="name" aria-invalid={!!errors.name} aria-describedby={errors.name ? 'inq-name-e' : undefined} />
                {errors.name && <span className="emsg" id="inq-name-e">{errors.name}</span>}
              </div>
              <div className={`field ${errors.phone ? 'err' : ''}`}>
                <label htmlFor="inq-phone">{t('phone')} <em>*</em></label>
                <input id="inq-phone" type="tel" dir="ltr" value={form.phone} onChange={set('phone')} autoComplete="tel" placeholder="09X XXX XXXX" aria-invalid={!!errors.phone} aria-describedby={errors.phone ? 'inq-phone-e' : undefined} />
                {errors.phone && <span className="emsg" id="inq-phone-e">{errors.phone}</span>}
              </div>
              <div className={`field full ${errors.email ? 'err' : ''}`}>
                <label htmlFor="inq-email">{t('emailOptional')}</label>
                <input id="inq-email" type="email" dir="ltr" value={form.email} onChange={set('email')} autoComplete="email" aria-invalid={!!errors.email} />
                {errors.email && <span className="emsg">{errors.email}</span>}
              </div>
              <div className="field full">
                <span className="label" id="inq-weight-l">{t('preferredWeight')}</span>
                <div className="pills" role="radiogroup" aria-labelledby="inq-weight-l">
                  {WEIGHTS.map((w) => (
                    <button key={w} type="button" role="radio" aria-checked={form.weight === w} className="pill ltr"
                      onClick={() => setForm((f) => ({ ...f, weight: f.weight === w ? '' : w }))}>{w}</button>
                  ))}
                </div>
              </div>
              <div className="field full">
                <label htmlFor="inq-qty">{t('quantity')}</label>
                <input id="inq-qty" value={form.quantity} onChange={set('quantity')} placeholder={t('quantityPlaceholder')} />
              </div>
              <div className="field full">
                <label htmlFor="inq-notes">{t('notes')}</label>
                <textarea id="inq-notes" style={{ minHeight: 90 }} value={form.notes} onChange={set('notes')} placeholder={t('notesPlaceholder')} />
              </div>
            </div>
            {status === 'failed' && <div className="form-error" role="alert">{t('sendError')}</div>}
            <button type="submit" className="btn orange block" disabled={status === 'sending'}>
              <span>{status === 'sending' ? t('sending') : t('sendInquiry')}</span>
            </button>
            <p className="note">{t('noPurchaseNote')}</p>
          </form>
        )}
      </div>
    </div>
  )
}
