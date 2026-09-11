import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import { API_URL } from '../config/api.js'
import OrderModal from './OrderModal.jsx'
import { useLanguage } from '../context/LanguageContext.jsx'

const ITEMS_PER_PAGE = 4

function LeafIcon() {
  return (
    <svg
      className="w-5 h-5 text-[#2FAB4B]"
      viewBox="0 0 24 24"
      fill="currentColor"
      aria-hidden="true"
    >
      <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z" />
    </svg>
  )
}

function PlaceholderImage({ className = '' }) {
  return (
    <div className={`bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl flex items-center justify-center border border-gray-100 ${className}`}>
      <svg
        className="w-16 h-16 text-gray-300"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth={1.5}
          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </div>
  )
}

function ProductCard({ product, onRequestPrice }) {
  const { t } = useLanguage()

  return (
    <div className="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow w-full max-w-[280px]">
      {product.image ? (
        <img
          src={product.image}
          alt={product.name}
          className="w-full h-44 object-contain rounded-2xl bg-gray-50"
        />
      ) : (
        <PlaceholderImage className="w-full h-44" />
      )}

      <div className="text-center mt-5">
        <h3 className="text-lg font-bold text-[#01526D] mb-2">
          {product.name}
        </h3>
        <p className="text-sm text-[var(--text-muted)] leading-relaxed mb-5">
          {product.description || t('productFallbackDesc')}
        </p>

        <button
          type="button"
          onClick={() => onRequestPrice(product)}
          className="w-full bg-[#2FAB4B] hover:bg-[#237a4a] text-white font-semibold py-2.5 rounded-full transition-colors mb-3"
        >
          {t('requestPrice')}
        </button>

        <Link
          to={`/products/${product.id}`}
          className="w-full flex items-center justify-center gap-2 border border-gray-200 text-[#01526D] font-medium py-2 rounded-full hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          {t('viewDetails')}
        </Link>
      </div>
    </div>
  )
}

function MobileProductCard({ product, onRequestPrice }) {
  const { t } = useLanguage()

  return (
    <div className="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm w-full">
      {product.image ? (
        <img
          src={product.image}
          alt={product.name}
          className="w-full h-56 object-contain rounded-2xl bg-gray-50"
        />
      ) : (
        <PlaceholderImage className="w-full h-56" />
      )}

      <div className="text-center mt-5">
        <h3 className="text-xl font-bold text-[#01526D] mb-2">
          {product.name}
        </h3>
        <p className="text-sm text-[var(--text-muted)] leading-relaxed mb-6">
          {product.description || t('productFallbackDesc')}
        </p>

        <button
          type="button"
          onClick={() => onRequestPrice(product)}
          className="w-full bg-[#2FAB4B] hover:bg-[#237a4a] text-white font-semibold py-3 rounded-full transition-colors mb-3"
        >
          {t('requestPrice')}
        </button>

        <Link
          to={`/products/${product.id}`}
          className="w-full flex items-center justify-center gap-2 border border-gray-200 text-[#01526D] font-medium py-2.5 rounded-full hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
          </svg>
          {t('viewDetails')}
        </Link>
      </div>
    </div>
  )
}

