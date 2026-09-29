import { useLanguage } from '../context/LanguageContext.jsx'

// Drawn fallback art for products without a photo: the brand's clear jar
// (blue ribbed lid, orange seal strip, round orange/black label) filled with the product.

const CASHEW = 'M-40 -6 C-30 -30 30 -30 40 -6 C46 8 30 18 14 10 C0 4 -14 4 -28 10 C-44 18 -48 8 -40 -6Z'

// ---------- Large pieces (quick nav icons) ----------
const bean = (a, b, c) => (
  <>
    <g transform="rotate(-30)"><ellipse rx="42" ry="58" fill={a} /><path d="M0 -54 Q-16 0 0 54" stroke={c} strokeWidth="7" fill="none" /></g>
    <g transform="translate(55 48) rotate(25)"><ellipse rx="24" ry="33" fill={b} /><path d="M0 -30 Q-9 0 0 30" stroke={c} strokeWidth="4" fill="none" /></g>
    <g transform="translate(-58 -50) rotate(60)"><ellipse rx="20" ry="28" fill={b} /><path d="M0 -26 Q-8 0 0 26" stroke={c} strokeWidth="4" fill="none" /></g>
  </>
)
const ART = {
  pistachio: (
    <>
      <g transform="rotate(-25)"><ellipse rx="64" ry="38" fill="#C9B37A" /><ellipse rx="50" ry="28" fill="#8FA04A" /><ellipse cx="-3" rx="32" ry="18" fill="#B4C56A" /></g>
      <g transform="translate(50 50) rotate(30)"><ellipse rx="34" ry="20" fill="#C9B37A" /><ellipse rx="26" ry="15" fill="#8FA04A" /></g>
      <g transform="translate(-55 -50) rotate(60)"><ellipse rx="30" ry="18" fill="#C9B37A" /><ellipse rx="23" ry="13" fill="#8FA04A" /></g>
    </>
  ),
  coffee: bean('#4A2A18', '#5B361C', '#2A160C'),
  espresso: bean('#2B1A12', '#3A2012', '#0E0703'),
  cashew: (
    <>
      <g transform="rotate(20) scale(1.25)"><path d={CASHEW} fill="#E7C89A" /></g>
      <g transform="translate(30 50) rotate(-150) scale(.9)"><path d={CASHEW} fill="#E7C89A" /></g>
      <g transform="translate(-45 -50) rotate(80) scale(.75)"><path d={CASHEW} fill="#E7C89A" /></g>
    </>
  ),
  almond: (
    <>
      <g transform="rotate(-40)"><ellipse rx="52" ry="30" fill="#B5764A" /><path d="M-52 0 L52 0" stroke="#8E5633" strokeWidth="2" /></g>
      <g transform="translate(45 45) rotate(30)"><ellipse rx="36" ry="20" fill="#B5764A" /></g>
      <g transform="translate(-50 -45) rotate(70)"><ellipse rx="32" ry="18" fill="#B5764A" /></g>
    </>
  ),
  hazelnut: (
    <>
      <circle r="44" fill="#7A4A26" /><circle cx="-14" cy="-16" r="15" fill="#A76D3E" />
      <circle cx="55" cy="50" r="26" fill="#7A4A26" /><circle cx="-55" cy="-52" r="22" fill="#6A3E1E" />
    </>
  ),
  walnut: (
    <>
      <circle r="50" fill="#C69A66" />
      <path d="M0 -50 Q-22 0 0 50 Q22 0 0 -50 M-50 0 Q0 -16 50 0 M-50 0 Q0 16 50 0" stroke="#8E6440" strokeWidth="3" fill="none" />
      <circle cx="58" cy="52" r="22" fill="#B98A58" />
    </>
  ),
  mix: (
    <>
      <g transform="translate(-40 -30) rotate(-25)"><ellipse rx="30" ry="18" fill="#C9B37A" /><ellipse rx="23" ry="13" fill="#8FA04A" /></g>
      <g transform="translate(35 -40) rotate(-40)"><ellipse rx="28" ry="15" fill="#B5764A" /></g>
      <g transform="translate(40 40) rotate(20) scale(.7)"><path d={CASHEW} fill="#E7C89A" /></g>
      <circle cx="-45" cy="40" r="20" fill="#7A4A26" />
      <g transform="translate(5 8) rotate(-30)"><ellipse rx="10" ry="14" fill="#4A2A18" /></g>
    </>
  ),
}

