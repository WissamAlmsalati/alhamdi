import { useEffect, useState } from 'react'
import { useParams, Link } from 'react-router-dom'
import { API_URL } from '../config/api.js'
import { useLanguage } from '../context/LanguageContext.jsx'
import Navbar from '../components/Navbar.jsx'
import Footer from '../components/Footer.jsx'
import OrderModal from '../components/OrderModal.jsx'

function Gallery({ images }) {
  const [index, setIndex] = useState(0)

  if (images.length === 0) {
    return (
      <div className="w-full h-64 md:h-96 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 mb-4">
        <svg className="w-20 h-20 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </div>
    )
  }

  const prev = () => setIndex((i) => (i - 1 + images.length) % images.length)
  const next = () => setIndex((i) => (i + 1) % images.length)

  return (
    <div className="mb-4">
      <div className="relative rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden">
        <img
          src={images[index]}
          alt=""
          className="w-full h-64 md:h-96 object-contain"
        />
        {images.length > 1 && (
          <>
            <button
              type="button"
              onClick={prev}
              aria-label="Previous image"
              className="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center text-[#01526D] hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              type="button"
              onClick={next}
              aria-label="Next image"
              className="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white border border-gray-200 shadow-sm flex items-center justify-center text-[#01526D] hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
            >
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </>
        )}
      </div>

      {images.length > 1 && (
        <div className="flex gap-2 overflow-x-auto pb-1 mt-4">
          {images.map((src, i) => (
            <button
              key={i}
              type="button"
              onClick={() => setIndex(i)}
              className={`shrink-0 w-16 h-16 rounded-xl bg-gray-50 border overflow-hidden transition-colors ${
                i === index ? 'border-[#2FAB4B] ring-1 ring-[#2FAB4B]' : 'border-gray-100'
              }`}
            >
              <img src={src} alt="" className="w-full h-full object-contain" />
            </button>
          ))}
        </div>
      )}
    </div>
  )
}

