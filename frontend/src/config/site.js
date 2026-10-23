// Company contact details shown across the site (from the Al-Hami Group company profile).
export const SITE = {
  company: { ar: 'مجموعة الحامي للصناعة وتعبئة المكسرات والمواد الغذائية', en: 'Al-Hami Group for Manufacturing & Packing Nuts and Food Products' },
  phones: ['0912110640', '0927568000', '0922829812'],
  whatsapp: '0912110640',
  whatsappDigits: '218912110640',
  phone: '0927568000',
  email: 'info@alhami.ly',
  licenseNumber: '36888',
  mapsUrl: 'https://maps.google.com/?q=Libya',
  // Optional hero video. Put the file in frontend/public/assets/ and keep these paths.
  heroVideo: '/assets/hero.mp4',
  heroPoster: '/assets/hero-poster.jpg',
  foundedYear: 2020,
}

export const whatsappLink = (text = '') =>
  `https://wa.me/${SITE.whatsappDigits}${text ? `?text=${encodeURIComponent(text)}` : ''}`