// ---------- Small pieces (jar contents) ----------
const PIECE = {
  pistachio: (k) => (
    <>
      <ellipse rx="12" ry="8" fill={k % 2 ? '#D9C68F' : '#CDB77C'} />
      <ellipse cx="1" rx="7.5" ry="4.6" fill={k % 3 ? '#93A84A' : '#A9BC5C'} />
      <path d="M-11 0 Q0 -3 11 0" stroke="#A89060" strokeWidth="1" fill="none" />
    </>
  ),
  almond: (k) => (
    <>
      <ellipse rx="12" ry="7" fill={k % 2 ? '#B5764A' : '#A9683E'} />
      <path d="M-11 0 Q0 1 11 0" stroke="#8E5633" strokeWidth="1" fill="none" />
    </>
  ),
  cashew: (k) => <g transform="scale(.3)"><path d={CASHEW} fill={k % 2 ? '#EBCFA5' : '#E2C18F'} /></g>,
  hazelnut: (k) => (
    <>
      <circle r="8.5" fill={k % 2 ? '#7A4A26' : '#6A3E1E'} />
      <circle cx="-2.6" cy="-3" r="3.2" fill="#A76D3E" />
    </>
  ),
  walnut: (k) => (
    <>
      <circle r="10" fill={k % 2 ? '#C69A66' : '#B98A58'} />
      <path d="M0 -10 Q-4 0 0 10 Q4 0 0 -10 M-10 0 Q0 -3 10 0" stroke="#8E6440" strokeWidth="1.2" fill="none" />
    </>
  ),
  coffee: (k) => (
    <>
      <ellipse rx="6.5" ry="9" fill={k % 2 ? '#4A2A18' : '#5B361C'} />
      <path d="M0 -8 Q-3 0 0 8" stroke="#2A160C" strokeWidth="1.6" fill="none" />
    </>
  ),
  espresso: (k) => (
    <>
      <ellipse rx="6.5" ry="9" fill={k % 2 ? '#2B1A12' : '#3A2012'} />
      <path d="M0 -8 Q-3 0 0 8" stroke="#0E0703" strokeWidth="1.6" fill="none" />
    </>
  ),
}
const MIX_ORDER = ['pistachio', 'cashew', 'almond', 'hazelnut', 'walnut']

// Deterministic pseudo-random numbers so a product's jar looks the same on every render.
function rng(seed) {
  let s = (Number(seed) || 1) * 9301 + 49297
  return () => {
    s = (s * 9301 + 49297) % 233280
    return s / 233280
  }
}

function contents(kind, seed) {
  const rand = rng(seed)
  const items = []
  const coffee = kind === 'coffee' || kind === 'espresso'
  const stepX = coffee ? 13 : 19
  const stepY = coffee ? 12 : 15
  let k = 0
  for (let y = 222; y > 64; y -= stepY) {
    const offset = (Math.round((222 - y) / stepY) % 2) * (stepX / 2)
    for (let x = 30 + offset; x < 176; x += stepX) {
      const piece = kind === 'mix' ? MIX_ORDER[Math.floor(rand() * MIX_ORDER.length)] : kind
      const jx = (rand() - 0.5) * 6
      const jy = (rand() - 0.5) * 6
      const rot = Math.round(rand() * 360)
      items.push(
        <g key={k} transform={`translate(${(x + jx).toFixed(1)} ${(y + jy).toFixed(1)}) rotate(${rot})`}>
          {PIECE[piece](k)}
        </g>,
      )
      k++
    }
  }
  return items
}

// Word boundary for Arabic: "مكس" must not match inside "مكسرات", "بن" not inside other words.
const AR = '؀-ۿ'
const word = (w) => new RegExp(`(?<![${AR}])${w}(?![${AR}])`)

// Product line: MiX blends (ground, in glass jars), Al-Hami nuts (clear jar), Al-Hami coffee.
const LINE_RULES = [
  ['blend', [/blend|royal|qunbula|mix\b/i, /خلط|ملكي|قنبل/, word('مكس'), word('ميكس')]],
  ['coffee', [/coffee|cardamom/i, word('بن'), /قهوة|حبهان|هيل/]],
]
export function productLine(...texts) {
  for (const text of texts.filter(Boolean)) {
    for (const [key, rules] of LINE_RULES) if (rules.some((re) => re.test(text))) return key
  }
  return 'nuts'
}

