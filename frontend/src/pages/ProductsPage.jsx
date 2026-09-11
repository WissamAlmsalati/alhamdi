import { useEffect, useMemo, useState } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import { API_URL } from '../config/api.js'
import { useLanguage } from '../context/LanguageContext.jsx'
import Navbar from '../components/Navbar.jsx'
import Footer from '../components/Footer.jsx'
import OrderModal from '../components/OrderModal.jsx'

const PER_PAGE = 9

function PlaceholderImage({ className = '' }) {
  return (
    <div className={`bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center border border-gray-100 ${className}`}>
      <svg className="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 2z" />
      </svg>
    </div>
  )
}

function ProductCard({ product, onRequestPrice, isAr }) {
  const { t } = useLanguage()

  return (
    <div className="bg-white rounded-2xl md:rounded-3xl border border-gray-100 p-3 md:p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col h-full">
      <div className="rounded-xl md:rounded-2xl bg-gray-50 overflow-hidden mb-3 md:mb-4">
        {product.image ? (
          <img
            src={product.image}
            alt={product.name}
            className="w-full h-32 md:h-52 object-contain"
          />
        ) : (
          <PlaceholderImage className="w-full h-32 md:h-52" />
        )}
      </div>

      <div className={`flex-1 flex flex-col ${isAr ? 'text-right' : 'text-left'}`}>
        {product.brand && (
          <p className="text-xs md:text-sm font-semibold text-[#2FAB4B] mb-1">{product.brand}</p>
        )}
        <h3 className="text-base md:text-xl font-bold text-[#01526D] mb-2">{product.name}</h3>
        <p className="text-xs md:text-sm text-[var(--text-muted)] leading-relaxed mb-4 flex-1 line-clamp-2 md:line-clamp-3">
          {product.description || t('productFallbackDesc')}
        </p>

        <button
          type="button"
          onClick={() => onRequestPrice(product)}
          className="w-full bg-[#2FAB4B] hover:bg-[#237a4a] text-white text-sm font-semibold py-2.5 rounded-full transition-colors mb-3"
        >
          {t('requestPrice')}
        </button>

        <Link
          to={`/products/${product.id}`}
          className="w-full flex items-center justify-center gap-2 border border-gray-200 text-[#01526D] text-sm font-medium py-2 rounded-full hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors"
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

function FiltersContent({ categories, brands, search, setSearch, setPage, selectedCategories, selectedBrands, toggleCategory, toggleBrand, hasFilters, clearFilters, t }) {
  return (
    <div className="h-full flex flex-col">
      <div className="flex items-center justify-between mb-5">
        <h3 className="font-bold text-[#01526D]">{t('filters')}</h3>
        {hasFilters && (
          <button
            type="button"
            onClick={clearFilters}
            className="text-sm text-[#2FAB4B] hover:underline"
          >
            {t('clearFilters')}
          </button>
        )}
      </div>

      <div className="mb-6">
        <input
          type="text"
          value={search}
          onChange={(e) => {
            setSearch(e.target.value)
            setPage(1)
          }}
          placeholder={t('searchProducts')}
          className="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none text-sm"
        />
      </div>

      <div className="flex-1 overflow-y-auto">
        <FilterGroup
          title={t('filterBySpecialty')}
          options={categories}
          selected={selectedCategories}
          onToggle={toggleCategory}
        />

        <FilterGroup
          title={t('filterByBrand')}
          options={brands}
          selected={selectedBrands}
          onToggle={toggleBrand}
        />
      </div>
    </div>
  )
}

function FilterGroup({ title, options, selected, onToggle }) {
  return (
    <div className="mb-6">
      <h4 className="font-bold text-[#01526D] mb-3">{title}</h4>
      <div className="space-y-2">
        {options.map((option) => {
          const checked = selected.includes(String(option.id))
          return (
            <label
              key={option.id}
              className="flex items-center gap-2 cursor-pointer text-sm text-gray-700 hover:text-[#2FAB4B] transition-colors"
            >
              <input
                type="checkbox"
                checked={checked}
                onChange={() => onToggle(String(option.id))}
                className="w-4 h-4 rounded border-gray-300 text-[#2FAB4B] focus:ring-[#2FAB4B]"
              />
              <span>{option.name}</span>
            </label>
          )
        })}
      </div>
    </div>
  )
}

function MobileFilterGroup({ title, options, selected, onToggle }) {
  const [open, setOpen] = useState(false)
  return (
    <div className="mb-3">
      <button
        type="button"
        onClick={() => setOpen(!open)}
        className="w-full flex items-center justify-between border border-gray-200 rounded-full px-5 py-4 bg-white"
      >
        <span className="flex items-center gap-2 text-base font-semibold text-[#01526D]">
          <svg className="w-5 h-5 text-[#2FAB4B]" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z" />
          </svg>
          {title}
        </span>
        <svg
          className={`w-5 h-5 text-[#01526D] transition-transform ${open ? 'rotate-180' : ''}`}
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      {open && (
        <div className="px-5 pt-4 pb-2 space-y-3">
          {options.map((option) => {
            const checked = selected.includes(String(option.id))
            return (
              <label
                key={option.id}
                className="flex items-center gap-2 cursor-pointer text-sm text-gray-700"
              >
                <input
                  type="checkbox"
                  checked={checked}
                  onChange={() => onToggle(String(option.id))}
                  className="w-4 h-4 rounded border-gray-300 text-[#2FAB4B] focus:ring-[#2FAB4B]"
                />
                <span>{option.name}</span>
              </label>
            )
          })}
        </div>
      )}
    </div>
  )
}

export default function ProductsPage() {
  const { t, language } = useLanguage()
  const isAr = language === 'ar'
  const [searchParams, setSearchParams] = useSearchParams()

  const [categories, setCategories] = useState([])
  const [brands, setBrands] = useState([])
  const [productsData, setProductsData] = useState({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 })
  const [loading, setLoading] = useState(true)
  const [orderProduct, setOrderProduct] = useState(null)

  const [search, setSearch] = useState(searchParams.get('search') || '')
  const [selectedCategories, setSelectedCategories] = useState(searchParams.getAll('categories[]'))
  const [selectedBrands, setSelectedBrands] = useState(searchParams.getAll('brands[]'))
  const [page, setPage] = useState(Number(searchParams.get('page')) || 1)

  useEffect(() => {
    fetch(`${API_URL}/categories?lang=${language}`)
      .then((res) => res.json())
      .then((data) => setCategories(Array.isArray(data) ? data : []))
      .catch(() => setCategories([]))

    fetch(`${API_URL}/brands?lang=${language}`)
      .then((res) => res.json())
      .then((data) => setBrands(Array.isArray(data) ? data : []))
      .catch(() => setBrands([]))
  }, [language])

  useEffect(() => {
    setLoading(true)

    const params = new URLSearchParams()
    params.set('lang', language)
    params.set('page', String(page))
    params.set('per_page', String(PER_PAGE))
    if (search.trim()) params.set('search', search.trim())
    selectedCategories.forEach((id) => params.append('categories[]', id))
    selectedBrands.forEach((id) => params.append('brands[]', id))

    fetch(`${API_URL}/products?${params.toString()}`)
      .then((res) => res.json())
      .then((data) => {
        if (data && Array.isArray(data.data)) {
          setProductsData(data)
        } else {
          setProductsData({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 })
        }
      })
      .catch(() => setProductsData({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0 }))
      .finally(() => setLoading(false))
  }, [language, search, selectedCategories, selectedBrands, page])

  useEffect(() => {
    const params = new URLSearchParams()
    if (search.trim()) params.set('search', search.trim())
    selectedCategories.forEach((id) => params.append('categories[]', id))
    selectedBrands.forEach((id) => params.append('brands[]', id))
    if (page > 1) params.set('page', String(page))
    setSearchParams(params, { replace: true })
  }, [search, selectedCategories, selectedBrands, page, setSearchParams])

  const toggleCategory = (id) => {
    setSelectedCategories((prev) =>
      prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
    )
    setPage(1)
  }

  const toggleBrand = (id) => {
    setSelectedBrands((prev) =>
      prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
    )
    setPage(1)
  }

  const hasFilters = search || selectedCategories.length > 0 || selectedBrands.length > 0

  const clearFilters = () => {
    setSearch('')
    setSelectedCategories([])
    setSelectedBrands([])
    setPage(1)
  }

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= productsData.last_page) {
      setPage(newPage)
      window.scrollTo({ top: 0, behavior: 'smooth' })
    }
  }

  const resultText = useMemo(() => {
    if (productsData.total === 0) return ''
    return t('showingResults')
      .replace('{{from}}', productsData.from)
      .replace('{{to}}', productsData.to)
      .replace('{{total}}', productsData.total)
  }, [productsData, t])

  return (
    <div className="min-h-screen bg-white flex flex-col">
      <Navbar />

      <main className="flex-1 pt-24 md:pt-28 pb-16 md:pb-20 px-5 lg:px-10">
        <div className="max-w-7xl mx-auto">
          {/* Breadcrumb */}
          <nav className={`text-sm text-gray-500 mb-4 text-center ${isAr ? 'lg:text-right' : 'lg:text-left'}`} aria-label="Breadcrumb">
            <Link to="/" className="hover:text-[#2FAB4B] transition-colors">{t('home')}</Link>
            <span className="mx-2">/</span>
            <span className="text-[#01526D] font-medium">{t('products')}</span>
          </nav>

          {/* Header */}
          <div className={`mb-10 md:mb-12 text-center ${isAr ? 'lg:text-right' : 'lg:text-left'}`}>
            <h1 className="text-3xl md:text-4xl font-bold text-[#01526D] mb-3">{t('products')}</h1>
            <p className="text-gray-600">{t('productsSubtitle')}</p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
            {/* Filters sidebar — always on the right on desktop */}
            <aside className={`hidden lg:block order-first lg:col-span-1 bg-gray-50 rounded-2xl p-5 md:p-6 ${isAr ? 'lg:order-1' : 'lg:order-2'}`}>
              <FiltersContent
                categories={categories}
                brands={brands}
                search={search}
                setSearch={setSearch}
                setPage={setPage}
                selectedCategories={selectedCategories}
                selectedBrands={selectedBrands}
                toggleCategory={toggleCategory}
                toggleBrand={toggleBrand}
                hasFilters={hasFilters}
                clearFilters={clearFilters}
                t={t}
              />
            </aside>

            {/* Products grid */}
            <section className={`order-last lg:col-span-3 ${isAr ? 'lg:order-2' : 'lg:order-1'}`}>
              {/* Mobile filters — search pill + accordion groups */}
              <div className="lg:hidden mb-6">
                <input
                  type="text"
                  value={search}
                  onChange={(e) => {
                    setSearch(e.target.value)
                    setPage(1)
                  }}
                  placeholder={t('searchProducts')}
                  className="w-full px-6 py-4 mb-4 rounded-full border border-gray-200 focus:ring-2 focus:ring-[#2FAB4B] focus:border-[#2FAB4B] outline-none text-base text-center"
                />
                <MobileFilterGroup
                  title={t('filterBySpecialty')}
                  options={categories}
                  selected={selectedCategories}
                  onToggle={toggleCategory}
                />
                <MobileFilterGroup
                  title={t('filterByBrand')}
                  options={brands}
                  selected={selectedBrands}
                  onToggle={toggleBrand}
                />
                {hasFilters && (
                  <button
                    type="button"
                    onClick={clearFilters}
                    className="mt-3 text-sm text-[#2FAB4B] hover:underline"
                  >
                    {t('clearFilters')}
                  </button>
                )}
              </div>

              {loading ? (
                <p className="text-center text-gray-500 py-12">{t('loading')}</p>
              ) : productsData.data.length === 0 ? (
                <div className="text-center py-16 bg-gray-50 rounded-2xl">
                  <p className="text-gray-500">{t('noProductsFound')}</p>
                  {hasFilters && (
                    <button
                      type="button"
                      onClick={clearFilters}
                      className="mt-4 text-[#2FAB4B] font-medium hover:underline"
                    >
                      {t('clearFilters')}
                    </button>
                  )}
                </div>
              ) : (
                <>
                  <div className={`mb-5 text-sm text-gray-500 ${isAr ? 'text-right' : 'text-left'}`}>
                    {resultText}
                  </div>

                  <div className="grid grid-cols-2 xl:grid-cols-3 gap-3 md:gap-6">
                    {productsData.data.map((product) => (
                      <ProductCard
                        key={product.id}
                        product={product}
                        onRequestPrice={setOrderProduct}
                        isAr={isAr}
                      />
                    ))}
                  </div>

                  {/* Pagination */}
                  {productsData.last_page > 1 && (
                    <div className="mt-10 overflow-x-auto pb-2">
                      <div className={`flex items-center justify-center gap-2 min-w-max ${isAr ? 'flex-row-reverse' : ''}`}>
                        <button
                          type="button"
                          onClick={() => handlePageChange(productsData.current_page - 1)}
                          disabled={productsData.current_page === 1}
                          className="w-9 h-9 rounded-full border border-gray-200 bg-white text-[#01526D] flex items-center justify-center hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                          aria-label={t('previous')}
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={isAr ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'} />
                          </svg>
                        </button>

                        {Array.from({ length: productsData.last_page }, (_, i) => i + 1).map((p) => (
                          <button
                            key={p}
                            type="button"
                            onClick={() => handlePageChange(p)}
                            className={`min-w-[2.25rem] h-9 px-2 rounded-full text-sm font-medium transition-colors ${
                              p === productsData.current_page
                                ? 'bg-[#2FAB4B] text-white'
                                : 'border border-gray-200 bg-white text-[#01526D] hover:border-[#2FAB4B] hover:text-[#2FAB4B]'
                            }`}
                          >
                            {p}
                          </button>
                        ))}

                        <button
                          type="button"
                          onClick={() => handlePageChange(productsData.current_page + 1)}
                          disabled={productsData.current_page === productsData.last_page}
                          className="w-9 h-9 rounded-full border border-gray-200 bg-white text-[#01526D] flex items-center justify-center hover:border-[#2FAB4B] hover:text-[#2FAB4B] transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                          aria-label={t('next')}
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={isAr ? 'M15 19l-7-7 7-7' : 'M9 5l7 7-7 7'} />
                          </svg>
                        </button>
                      </div>
                    </div>
                  )}
                </>
              )}
            </section>
          </div>
        </div>
      </main>

      <Footer />

      {orderProduct && (
        <OrderModal product={orderProduct} onClose={() => setOrderProduct(null)} />
      )}
    </div>
  )
}
