import { useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { useApi } from '../lib/api.js'
import PageHeader from '../components/PageHeader.jsx'
import ProductCard, { CardSkeleton } from '../components/ProductCard.jsx'
import Icon from '../components/Icon.jsx'
import { useReveal } from '../components/Effects.jsx'

const PER_PAGE = 9
const listParam = (v) => (v ? v.split(',').filter(Boolean) : [])

export default function ProductsPage() {
  const { t } = useLanguage()
  const [params, setParams] = useSearchParams()
  const q = params.get('q') || ''
  const cats = listParam(params.get('category'))
  const origins = listParam(params.get('origin'))
  const page = Math.max(1, Number(params.get('page')) || 1)
  const [search, setSearch] = useState(q)
  const [filtersOpen, setFiltersOpen] = useState(false)

  useEffect(() => setSearch(q), [q])
  useReveal([])

  const categories = useApi('/categories')
  const brands = useApi('/brands')
  const products = useApi('/products', { search: q, categories: cats, brands: origins, page, per_page: PER_PAGE })

  const update = (changes, resetPage = true) => {
    const next = new URLSearchParams(params)
    for (const [k, v] of Object.entries(changes)) {
      const value = Array.isArray(v) ? v.join(',') : v
      if (value) next.set(k, value)
      else next.delete(k)
    }
    if (resetPage) next.delete('page')
    setParams(next, { replace: false })
  }

  // Debounced search as you type.
  useEffect(() => {
    if (search === q) return
    const id = setTimeout(() => update({ q: search.trim() }), 350)
    return () => clearTimeout(id)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [search])

  const toggle = (key, list, id) => {
    const s = String(id)
    update({ [key]: list.includes(s) ? list.filter((x) => x !== s) : [...list, s] })
  }

  const catList = Array.isArray(categories.data) ? categories.data : []
  const brandList = Array.isArray(brands.data) ? brands.data : []
  const nameOf = (list, id) => list.find((x) => String(x.id) === id)?.name || id

  const data = products.data
  const items = data?.data || []
  const total = data?.total || 0
  const lastPage = data?.last_page || 1

  const chips = [
    ...cats.map((id) => ({ key: 'category', id, label: nameOf(catList, id) })),
    ...origins.map((id) => ({ key: 'origin', id, label: nameOf(brandList, id) })),
    ...(q ? [{ key: 'q', id: q, label: t('searchLabel', { q }) }] : []),
  ]
  const removeChip = (c) => {
    if (c.key === 'q') { setSearch(''); update({ q: '' }) }
    else update({ [c.key]: (c.key === 'category' ? cats : origins).filter((x) => x !== c.id) })
  }
  const clearAll = () => { setSearch(''); setParams(new URLSearchParams()) }
  const goPage = (n) => { update({ page: n > 1 ? String(n) : '' }, false); window.scrollTo({ top: 0, behavior: 'smooth' }) }

  return (
    <>
      <PageHeader title1={t('productsPageTitle1')} title2={t('productsPageTitle2')} desc={t('productsPageDesc')} crumbs={[{ label: t('products') }]} ghost={t('products')} />
      <div className="wrap shop">
        <aside className={`side ${filtersOpen ? 'open' : ''}`} id="filters" aria-label={t('filters')}>
          <fieldset className="fgroup" style={{ border: 0, padding: 0, margin: 0 }}>
            <legend><h4>{t('filterCategory')}</h4></legend>
            {catList.map((c) => (
              <label className="chk" key={c.id}>
                <input type="checkbox" checked={cats.includes(String(c.id))} onChange={() => toggle('category', cats, c.id)} />
                <span>{c.name}</span>
                {typeof c.products_count === 'number' && <em className="num">{c.products_count}</em>}
              </label>
            ))}
          </fieldset>
          {brandList.length > 0 && (
            <fieldset className="fgroup" style={{ border: 0, padding: 0, margin: 0 }}>
              <legend><h4>{t('filterOrigin')}</h4></legend>
              {brandList.map((b) => (
                <label className="chk" key={b.id}>
                  <input type="checkbox" checked={origins.includes(String(b.id))} onChange={() => toggle('origin', origins, b.id)} />
                  <span>{b.name}</span>
                </label>
              ))}
            </fieldset>
          )}
          {chips.length > 0 && <button type="button" className="clear" onClick={clearAll}>{t('clearFilters')}</button>}
        </aside>

        <div>
          <div className="toolbar">
            <label className="search">
              <Icon name="search" />
              <span className="sr-only">{t('search')}</span>
              <input type="search" value={search} onChange={(e) => setSearch(e.target.value)} placeholder={t('searchPlaceholder')} />
            </label>
            <button type="button" className="btn ftoggle" aria-expanded={filtersOpen} aria-controls="filters" onClick={() => setFiltersOpen((o) => !o)}>
              <Icon name="filter" style={{ width: 17, height: 17 }} /><span>{t('filters')}{chips.length ? ` (${chips.length})` : ''}</span>
            </button>
          </div>

          <p className="count" aria-live="polite" style={{ margin: '0 0 12px' }}>
            {!products.loading && total > 0 && t('resultsCount', { from: data.from, to: data.to, total })}
          </p>

          {chips.length > 0 && (
            <div className="active-f">
              {chips.map((c) => (
                <button key={`${c.key}-${c.id}`} type="button" onClick={() => removeChip(c)} aria-label={t('removeFilter', { name: c.label })}>
                  {c.label} ✕
                </button>
              ))}
            </div>
          )}

          {products.error ? (
            <div className="state" role="alert">{t('loadError')}<br /><button type="button" className="btn" onClick={products.retry}><span>{t('retry')}</span></button></div>
          ) : (
            <div className="grid" aria-busy={products.loading}>
              {products.loading
                ? Array.from({ length: 6 }, (_, i) => <CardSkeleton key={i} />)
                : items.length
                  ? items.map((p, i) => <ProductCard key={p.id} product={p} index={(page - 1) * PER_PAGE + i} />)
                  : (
                    <div className="empty">
                      <b>{t('noResultsTitle')}</b>
                      {t('noResultsText')}
                      {chips.length > 0 && <div><button type="button" className="btn" onClick={clearAll}><span>{t('clearFilters')}</span></button></div>}
                    </div>
                  )}
            </div>
          )}

          {lastPage > 1 && (
            <nav className="pager" aria-label={t('products')}>
              <button type="button" disabled={page <= 1} onClick={() => goPage(page - 1)}>{t('previous')}</button>
              {Array.from({ length: lastPage }, (_, i) => i + 1).map((n) => (
                <button key={n} type="button" className={n === page ? 'on' : ''} aria-current={n === page ? 'page' : undefined} aria-label={t('pageN', { n })} onClick={() => goPage(n)}>{n}</button>
              ))}
              <button type="button" disabled={page >= lastPage} onClick={() => goPage(page + 1)}>{t('next')}</button>
            </nav>
          )}
        </div>
      </div>
    </>
  )
}