// Which nut is inside a nuts jar. "مشكل" / assorted falls back to a mix of nuts.
const NUT_RULES = [
  ['pistachio', /pistach|فستق/i],
  ['cashew', /cashew|كاجو/i],
  ['almond', /almond|لوز/i],
  ['walnut', /walnut|عين جمل/i],
  ['hazelnut', /hazel|بندق/i],
]
export function artKey(...texts) {
  const line = productLine(...texts)
  if (line === 'coffee') return 'coffee'
  if (line === 'blend') return 'mix'
  for (const text of texts.filter(Boolean)) {
    for (const [key, re] of NUT_RULES) if (re.test(text)) return key
  }
  return 'mix'
}

// Ground blend / coffee powder texture.
function powder(seed, base, specks) {
  const rand = rng(seed)
  const dots = []
  for (let n = 0; n < 260; n++) {
    const x = 30 + rand() * 140
    const y = 60 + rand() * 170
    dots.push(<circle key={n} cx={x.toFixed(1)} cy={y.toFixed(1)} r={(0.8 + rand() * 1.8).toFixed(1)} fill={specks[n % specks.length]} />)
  }
  return <><rect x="30" y="58" width="140" height="172" fill={base} />{dots}</>
}

function JarShell({ clipId, children, lid }) {
  return (
    <>
      <defs><clipPath id={clipId}><rect x="30" y="58" width="140" height="170" rx="16" /></clipPath></defs>
      <ellipse cx="100" cy="232" rx="74" ry="6" fill="#000" opacity=".12" />
      <rect x="30" y="58" width="140" height="170" rx="16" fill="#F7F7F4" />
      <g clipPath={`url(#${clipId})`}>{children}</g>
      <rect x="30" y="58" width="140" height="170" rx="16" fill="#FFFFFF" opacity=".14" />
      <rect x="30" y="58" width="140" height="170" rx="16" fill="none" stroke="#CFD3D8" strokeWidth="2" />
      <rect x="38" y="66" width="7" height="150" rx="3.5" fill="#FFFFFF" opacity=".5" />
      <rect x="156" y="70" width="4" height="120" rx="2" fill="#FFFFFF" opacity=".3" />
      {lid}
    </>
  )
}

function RibbedLid({ color, light, dark }) {
  return (
    <>
      <rect x="28" y="16" width="144" height="36" rx="7" fill={color} />
      <rect x="28" y="16" width="144" height="10" rx="5" fill={light} />
      {Array.from({ length: 17 }, (_, i) => <rect key={i} x={34 + i * 8} y="28" width="3" height="21" rx="1.5" fill={dark} opacity=".75" />)}
      <rect x="32" y="50" width="136" height="9" rx="3" fill={dark} />
    </>
  )
}

function Burlap() {
  // Burlap cloth tied over a glass jar (MiX line packaging).
  const lines = []
  for (let i = 0; i < 16; i++) lines.push(<path key={`h${i}`} d={`M18 ${14 + i * 4} Q100 ${10 + i * 4} 182 ${14 + i * 4}`} stroke="#A98B5F" strokeWidth=".8" opacity=".6" fill="none" />)
  for (let i = 0; i < 26; i++) lines.push(<path key={`v${i}`} d={`M${22 + i * 6.3} 8 L${20 + i * 6.3} 78`} stroke="#B89A6C" strokeWidth=".7" opacity=".5" fill="none" />)
  return (
    <>
      <path d="M16 18 Q100 -4 184 18 L190 58 Q176 86 160 72 Q148 92 128 78 Q112 96 96 80 Q80 96 66 78 Q48 94 38 74 Q20 84 12 60 Z" fill="#CBB184" />
      <g clipPath="url(#burlap-clip)">{lines}</g>
      <path d="M22 50 Q100 60 178 50" stroke="#8C6A3E" strokeWidth="4" fill="none" />
      <path d="M22 50 Q100 60 178 50" stroke="#A9855A" strokeWidth="1.5" fill="none" />
    </>
  )
}

