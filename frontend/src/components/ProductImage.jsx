import { useState } from 'react'
import { assetUrl } from '../lib/api.js'
import ProductArt from './ProductArt.jsx'

// Shows the uploaded photo, or the drawn fallback when there is none or it fails to load.
export default function ProductImage({ product, rotate = 0 }) {
  const [failed, setFailed] = useState(false)
  const src = assetUrl(product?.image)
  if (src && !failed) {
    return <img src={src} alt={product.name} loading="lazy" onError={() => setFailed(true)} />
  }
  return <ProductArt product={product} rotate={rotate} />
}
