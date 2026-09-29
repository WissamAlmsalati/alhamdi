import { useEffect, useRef } from 'react'

// Floating nuts and coffee beans drawn on a canvas over the hero video.
export default function HeroParticles() {
  const ref = useRef(null)
  useEffect(() => {
    const cv = ref.current
    if (!cv || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return
    const ctx = cv.getContext('2d')
    const dpr = Math.min(window.devicePixelRatio || 1, 2)
    let W = 0, H = 0, mx = 0, my = 0, raf
    const size = () => { W = cv.width = cv.offsetWidth * dpr; H = cv.height = cv.offsetHeight * dpr }
    size()
    window.addEventListener('resize', size)
    const cols = [['#C9B37A', '#8FA04A', 0], ['#4A2A18', '#2A160C', 1], ['#B5764A', '#8E5633', 0], ['#2B1A12', '#0E0703', 1], ['#E7C89A', '#D9B27A', 0]]
    const P = Array.from({ length: 26 }, (_, i) => ({
      x: Math.random(), y: Math.random(), z: 0.3 + Math.random() * 0.7, r: Math.random() * Math.PI,
      vr: (Math.random() - 0.5) * 0.01, vy: 0.00025 + Math.random() * 0.0005, c: cols[i % cols.length],
    }))
    const parent = cv.parentElement
    const onMove = (e) => { const r = cv.getBoundingClientRect(); mx = (e.clientX - r.left) / r.width - 0.5; my = (e.clientY - r.top) / r.height - 0.5 }
    parent.addEventListener('mousemove', onMove)
    const draw = () => {
      ctx.clearRect(0, 0, W, H)
      for (const p of P) {
        p.y -= p.vy * p.z; p.r += p.vr
        if (p.y < -0.1) { p.y = 1.1; p.x = Math.random() }
        const x = (p.x + mx * 0.06 * p.z) * W, y = (p.y + my * 0.06 * p.z) * H, s = (12 + 22 * p.z) * dpr
        ctx.save(); ctx.translate(x, y); ctx.rotate(p.r); ctx.globalAlpha = 0.3 + 0.45 * p.z
        if (p.c[2]) {
          ctx.fillStyle = p.c[0]; ctx.beginPath(); ctx.ellipse(0, 0, s * 0.62, s * 0.85, 0, 0, 7); ctx.fill()
          ctx.strokeStyle = p.c[1]; ctx.lineWidth = s * 0.12; ctx.beginPath(); ctx.moveTo(0, -s * 0.75); ctx.quadraticCurveTo(-s * 0.3, 0, 0, s * 0.75); ctx.stroke()
        } else {
          ctx.fillStyle = p.c[0]; ctx.beginPath(); ctx.ellipse(0, 0, s, s * 0.58, 0, 0, 7); ctx.fill()
          ctx.fillStyle = p.c[1]; ctx.beginPath(); ctx.ellipse(0, 0, s * 0.72, s * 0.4, 0, 0, 7); ctx.fill()
        }
        ctx.restore()
      }
      raf = requestAnimationFrame(draw)
    }
    draw()
    return () => { cancelAnimationFrame(raf); window.removeEventListener('resize', size); parent.removeEventListener('mousemove', onMove) }
  }, [])
  return <canvas ref={ref} aria-hidden="true" />
}