function Badge({ line1, line2, sub, big, ring = '#F7941D' }) {
  return (
    <>
      <circle cx="100" cy="149" r="42" fill="#111" />
      <circle cx="100" cy="149" r="37" fill={ring} />
      <circle cx="100" cy="149" r="33" fill="#111" />
      <path d="M72 136 Q100 122 128 136" stroke={ring} strokeWidth="2.5" fill="none" strokeLinecap="round" />
      {line2 ? (
        <>
          <text x="100" y="147" textAnchor="middle" fontFamily="Lalezar, Alexandria, sans-serif" fontSize="13" fill="#F3EFD2">{line1}</text>
          <text x="100" y="162" textAnchor="middle" fontFamily="Lalezar, Alexandria, sans-serif" fontSize="13" fill="#F3EFD2">{line2}</text>
        </>
      ) : (
        <text x="100" y="155" textAnchor="middle" fontFamily="Lalezar, Alexandria, sans-serif" fontSize={big || 18} fill="#F3EFD2">{line1}</text>
      )}
      {sub && <text x="100" y="172" textAnchor="middle" fontFamily="Arial, sans-serif" fontWeight="700" fontSize="6.5" fill={ring}>{sub}</text>}
      <path d="M76 176 Q100 186 124 176" stroke={ring} strokeWidth="2" fill="none" strokeLinecap="round" />
    </>
  )
}

export default function ProductArt({ product }) {
  const { language } = useLanguage()
  const texts = [product?.name, product?.category, product?.category_key]
  const line = productLine(...texts)
  const kind = artKey(...texts)
  const id = product?.id ?? 7
  const clipId = `jar-${id}-${line}`
  const en = language === 'en'

  let body, lid, label
  if (line === 'blend') {
    body = powder(id, '#9A6B3E', ['#6E4523', '#C29160', '#3F2814', '#D8B27D'])
    lid = (
      <>
        <defs><clipPath id="burlap-clip"><path d="M16 18 Q100 -4 184 18 L190 58 Q176 86 160 72 Q148 92 128 78 Q112 96 96 80 Q80 96 66 78 Q48 94 38 74 Q20 84 12 60 Z" /></clipPath></defs>
        <Burlap />
      </>
    )
    label = (
      <>
        <rect x="30" y="118" width="140" height="62" fill="#111" />
        <rect x="30" y="122" width="140" height="54" fill="#F7941D" />
        <Badge line1={en ? 'MiX' : 'ميكس'} sub="100% Natural" big={en ? 20 : 19} />
      </>
    )
  } else if (line === 'coffee') {
    body = powder(id, '#4A2C18', ['#2E1A0D', '#6B4326', '#1C0F07', '#7C5332'])
    lid = <RibbedLid color="#C99A2E" light="#E2B84A" dark="#9C7420" />
    label = (
      <>
        <rect x="30" y="112" width="140" height="74" fill="#141414" />
        <rect x="30" y="116" width="140" height="2" fill="#C99A2E" />
        <rect x="30" y="180" width="140" height="2" fill="#C99A2E" />
        <Badge line1={en ? 'Al-Hami' : 'بن'} line2={en ? 'Coffee' : 'الحامي'} ring="#C99A2E" />
      </>
    )
  } else {
    body = contents(kind, id)
    lid = (
      <>
        <RibbedLid color="#2446B6" light="#3A5DD0" dark="#1B3590" />
        <rect x="90" y="12" width="20" height="104" fill="#111" />
        <rect x="93" y="12" width="14" height="104" fill="#F7941D" />
      </>
    )
    label = (
      <>
        <rect x="30" y="118" width="140" height="62" fill="#111" />
        <rect x="30" y="122" width="140" height="54" fill="#F7941D" />
        <Badge line1={en ? 'Al-Hami' : 'مكسرات'} line2={en ? 'Nuts' : 'الحامي'} />
      </>
    )
  }

  return (
    <svg viewBox="0 0 200 240" role="img" aria-label={product?.name || ''}>
      <JarShell clipId={clipId} lid={lid}>{body}</JarShell>
      {label}
    </svg>
  )
}

export function MiniArt({ kind }) {
  return (
    <svg viewBox="0 0 200 200" aria-hidden="true">
      <g transform="translate(100 100) scale(1.15)">{ART[kind]}</g>
    </svg>
  )
}
