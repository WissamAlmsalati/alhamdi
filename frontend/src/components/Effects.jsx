import { useEffect, useState } from 'react'
import { useLocation } from 'react-router-dom'

const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches

// Brand intro, shown once per browser session.
export function Intro() {
  const [state, setState] = useState(() => {
    try {
      if (reducedMotion() || sessionStorage.getItem('mix-intro')) return 'gone'
    } catch { /* storage blocked: show intro */ }
    return 'show'
  })
  useEffect(() => {
    if (state !== 'show') return
    try { sessionStorage.setItem('mix-intro', '1') } catch { /* ignore */ }
    const a = setTimeout(() => setState('done'), 1500)
    const b = setTimeout(() => setState('gone'), 2400)
    return () => { clearTimeout(a); clearTimeout(b) }
  }, [state])
  if (state === 'gone') return null
  return (
    <div className={`intro ${state === 'done' ? 'done' : ''}`} aria-hidden="true">
      <div className="word"><span><img src="/assets/logo.png" alt="" width="200" height="200" style={{ width: 'min(46vw, 220px)', height: 'auto' }} /></span></div>
      <div className="bar"><i /></div>
    </div>
  )
}

export function ScrollProgress() {
  const [width, setWidth] = useState(0)
  useEffect(() => {
    const onScroll = () => {
      const h = document.documentElement
      const max = h.scrollHeight - h.clientHeight
      setWidth(max > 0 ? (h.scrollTop / max) * 100 : 0)
    }
    onScroll()
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])
  return <div className="progress" style={{ width: `${width}%` }} aria-hidden="true" />
}

// Scroll to top on page change, or to #hash targets (e.g. /#categories).
export function ScrollManager() {
  const { pathname, hash } = useLocation()
  useEffect(() => {
    if (hash) {
      const tryScroll = (n) => {
        const el = document.getElementById(hash.slice(1))
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
        else if (n > 0) setTimeout(() => tryScroll(n - 1), 120)
      }
      tryScroll(15)
    } else {
      window.scrollTo({ top: 0, behavior: 'instant' })
    }
  }, [pathname, hash])
  return null
}

// Sections with class "rv" slide in from a visible resting state.
export function useReveal(deps = []) {
  useEffect(() => {
    const els = document.querySelectorAll('.rv:not(.in)')
    if (!('IntersectionObserver' in window) || reducedMotion()) {
      els.forEach((el) => el.classList.add('in'))
      return
    }
    const io = new IntersectionObserver((entries) => entries.forEach((e) => {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target) }
    }), { threshold: 0.08 })
    els.forEach((el) => io.observe(el))
    return () => io.disconnect()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, deps)
}

// Shows the final number at rest; animates from 0 the first time it scrolls into view.
export function CountUp({ value }) {
  const [shown, setShown] = useState(value)
  const [el, setEl] = useState(null)
  useEffect(() => {
    setShown(value)
    if (!el || reducedMotion() || typeof value !== 'number' || !('IntersectionObserver' in window)) return
    let raf
    const io = new IntersectionObserver(([entry]) => {
      if (!entry.isIntersecting) return
      io.disconnect()
      const start = performance.now()
      const step = (now) => {
        const k = Math.min(1, (now - start) / 1300)
        setShown(Math.round(value * (1 - Math.pow(1 - k, 3))))
        if (k < 1) raf = requestAnimationFrame(step)
      }
      raf = requestAnimationFrame(step)
    }, { threshold: 0.4 })
    io.observe(el)
    return () => { io.disconnect(); cancelAnimationFrame(raf) }
  }, [el, value])
  return <b ref={setEl} className="num">{shown}</b>
}