export default function Products({ selectedCategory, className = '' }) {
  const { t, language } = useLanguage()
  const isAr = language === 'ar'
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [showAll, setShowAll] = useState(false)
  const [page, setPage] = useState(1)
  const [mobileMainIndex, setMobileMainIndex] = useState(0)
  const [orderProduct, setOrderProduct] = useState(null)

  useEffect(() => {
    setLoading(true)
    fetch(`${API_URL}/products?lang=${language}`)
      .then((res) => res.json())
      .then((data) => setProducts(Array.isArray(data) ? data : []))
      .catch(() => setProducts([]))
      .finally(() => setLoading(false))
  }, [language])

  useEffect(() => {
    setPage(1)
    setShowAll(false)
    setMobileMainIndex(0)
  }, [selectedCategory])

  useEffect(() => {
    if (selectedCategory) {
      document.getElementById('products')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
  }, [selectedCategory])

  useEffect(() => {
    setMobileMainIndex(0)
  }, [page])

  const filteredProducts = selectedCategory
    ? products.filter((p) => p.category_id === selectedCategory)
    : products

  const totalPages = Math.ceil(filteredProducts.length / ITEMS_PER_PAGE)
  const visibleProducts = showAll
    ? filteredProducts
    : filteredProducts.slice((page - 1) * ITEMS_PER_PAGE, page * ITEMS_PER_PAGE)

  const canGoBack = page > 1
  const canGoForward = page < totalPages
  const centerItems = !showAll && filteredProducts.length <= ITEMS_PER_PAGE

  return (
    <section id="products" className={`py-16 md:py-20 px-5 lg:px-10 bg-white ${className}`}>
      <div className="max-w-7xl mx-auto">
        {/* Heading */}
        <div className="flex items-center justify-center gap-3 mb-8 md:mb-12">
          <h2 className="text-xl md:text-3xl font-bold text-[#2FAB4B]">
            {t('featuredProducts')}
          </h2>
          <LeafIcon />
        </div>

        {loading ? (
          <p className="text-center text-gray-500">{t('loading')}</p>
        ) : filteredProducts.length === 0 ? (
          <p className="text-center text-gray-500">
            {selectedCategory ? t('noProductsInSpecialty') : t('noProducts')}
          </p>
        ) : (
          <>
            <div className="relative">
              {/* Previous arrow desktop */}
              {!showAll && (
                <button
                  type="button"
                  onClick={() => setPage((p) => Math.max(1, p - 1))}
                  disabled={!canGoBack}
                  className={`hidden md:flex absolute ${isAr ? '-right-4 lg:-right-12' : '-left-4 lg:-left-12'} top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full border border-gray-200 bg-white text-[#01526D] items-center justify-center hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors disabled:opacity-40 disabled:cursor-not-allowed`}
                  aria-label={t('previous')}
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={isAr ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'} />
                  </svg>
                </button>
              )}

              {/* Next arrow desktop */}
              {!showAll && (
                <button
                  type="button"
                  onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                  disabled={!canGoForward}
                  className={`hidden md:flex absolute ${isAr ? '-left-4 lg:-left-12' : '-right-4 lg:-right-12'} top-1/2 -translate-y-1/2 z-10 w-10 h-10 rounded-full border border-gray-200 bg-white text-[#01526D] items-center justify-center hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors disabled:opacity-40 disabled:cursor-not-allowed`}
                  aria-label={t('next')}
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={isAr ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7'} />
                  </svg>
                </button>
              )}

              {/* Desktop Cards */}
              <div
                className={`hidden md:grid gap-6 ${
                  showAll
                    ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
                    : centerItems
                      ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 place-items-center'
                      : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4'
                }`}
              >
                {visibleProducts.map((product) => (
                  <ProductCard key={product.id} product={product} onRequestPrice={setOrderProduct} />
                ))}
              </div>

              {/* Mobile carousel */}
              <div className="md:hidden">
                {!showAll ? (
                  <>
                    {visibleProducts[mobileMainIndex] && (
                      <MobileProductCard product={visibleProducts[mobileMainIndex]} onRequestPrice={setOrderProduct} />
                    )}

                    {visibleProducts.length > 1 && (
                      <div className="flex items-center gap-3 mt-6">
                        <button
                          type="button"
                          onClick={() => setPage((p) => Math.max(1, p - 1))}
                          disabled={!canGoBack}
                          className="shrink-0 w-8 h-8 rounded-full border border-gray-200 bg-white text-[#01526D] flex items-center justify-center hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                          aria-label={t('previous')}
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={isAr ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'} />
                          </svg>
                        </button>

                        <div className="flex-1 flex items-center justify-center gap-3 overflow-x-auto scrollbar-hide">
                          {visibleProducts.map((product, index) => (
                            <button
                              key={product.id}
                              type="button"
                              onClick={() => setMobileMainIndex(index)}
                              className={`w-16 h-16 shrink-0 rounded-2xl border-2 p-1.5 flex items-center justify-center transition-colors ${
                                index === mobileMainIndex
                                  ? 'border-[#2FAB4B] bg-[#2FAB4B]/5'
                                  : 'border-[#2FAB4B]/30 bg-white'
                              }`}
                            >
                              {product.image ? (
                                <img
                                  src={product.image}
                                  alt={product.name}
                                  className="w-full h-full object-contain"
                                />
                              ) : (
                                <PlaceholderImage className="w-full h-full" />
                              )}
                            </button>
                          ))}
                        </div>

                        <button
                          type="button"
                          onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                          disabled={!canGoForward}
                          className="shrink-0 w-8 h-8 rounded-full border border-gray-200 bg-white text-[#01526D] flex items-center justify-center hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                          aria-label={t('next')}
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={isAr ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7'} />
                          </svg>
                        </button>
                      </div>
                    )}
                  </>
                ) : (
                  <div className="grid grid-cols-1 gap-6">
                    {visibleProducts.map((product) => (
                      <MobileProductCard key={product.id} product={product} onRequestPrice={setOrderProduct} />
                    ))}
                  </div>
                )}
              </div>
            </div>

            {/* Mobile dots */}
            {!showAll && totalPages > 1 && (
              <div className="flex md:hidden items-center justify-center gap-2 mt-4">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
                  <button
                    key={p}
                    type="button"
                    onClick={() => setPage(p)}
                    className={`w-2.5 h-2.5 rounded-full transition-colors ${
                      p === page ? 'bg-[#2FAB4B]' : 'bg-gray-300'
                    }`}
                    aria-label={`page ${p}`}
                  />
                ))}
              </div>
            )}

            {/* Dots desktop */}
            {!showAll && totalPages > 1 && (
              <div className="hidden md:flex items-center justify-center gap-2 mt-8">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
                  <button
                    key={p}
                    type="button"
                    onClick={() => setPage(p)}
                    className={`w-2.5 h-2.5 rounded-full transition-colors ${
                      p === page ? 'bg-[#2FAB4B]' : 'bg-gray-300'
                    }`}
                    aria-label={`page ${p}`}
                  />
                ))}
              </div>
            )}

            {/* View All */}
            {filteredProducts.length > ITEMS_PER_PAGE && (
              <div className="flex justify-center mt-8">
                <Link
                  to="/products"
                  className="inline-flex items-center gap-2 px-6 py-2.5 rounded-full border-2 border-[#2FAB4B] text-[#2FAB4B] font-semibold hover:bg-[#2FAB4B] hover:text-white transition-colors"
                >
                  {t('showAll')}
                </Link>
              </div>
            )}
          </>
        )}
      </div>

      {orderProduct && (
        <OrderModal product={orderProduct} onClose={() => setOrderProduct(null)} />
      )}
    </section>
  )
}