export default function ProductDetails() {
  const { id } = useParams()
  const { t, language } = useLanguage()
  const isAr = language === 'ar'
  const [product, setProduct] = useState(null)
  const [similar, setSimilar] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState(false)
  const [orderProduct, setOrderProduct] = useState(null)

  useEffect(() => {
    setLoading(true)
    setError(false)
    fetch(`${API_URL}/products/${id}?lang=${language}`)
      .then((res) => {
        if (!res.ok) throw new Error('Not found')
        return res.json()
      })
      .then((data) => setProduct(data))
      .catch(() => setError(true))
      .finally(() => setLoading(false))
  }, [id, language])

  useEffect(() => {
    if (!product?.category_id) return
    fetch(`${API_URL}/products?categories[]=${product.category_id}&lang=${language}`)
      .then((res) => res.json())
      .then((data) => {
        const items = Array.isArray(data) ? data : data.data || []
        setSimilar(items.filter((p) => p.id !== product.id).slice(0, 4))
      })
      .catch(() => setSimilar([]))
  }, [product, language])

  const images = product?.image ? [product.image] : []

  return (
    <div className="min-h-screen bg-white flex flex-col">
      <Navbar />

      <main className="flex-1 pt-24 md:pt-28 pb-16 md:pb-20 px-5 lg:px-10">
        <div className="max-w-6xl mx-auto">
          {loading ? (
            <p className="text-center text-gray-500 py-16">{t('loading')}</p>
          ) : error || !product ? (
            <div className="text-center py-16">
              <p className="text-gray-500 mb-4">{t('notFound')}</p>
              <Link to="/" className="text-[#2FAB4B] hover:underline">
                {t('backToHome')}
              </Link>
            </div>
          ) : (
            <>
              {/* Title + breadcrumb */}
              <div className="text-center mb-8">
                <h1 className="text-3xl md:text-4xl font-bold text-[#01526D] mb-3">{t('productDetails')}</h1>
                <nav className="text-xs md:text-sm text-gray-500 flex flex-wrap items-center justify-center gap-x-1 gap-y-1" aria-label="Breadcrumb">
                  <Link to="/" className="hover:text-[#2FAB4B] transition-colors">{t('home')}</Link>
                  <span>/</span>
                  <Link to="/products" className="hover:text-[#2FAB4B] transition-colors">{t('products')}</Link>
                  {product.category && (
                    <>
                      <span>/</span>
                      <span>{product.category}</span>
                    </>
                  )}
                  <span>/</span>
                  <span className="text-[#01526D] font-medium">{product.name}</span>
                </nav>
              </div>

              {/* Product card */}
              <div className="bg-white border border-gray-100 rounded-3xl shadow-sm p-4 md:p-8 mb-12 md:mb-16">
                <div className={`flex ${isAr ? 'justify-end' : 'justify-start'} mb-3`}>
                  <span className="px-4 py-1 rounded-full bg-[#2FAB4B] text-white text-sm font-semibold">
                    {t('available')}
                  </span>
                </div>

                <div className={`mb-4 ${isAr ? 'text-right' : 'text-left'}`}>
                  {product.brand && (
                    <p className="text-base font-semibold text-[#2FAB4B] mb-1">{product.brand}</p>
                  )}
                  <h2 className="text-2xl md:text-3xl font-bold text-[#01526D]">{product.name}</h2>
                </div>

                <Gallery images={images} />

                <p className={`text-[var(--text-muted)] leading-relaxed mb-6 ${isAr ? 'text-right' : 'text-left'}`}>
                  {product.description || t('noDescription')}
                </p>

                <div className="flex flex-col sm:flex-row gap-3 md:gap-4 mb-6">
                  <button
                    type="button"
                    onClick={() => setOrderProduct(product)}
                    className="flex-1 inline-flex items-center justify-center gap-2 bg-[#2FAB4B] hover:bg-[#237a4a] text-white font-semibold px-6 py-3.5 rounded-full transition-colors shadow-lg"
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    {t('requestPrice')}
                  </button>

                  <Link
                    to="/products"
                    className="flex-1 inline-flex items-center justify-center gap-2 border border-gray-200 text-[#01526D] font-semibold px-6 py-3.5 rounded-full hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    {t('moreProducts')}
                  </Link>
                </div>

                <div className="flex flex-wrap gap-x-6 gap-y-2 text-sm text-[#01526D] font-medium justify-start">
                  <span className="inline-flex items-center gap-1.5">
                    <svg className="w-5 h-5 text-[#2FAB4B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {t('originalProduct')}
                  </span>
                  <span className="inline-flex items-center gap-1.5">
                    <svg className="w-5 h-5 text-[#2FAB4B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    {t('certifiedWarranty')}
                  </span>
                </div>
              </div>

              {/* Similar products */}
              {similar.length > 0 && (
                <section>
                  <div className={`inline-block mb-6 ${isAr ? 'me-auto' : 'ms-auto'}`}>
                    <p className="text-[#2FAB4B] font-semibold text-base md:text-lg mb-1 text-start">{t('similarProducts')}</p>
                    <span className="block w-12 h-0.5 bg-[#2FAB4B] rounded-full me-auto" />
                  </div>

                  <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
                    {similar.map((p) => (
                      <div key={p.id} className="bg-white rounded-2xl border border-gray-100 p-3 md:p-4 shadow-sm flex flex-col h-full">
                        <div className="rounded-xl bg-gray-50 overflow-hidden mb-3">
                          {p.image ? (
                            <img src={p.image} alt={p.name} className="w-full h-28 md:h-40 object-contain" />
                          ) : (
                            <div className="w-full h-28 md:h-40 flex items-center justify-center">
                              <svg className="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                            </div>
                          )}
                        </div>
                        <div className={`flex-1 flex flex-col ${isAr ? 'text-right' : 'text-left'}`}>
                          {p.brand && <p className="text-xs md:text-sm font-semibold text-[#2FAB4B] mb-1">{p.brand}</p>}
                          <h3 className="text-sm md:text-base font-bold text-[#01526D] mb-3">{p.name}</h3>
                          <Link
                            to={`/products/${p.id}`}
                            className="mt-auto w-full flex items-center justify-center gap-2 border border-gray-200 text-[#01526D] text-sm font-medium py-2 rounded-full hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
                          >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {t('viewDetails')}
                          </Link>
                        </div>
                      </div>
                    ))}
                  </div>
                </section>
              )}
            </>
          )}
        </div>
      </main>

      <Footer />

      {orderProduct && (
        <OrderModal product={orderProduct} onClose={() => setOrderProduct(null)} />
      )}
    </div>
  )
}
