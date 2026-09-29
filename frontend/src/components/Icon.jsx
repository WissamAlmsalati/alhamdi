const PATHS = {
  menu: 'M4 7h16M4 12h16M4 17h16',
  close: 'M6 6l12 12M18 6L6 18',
  arrow: 'M5 12h14M13 6l6 6-6 6',
  search: 'M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zM20 20l-4-4',
  check: 'M20 6 9 17l-5-5',
  shield: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',
  box: 'M21 8 12 3 3 8v8l9 5 9-5zM3 8l9 5 9-5M12 13v8',
  phone: 'M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2',
  mail: 'M3 5h18v14H3zM3 7l9 6 9-6',
  pin: 'M12 22s8-7.6 8-13a8 8 0 0 0-16 0c0 5.4 8 13 8 13zM12 6a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
  whatsapp: 'M4 20l1.3-4A8 8 0 1 1 8 18.7zM9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-1 .8a4 4 0 0 1-1.8-1.8l.8-1-1-2z',
  clip: 'M21 12.5 12.6 21a5 5 0 0 1-7-7L14 5.5a3.3 3.3 0 0 1 4.7 4.7L10.3 18.6a1.7 1.7 0 0 1-2.4-2.4L15.5 8.6',
  volume: 'M4 9v6h4l5 4V5L8 9zM16 9a4 4 0 0 1 0 6M18.5 6.5a8 8 0 0 1 0 11',
  mute: 'M4 9v6h4l5 4V5L8 9zM17 9l5 6M22 9l-5 6',
  store: 'M4 10v10h16V10M3 10l2-6h14l2 6zM9 20v-5h6v5',
  cake: 'M4 21h16v-8H4zM4 16c2 1.5 4 1.5 6 0s4-1.5 6 0 4 1.5 4 0M12 13V8M12 4v1',
  cup: 'M4 8h13v5a6 6 0 0 1-6 6h-1a6 6 0 0 1-6-6zM17 10h2a2 2 0 0 1 0 4h-2M8 3v2M12 3v2',
  hotel: 'M3 21V5h18v16M3 13h18M8 9h.01M12 9h.01M16 9h.01M10 21v-4h4v4',
  home: 'M3 11.5 12 4l9 7.5M5 10v10h14V10',
  file: 'M6 3h8l4 4v14H6zM14 3v4h4',
  external: 'M14 4h6v6M20 4l-9 9M18 14v6H4V6h6',
  filter: 'M4 5h16l-6 8v6l-4-2v-4z',
}

export default function Icon({ name, className = '', ...rest }) {
  return (
    <svg className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.9" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true" {...rest}>
      <path d={PATHS[name]} />
    </svg>
  )
}
