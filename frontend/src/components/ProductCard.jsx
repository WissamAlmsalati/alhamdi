import { Link } from 'react-router-dom'
import { useLanguage } from '../context/LanguageContext.jsx'
import { useInquiry } from './InquiryModal.jsx'
import ProductImage from './ProductImage.jsx'

export default function ProductCard({ product, index }) {
  const { t } = useLanguage()
  const { openInquiry } = useInquiry()
  const kind = [product.category, product.brand].filter(Boolean).join(' · ')

  return (
    <article className="card">
      <Link to={`/products/${product.id}`} className="thumb" tabIndex={-1} aria-hidden="true">
        {index !== undefined && <span className="idx num">{String(index + 1).padStart(2, '0')}</span>}
        <ProductImage product={product} />
      </Link>
      <div className="body">
        {kind && <span className="kind">{kind}</span>}
        <h3><Link to={`/products/${product.id}`}>{product.name}</Link></h3>
        <p>{product.description || t('productFallbackDesc')}</p>
      </div>
      <div className="act">
        <button type="button" onClick={() => openInquiry(product)}>{t('askAboutProduct')}</button>
        <Link to={`/products/${product.id}`}>{t('details')}</Link>
      </div>
    </article>
  )
}

export function CardSkeleton() {
  return (
    <div className="card loading" aria-hidden="true">
      <div className="thumb skel" />
      <div className="body">
        <div className="line skel" style={{ width: '40%' }} />
        <div className="line skel" style={{ width: '80%', height: 18 }} />
        <div className="line skel" />
        <div className="line skel" style={{ width: '60%' }} />
      </div>
    </div>
  )
}
