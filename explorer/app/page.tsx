'use client'

import { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import { 
  MagnifyingGlassIcon,
  CubeIcon,
  ArrowsRightLeftIcon,
  ServerIcon,
  ChartBarIcon,
  ClockIcon,
  FireIcon
} from '@heroicons/react/24/outline'
import { useWebSocket } from '@/hooks/useWebSocket'
import { StatsCard } from '@/components/StatsCard'
import { RecentBlocks } from '@/components/RecentBlocks'
import { RecentTransactions } from '@/components/RecentTransactions'
import { NetworkStats } from '@/components/NetworkStats'

export default function ExplorerPage() {
  const [searchQuery, setSearchQuery] = useState('')
  const { isConnected, latestBlock, networkStats } = useWebSocket()

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    if (!searchQuery.trim()) return

    // Determine if it's a block, transaction, or address
    if (searchQuery.startsWith('0x') && searchQuery.length === 66) {
      // Transaction hash
      window.location.href = `/tx/${searchQuery}`
    } else if (searchQuery.startsWith('0x') && searchQuery.length === 42) {
      // Address
      window.location.href = `/address/${searchQuery}`
    } else if (/^\d+$/.test(searchQuery)) {
      // Block number
      window.location.href = `/block/${searchQuery}`
    }
  }

  return (
    <div className="min-h-screen bg-dark-bg text-dark-text">
      {/* Header */}
      <header className="glass border-b border-dark-border sticky top-0 z-40">
        <div className="container mx-auto px-4 py-4">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-8">
              <div className="flex items-center space-x-3">
                <div className="p-2 bg-gradient-to-r from-diora-purple to-diora-blue rounded-xl">
                  <CubeIcon className="w-6 h-6 text-white" />
                </div>
                <h1 className="text-2xl font-bold gradient-text">Diora Explorer</h1>
              </div>
              
              <nav className="hidden md:flex items-center space-x-6">
                <a href="/" className="nav-link-active">Overview</a>
                <a href="/blocks" className="nav-link">Blocks</a>
                <a href="/transactions" className="nav-link">Transactions</a>
                <a href="/validators" className="nav-link">Validators</a>
                <a href="/tokens" className="nav-link">Tokens</a>
                <a href="/nfts" className="nav-link">NFTs</a>
              </nav>
            </div>

            <div className="flex items-center space-x-4">
              <div className="flex items-center space-x-2">
                <div className={`w-2 h-2 rounded-full ${isConnected ? 'bg-green-500' : 'bg-red-500'} animate-pulse`} />
                <span className="text-sm text-dark-text-secondary">
                  {isConnected ? 'Connected' : 'Disconnected'}
                </span>
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* Search Section */}
      <section className="py-12 border-b border-dark-border">
        <div className="container mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="max-w-2xl mx-auto"
          >
            <h2 className="text-3xl font-bold text-center mb-8">
              <span className="gradient-text">Explore Diora Network</span>
            </h2>
            
            <form onSubmit={handleSearch} className="relative">
              <div className="relative">
                <MagnifyingGlassIcon className="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-dark-text-secondary" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search by address, transaction hash, or block number..."
                  className="w-full pl-12 pr-4 py-4 bg-dark-surface border border-dark-border rounded-xl text-dark-text placeholder-dark-text-secondary focus:outline-none focus:ring-2 focus:ring-diora-blue focus:border-transparent text-lg"
                />
              </div>
              <button
                type="submit"
                className="absolute right-2 top-1/2 transform -translate-y-1/2 btn-primary px-6 py-2"
              >
                Search
              </button>
            </form>

            <div className="mt-4 flex justify-center space-x-6 text-sm text-dark-text-secondary">
              <span>Search by:</span>
              <span className="text-diora-cyan">Address</span>
              <span>•</span>
              <span className="text-diora-cyan">Transaction</span>
              <span>•</span>
              <span className="text-diora-cyan">Block</span>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <StatsCard
              title="Latest Block"
              value={latestBlock?.number || 'Loading...'}
              icon={CubeIcon}
              trend="+1"
              href={`/block/${latestBlock?.number}`}
            />
            <StatsCard
              title="Total Transactions"
              value={networkStats?.totalTransactions?.toLocaleString() || 'Loading...'}
              icon={ArrowsRightLeftIcon}
              trend="+12.5%"
            />
            <StatsCard
              title="Active Validators"
              value={networkStats?.activeValidators || 'Loading...'}
              icon={ServerIcon}
              trend="+2"
              href="/validators"
            />
            <StatsCard
              title="Network TPS"
              value={networkStats?.tps || 'Loading...'}
              icon={ChartBarIcon}
              trend="+8.3%"
            />
          </div>
        </div>
      </section>

      {/* Main Content */}
      <section className="py-12">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <motion.div
              initial={{ opacity: 0, x: -20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.2 }}
            >
              <RecentBlocks />
            </motion.div>
            
            <motion.div
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              transition={{ delay: 0.4 }}
            >
              <RecentTransactions />
            </motion.div>
          </div>
        </div>
      </section>

      {/* Network Stats Section */}
      <section className="py-12 border-t border-dark-border">
        <div className="container mx-auto px-4">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.6 }}
          >
            <NetworkStats stats={networkStats} />
          </motion.div>
        </div>
      </section>

      {/* Footer */}
      <footer className="py-8 border-t border-dark-border">
        <div className="container mx-auto px-4">
          <div className="flex flex-col md:flex-row items-center justify-between">
            <div className="flex items-center space-x-3 mb-4 md:mb-0">
              <div className="p-2 bg-gradient-to-r from-diora-purple to-diora-blue rounded-lg">
                <CubeIcon className="w-5 h-5 text-white" />
              </div>
              <span className="text-lg font-semibold">Diora Explorer</span>
            </div>
            
            <div className="flex items-center space-x-6 text-sm text-dark-text-secondary">
              <a href="https://diora.io" className="hover:text-diora-cyan transition-colors">
                About
              </a>
              <a href="https://docs.diora.io" className="hover:text-diora-cyan transition-colors">
                Docs
              </a>
              <a href="https://github.com/diora-blockchain" className="hover:text-diora-cyan transition-colors">
                GitHub
              </a>
              <a href="https://discord.gg/diora" className="hover:text-diora-cyan transition-colors">
                Discord
              </a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}
