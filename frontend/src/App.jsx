import { BrowserRouter, Link, Route, Routes } from 'react-router-dom'
import { LanguageProvider, useLanguage } from './context/LanguageContext.jsx'
import { InquiryProvider } from './components/InquiryModal.jsx'
import Header from './components/Header.jsx'
import Footer from './components/Footer.jsx'
import { Intro, ScrollManager, ScrollProgress } from './components/Effects.jsx'
import Home from './pages/Home.jsx'
import ProductsPage from './pages/ProductsPage.jsx'
import ProductDetails from './pages/ProductDetails.jsx'
import AboutPage from './pages/AboutPage.jsx'
import ContactPage from './pages/ContactPage.jsx'

function NotFound() {
  const { t } = useLanguage()
  return (
    <div className="wrap state" style={{ paddingBlock: 120 }}>
      <h1 style={{ color: 'var(--text)', fontSize: 64 }}>404</h1>
      <p>{t('notFoundText')}</p>
      <Link className="btn" to="/"><span>{t('home')}</span></Link>
    </div>
  )
}

function Shell() {
  return (
    <>
      <Intro />
      <ScrollProgress />
      <ScrollManager />
      <div className="grain" aria-hidden="true" />
      <Header />
      <main id="main">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/products" element={<ProductsPage />} />
          <Route path="/products/:id" element={<ProductDetails />} />
          <Route path="/about" element={<AboutPage />} />
          <Route path="/contact" element={<ContactPage />} />
          <Route path="*" element={<NotFound />} />
        </Routes>
      </main>
      <Footer />
    </>
  )
}

export default function App() {
  return (
    <LanguageProvider>
      <BrowserRouter>
        <InquiryProvider>
          <Shell />
        </InquiryProvider>
      </BrowserRouter>
    </LanguageProvider>
  )
}
