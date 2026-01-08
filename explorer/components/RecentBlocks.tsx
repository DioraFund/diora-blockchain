import { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import { CubeIcon, ClockIcon, FireIcon } from '@heroicons/react/24/outline'
import { useWebSocket } from '@/hooks/useWebSocket'
import { formatDistanceToNow } from 'date-fns'

interface Block {
  number: number
  hash: string
  timestamp: number
  transactions: number
  gasUsed: number
  gasLimit: number
  miner: string
}

export function RecentBlocks() {
  const [blocks, setBlocks] = useState<Block[]>([])
  const { subscribeToBlocks } = useWebSocket()

  useEffect(() => {
    // Fetch initial blocks
    fetchInitialBlocks()
    
    // Subscribe to new blocks
    subscribeToBlocks((newBlock) => {
      setBlocks(prev => [newBlock, ...prev.slice(0, 9)])
    })
  }, [])

  const fetchInitialBlocks = async () => {
    try {
      const response = await fetch('/api/blocks?limit=10')
      const data = await response.json()
      setBlocks(data.blocks || [])
    } catch (error) {
      console.error('Failed to fetch blocks:', error)
    }
  }

  const formatAddress = (address: string) => {
    return `${address.slice(0, 6)}...${address.slice(-4)}`
  }

  return (
    <div className="card">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-xl font-semibold flex items-center">
          <CubeIcon className="w-5 h-5 mr-2 text-diora-cyan" />
          Recent Blocks
        </h3>
        <a href="/blocks" className="text-sm text-diora-cyan hover:text-diora-blue transition-colors">
          View All
        </a>
      </div>

      <div className="space-y-4">
        {blocks.map((block, index) => (
          <motion.div
            key={block.hash}
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: index * 0.1 }}
            className="flex items-center justify-between p-4 bg-dark-surface/50 rounded-lg hover:bg-dark-surface transition-colors cursor-pointer"
            onClick={() => window.location.href = `/block/${block.number}`}
          >
            <div className="flex items-center space-x-4">
              <div className="text-center">
                <div className="text-2xl font-bold gradient-text">#{block.number}</div>
                <div className="text-xs text-dark-text-secondary flex items-center">
                  <ClockIcon className="w-3 h-3 mr-1" />
                  {formatDistanceToNow(new Date(block.timestamp * 1000), { addSuffix: true })}
                </div>
              </div>
            </div>

            <div className="flex-1 text-center">
              <div className="text-sm text-dark-text-secondary">Transactions</div>
              <div className="font-semibold">{block.transactions}</div>
            </div>

            <div className="flex-1 text-center">
              <div className="text-sm text-dark-text-secondary">Gas Used</div>
              <div className="font-semibold">{((block.gasUsed / block.gasLimit) * 100).toFixed(1)}%</div>
            </div>

            <div className="text-right">
              <div className="text-sm text-dark-text-secondary">Validator</div>
              <div className="font-mono text-sm">{formatAddress(block.miner)}</div>
            </div>
          </motion.div>
        ))}

        {blocks.length === 0 && (
          <div className="text-center py-8 text-dark-text-secondary">
            <CubeIcon className="w-12 h-12 mx-auto mb-4 opacity-50" />
            <p>Loading blocks...</p>
          </div>
        )}
      </div>
    </div>
  )
}
