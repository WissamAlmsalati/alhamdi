import { useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { useApi } from '../lib/api.js'
import { SITE } from '../config/site.js'
import { CountUp, useReveal } from '../components/Effects.jsx'
import HeroParticles from '../components/HeroParticles.jsx'
import ProductCard, { CardSkeleton } from '../components/ProductCard.jsx'
import CtaBox from '../components/CtaBox.jsx'
import Icon from '../components/Icon.jsx'
import ProductArt, { MiniArt } from '../components/ProductArt.jsx'

function Hero() {
  const { t } = useLanguage()
  const [videoOk, setVideoOk] = useState(true)
  const [muted, setMuted] = useState(true)

  return (
    <section className="hero" id="hero">
      {videoOk ? (
        <video
          autoPlay muted={muted} loop playsInline preload="metadata" poster={SITE.heroPoster}
          onError={() => setVideoOk(false)}
          aria-hidden="true"
        >
          <source src={SITE.heroVideo} type="video/mp4" onError={() => setVideoOk(false)} />
        </video>
      ) : (
        <div className="video-ph" aria-hidden="true" />
      )}
      <div className="shade" aria-hidden="true" />
      <HeroParticles />
      <div className="wrap">
        <div>
          <span className="kicker">{t('heroKicker')}</span>
          <h1>
            {t('heroTitle1')} <span className="o">{t('heroTitle2')}</span>
            <br />
            <span className="outline">{t('heroTitle3')}</span>
          </h1>
          <p>{t('heroDesc')}</p>
          <div className="hero-acts">
            <Link className="btn orange" to="/products"><span>{t('browseProducts')}</span><span className="arrow">→</span></Link>
            <Link className="btn light" to="/contact"><span>{t('askNow')}</span></Link>
          </div>
        </div>
        <div className="hero-side">
          {videoOk && <span className="vid-tag"><i />{t('videoTag')}</span>}
          <div className="badge">
            <small>{t('featuredBadgeLabel')}</small>
            <b>{t('featuredBadgeTitle')}</b>
            <small>{t('featuredBadgeSub')}</small>
          </div>
        </div>
      </div>
      {videoOk && (
        <button type="button" className="mute" onClick={() => setMuted((m) => !m)} aria-label={muted ? t('unmuteVideo') : t('muteVideo')} aria-pressed={!muted}>
          <Icon name={muted ? 'mute' : 'volume'} />
        </button>
      )}
    </section>
  )
}

function Ticker() {
  const { t } = useLanguage()
  const items = [t('qnPistachio'), t('qnArabicCoffee'), t('qnAlmond'), t('qnEspresso'), t('qnCashew'), t('qnHazelnut')]
  return (
    <div className="ticker" aria-hidden="true">
      <div className="track">{[...items, ...items, ...items, ...items].map((x, i) => <span key={i}>{x}</span>)}</div>
    </div>
  )
}

const QUICK_TERMS = {
  pistachio: { ar: 'فستق', en: 'pistachio' },
  almond: { ar: 'لوز', en: 'almond' },
  cashew: { ar: 'كاجو', en: 'cashew' },
  hazelnut: { ar: 'بندق', en: 'hazelnut' },
  coffee: { ar: 'بن', en: 'coffee' },
  espresso: { ar: 'خلطة', en: 'blend' },
}

function QuickNav() {
  const { t, language } = useLanguage()
  const navigate = useNavigate()
  const items = [
    ['pistachio', 'qnPistachio', 'qnPistachioSub'],
    ['almond', 'qnAlmond', 'qnAlmondSub'],
    ['cashew', 'qnCashew', 'qnCashewSub'],
    ['hazelnut', 'qnHazelnut', 'qnHazelnutSub'],
    ['coffee', 'qnArabicCoffee', 'qnArabicCoffeeSub'],
    ['espresso', 'qnEspresso', 'qnEspressoSub'],
  ]
  return (
    <section className="quick rv">
      <div className="wrap row">
        {items.map(([art, title, sub]) => (
          <button key={art} type="button" className="nut" onClick={() => navigate(`/products?q=${encodeURIComponent(QUICK_TERMS[art][language])}`)}>
            <span className="ring"><MiniArt kind={art} /></span>
            <b>{t(title)}</b>
            <small>{t(sub)}</small>
          </button>
        ))}
      </div>
    </section>
  )
}

function AboutTeaser() {
  const { t } = useLanguage()
  return (
    <section className="sec about rv" id="about">
      <div className="wrap">
        <div>
          <span className="kicker">{t('aboutKicker')}</span>
          <h2>{t('aboutTitle')}</h2>
          <p className="lead">{t('aboutLead')}</p>
          <p>{t('aboutBody')}</p>
          <div className="facts">
            {[['factQuality', 'factQualityText'], ['factPacking', 'factPackingText'], ['factSupply', 'factSupplyText'], ['factInquiry', 'factInquiryText']].map(([a, b]) => (
              <div className="fact" key={a}><b>{t(a)}</b>{t(b)}</div>
            ))}
          </div>
          <Link to="/about" className="btn" style={{ marginTop: 24 }}><span>{t('learnMore')}</span><span className="arrow">→</span></Link>
        </div>
        <figure className="about-vis">
          <div className="frame"><BagsIllustration /></div>
          <div className="stamp" aria-hidden="true">{t('stampText')}</div>
        </figure>
      </div>
    </section>
  )
}

// Three brand jars (pistachio, mixed nuts, coffee) standing together.
export function BagsIllustration() {
  const jars = [
    { id: 901, name: 'فستق pistachio' },
    { id: 902, name: 'خلطة mix' },
    { id: 903, name: 'قهوة coffee' },
  ]
  return (
    <div className="jars" aria-hidden="true">
      {jars.map((p) => <div key={p.id} className="jar"><ProductArt product={p} /></div>)}
    </div>
  )
}

function Stats() {
  const { t } = useLanguage()
  const products = useApi('/products', { page: 1, per_page: 1 })
  const categories = useApi('/categories')
  const years = new Date().getFullYear() - SITE.foundedYear
  const n = (d) => (typeof d === 'number' ? d : '–')
  return (
    <section className="stats">
      <div className="wrap">
        <div className="stat"><CountUp value={years} /><span>{t('statYears')}</span></div>
        <div className="stat"><CountUp value={n(products.data?.total)} /><span>{t('statProducts')}</span></div>
        <div className="stat"><CountUp value={n(Array.isArray(categories.data) ? categories.data.length : null)} /><span>{t('statCategories')}</span></div>
        <div className="stat"><b className="num">100%</b><span>{t('statOrigins')}</span></div>
      </div>
    </section>
  )
}

function Featured() {
  const { t } = useLanguage()
  const { data, loading, error, retry } = useApi('/products', { page: 1, per_page: 4 })
  const items = data?.data || []
  const total = data?.total || 0
  return (
    <section className="sec rv" id="products">
      <div className="wrap">
        <div className="sec-head">
          <div><span className="kicker">{t('productsKicker')}</span><h2>{t('featuredTitle')}</h2></div>
          <p>{t('featuredDesc')}</p>
        </div>
        {error ? (
          <div className="state" role="alert">{t('loadError')}<br /><button type="button" className="btn" onClick={retry}><span>{t('retry')}</span></button></div>
        ) : (
          <div className="grid swipe" aria-busy={loading}>
            {loading
              ? Array.from({ length: 4 }, (_, i) => <CardSkeleton key={i} />)
              : items.length
                ? items.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)
                : <div className="empty">{t('noProducts')}</div>}
          </div>
        )}
        {total > 0 && (
          <div className="more">
            <span className="num">{t('showingOf', { shown: items.length, total })}</span>
            <span className="bar" aria-hidden="true"><i style={{ width: `${Math.min(100, (items.length / total) * 100)}%` }} /></span>
            <Link className="btn" to="/products"><span>{t('allProducts')}</span><span className="arrow">→</span></Link>
          </div>
        )}
      </div>
    </section>
  )
}

