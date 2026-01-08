import './globals.css'
import { Inter } from 'next/font/google'
import { Providers } from './providers'
import { Toaster } from 'react-hot-toast'

const inter = Inter({ subsets: ['latin'] })

export const metadata = {
  title: 'Diora - Independent Web3 Ecosystem',
  description: 'Diora is a modern, EVM-compatible Layer 1 blockchain built for community-driven growth, transparency, and digital value creation.',
  keywords: ['blockchain', 'web3', 'crypto', 'diora', 'defi', 'nft', 'staking'],
  authors: [{ name: 'Diora Team' }],
  openGraph: {
    title: 'Diora - Independent Web3 Ecosystem',
    description: 'Modern EVM-compatible blockchain with PoS consensus, low fees, and community governance.',
    url: 'https://diora.io',
    siteName: 'Diora',
    images: [
      {
        url: '/og-image.png',
        width: 1200,
        height: 630,
        alt: 'Diora Blockchain',
      },
    ],
    locale: 'en_US',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Diora - Independent Web3 Ecosystem',
    description: 'Modern EVM-compatible blockchain with PoS consensus.',
    images: ['/og-image.png'],
  },
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en" className="dark">
      <body className={`${inter.className} bg-dark-bg text-dark-text antialiased`}>
        <Providers>
          {children}
          <Toaster
            position="top-right"
            toastOptions={{
              duration: 4000,
              style: {
                background: '#1a1a1a',
                color: '#fafafa',
                border: '1px solid #262626',
              },
              success: {
                iconTheme: {
                  primary: '#10b981',
                  secondary: '#1a1a1a',
                },
              },
              error: {
                iconTheme: {
                  primary: '#ef4444',
                  secondary: '#1a1a1a',
                },
              },
            }}
          />
        </Providers>
      </body>
    </html>
  )
}
