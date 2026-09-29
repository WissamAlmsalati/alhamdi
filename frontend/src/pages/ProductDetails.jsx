import { useEffect, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { assetUrl, useApi } from '../lib/api.js'
import PageHeader from '../components/PageHeader.jsx'
import ProductCard, { CardSkeleton } from '../components/ProductCard.jsx'
import ProductImage from '../components/ProductImage.jsx'
import { useInquiry } from '../components/InquiryModal.jsx'
import Icon from '../components/Icon.jsx'

export default function ProductDetails() {
  const { id } = useParams()
  const { t } = useLanguage()
  const { openInquiry } = useInquiry()
  const { data: product, loading, error, retry } = useApi(`/products/${id}`)
  const similar = useApi('/products', product ? { categories: [product.category_id], page: 1, per_page: 5 } : { page: 1, per_page: 5 })

  if (error?.status === 404) {
    return (
      <>
        <PageHeader title1={t('detailsTitle1')} title2={t('detailsTitle2')} crumbs={[{ to: '/products', label: t('products') }, { label: t('notFoundTitle') }]} />
        <div className="wrap state" style={{ paddingBlock: 80 }}>
          <h2 style={{ color: 'var(--text)', marginBottom: 8 }}>{t('notFoundTitle')}</h2>
          <p>{t('notFoundText')}</p>
          <Link className="btn" to="/products"><span>{t('backToProducts')}</span></Link>
        </div>
      </>
    )
  }

  const [active, setActive] = useState(0)
  useEffect(() => setActive(0), [id])
  const gallery = product?.images?.length ? product.images : product?.image ? [product.image] : []

  const similarItems = (similar.data?.data || []).filter((p) => String(p.id) !== String(id)).slice(0, 4)
  const catalog = assetUrl(product?.catalog)

  return (
    <>
      <PageHeader
        title1={t('detailsTitle1')} title2={t('detailsTitle2')}
        crumbs={[{ to: '/products', label: t('products') }, { label: product?.name || '…' }]}
        ghost={t('details')}
      />
      {error ? (
        <div className="wrap state" role="alert">{t('loadError')}<br /><button type="button" className="btn" onClick={retry}><span>{t('retry')}</span></button></div>
      ) : loading || !product ? (
        <div className="wrap pd" aria-busy="true">
          <div className="gal"><div className="main skel" /></div>
          <div className="info"><div className="skel" style={{ height: 24, width: '40%' }} /><div className="skel" style={{ height: 44, marginTop: 12 }} /><div className="skel" style={{ height: 120, marginTop: 16 }} /></div>
        </div>
      ) : (
        <div className="wrap pd">
          <div className="gal">
            <div className="main">
              {gallery.length > 0
                ? <img src={assetUrl(gallery[Math.min(active, gallery.length - 1)])} alt={product.name} />
                : <ProductImage product={product} />}
            </div>
            {gallery.length > 1 && (
              <div className="thumbs" role="group" aria-label={t('productImages')}>
                {gallery.map((src, i) => (
                  <button key={src + i} type="button" className={i === active ? 'on' : ''} aria-pressed={i === active}
                    aria-label={t('imageN', { n: i + 1 })} onClick={() => setActive(i)}>
                    <img src={assetUrl(src)} alt="" loading="lazy" />
                  </button>
                ))}
              </div>
            )}
          </div>
          <div className="info">
            <span className="kind">{[product.category, product.brand].filter(Boolean).join(' · ')}</span>
            <h2>{product.name}</h2>
            <p className="lead">{product.description || t('noDescription')}</p>
            <span className="avail"><i />{t('availableNow')}</span>
            <dl className="specs">
              {product.category && <div><dt>{t('specCategory')}</dt><dd>{product.category}</dd></div>}
              <div><dt>{t('specOrigin')}</dt><dd>{product.brand || t('specOriginValue')}</dd></div>
              <div><dt>{t('specStorage')}</dt><dd>{t('specStorageValue')}</dd></div>
            </dl>
            <div className="pd-acts">
              <button type="button" className="btn orange wide" onClick={() => openInquiry(product)}>
                <span>{t('askAboutThis')}</span><span className="arrow">→</span>
              </button>
              {catalog && (
                <a className="btn" href={catalog} target="_blank" rel="noopener noreferrer" download>
                  <Icon name="file" style={{ width: 18, height: 18 }} /><span>{t('downloadCatalog')}</span>
                </a>
              )}
            </div>
            <div className="perks">
              <div className="perk"><Icon name="check" /><b>{t('perkOriginal')}</b><span>{t('perkOriginalText')}</span></div>
              <div className="perk"><Icon name="shield" /><b>{t('perkQuality')}</b><span>{t('perkQualityText')}</span></div>
              <div className="perk"><Icon name="box" /><b>{t('perkPacking')}</b><span>{t('perkPackingText')}</span></div>
            </div>
          </div>
        </div>
      )}

      {(similar.loading || similarItems.length > 0) && (
        <section style={{ paddingBottom: 96 }}>
          <div className="wrap">
            <div className="sec-head">
              <div><span className="kicker">{t('youMayLike')}</span><h2>{t('similarProducts')}</h2></div>
              <Link className="btn" to="/products"><span>{t('allProducts')}</span><span className="arrow">→</span></Link>
            </div>
            <div className="grid swipe">
              {similar.loading ? Array.from({ length: 4 }, (_, i) => <CardSkeleton key={i} />) : similarItems.map((p, i) => <ProductCard key={p.id} product={p} index={i} />)}
            </div>
          </div>
        </section>
      )}
    </>
  )
}