function Categories() {
  const { t } = useLanguage()
  const navigate = useNavigate()
  const { data, loading, error, retry } = useApi('/categories')
  const cats = Array.isArray(data) ? data : []
  return (
    <section className="sec cats rv" id="categories">
      <div className="wrap">
        <div className="sec-head">
          <div><span className="kicker">{t('categoriesKicker')}</span><h2>{t('categoriesTitle')}</h2></div>
          <p>{t('categoriesDesc')}</p>
        </div>
        {error ? (
          <div className="state" role="alert">{t('loadError')}<br /><button type="button" className="btn" onClick={retry}><span>{t('retry')}</span></button></div>
        ) : loading ? (
          <div className="state">{t('loading')}</div>
        ) : cats.length === 0 ? (
          <div className="state">{t('noCategories')}</div>
        ) : (
          <div className="cat-list">
            {cats.map((c, i) => (
              <button key={c.id} type="button" className="cat" onClick={() => navigate(`/products?category=${c.id}`)}>
                <span className="num">{String(i + 1).padStart(2, '0')}</span>
                <span>
                  <span className="name">{c.name}</span>
                  {c.description && <span className="desc">{c.description}</span>}
                </span>
                <span className="cnt">{t('itemsCount', { count: c.products_count ?? 0 })} <span className="arrow" aria-hidden="true">→</span></span>
              </button>
            ))}
          </div>
        )}
      </div>
    </section>
  )
}

export default function Home() {
  useReveal([])
  return (
    <>
      <Hero />
      <Ticker />
      <QuickNav />
      <AboutTeaser />
      <Stats />
      <Featured />
      <Categories />
      <CtaBox id="cta" />
    </>
  )
}
