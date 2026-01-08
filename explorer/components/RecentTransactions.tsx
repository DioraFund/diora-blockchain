import { useState, useEffect } from 'react'
import { motion } from 'framer-motion'
import { 
  ArrowsRightLeftIcon, 
  ArrowUpIcon, 
  ArrowDownIcon,
  ClockIcon 
} from '@heroicons/react/24/outline'
import { useWebSocket } from '@/hooks/useWebSocket'
import { formatDistanceToNow } from 'date-fns'

interface Transaction {
  hash: string
  blockNumber: number
  timestamp: number
  from: string
  to: string
  value: string
  gasUsed: number
  gasPrice: string
  status: 'success' | 'pending' | 'failed'
}

export function RecentTransactions() {
  const [transactions, setTransactions] = useState<Transaction[]>([])
  const { subscribeToTransactions } = useWebSocket()

  useEffect(() => {
    // Fetch initial transactions
    fetchInitialTransactions()
    
    // Subscribe to new transactions
    subscribeToTransactions((newTx) => {
      setTransactions(prev => [newTx, ...prev.slice(0, 9)])
    })
  }, [])

  const fetchInitialTransactions = async () => {
    try {
      const response = await fetch('/api/transactions?limit=10')
      const data = await response.json()
      setTransactions(data.transactions || [])
    } catch (error) {
      console.error('Failed to fetch transactions:', error)
    }
  }

  const formatAddress = (address: string) => {
    return `${address.slice(0, 6)}...${address.slice(-4)}`
  }

  const formatValue = (value: string) => {
    const ethValue = parseFloat(value) / 1e18
    return ethValue > 0.01 ? `${ethValue.toFixed(4)} DIO` : `${ethValue.toFixed(8)} DIO`
  }

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'success':
        return <ArrowUpIcon className="w-4 h-4 text-green-500" />
      case 'failed':
        return <ArrowDownIcon className="w-4 h-4 text-red-500" />
      default:
        return <ClockIcon className="w-4 h-4 text-yellow-500" />
    }
  }

  return (
    <div className="card">
      <div className="flex items-center justify-between mb-6">
        <h3 className="text-xl font-semibold flex items-center">
          <ArrowsRightLeftIcon className="w-5 h-5 mr-2 text-diora-cyan" />
          Recent Transactions
        </h3>
        <a href="/transactions" className="text-sm text-diora-cyan hover:text-diora-blue transition-colors">
          View All
        </a>
      </div>

      <div className="space-y-3">
        {transactions.map((tx, index) => (
          <motion.div
            key={tx.hash}
            initial={{ opacity: 0, x: 20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: index * 0.1 }}
            className="flex items-center justify-between p-4 bg-dark-surface/50 rounded-lg hover:bg-dark-surface transition-colors cursor-pointer"
            onClick={() => window.location.href = `/tx/${tx.hash}`}
          >
            <div className="flex items-center space-x-3">
              {getStatusIcon(tx.status)}
              <div>
                <div className="font-mono text-sm">{formatAddress(tx.hash)}</div>
                <div className="text-xs text-dark-text-secondary flex items-center">
                  <ClockIcon className="w-3 h-3 mr-1" />
                  {formatDistanceToNow(new Date(tx.timestamp * 1000), { addSuffix: true })}
                </div>
              </div>
            </div>

            <div className="flex-1 text-center">
              <div className="text-sm text-dark-text-secondary">From</div>
              <div className="font-mono text-sm">{formatAddress(tx.from)}</div>
            </div>

            <div className="flex items-center justify-center">
              <div className="p-1 bg-diora-purple/20 rounded">
                <ArrowsRightLeftIcon className="w-4 h-4 text-diora-purple" />
              </div>
            </div>

            <div className="flex-1 text-center">
              <div className="text-sm text-dark-text-secondary">To</div>
              <div className="font-mono text-sm">{formatAddress(tx.to)}</div>
            </div>

            <div className="text-right">
              <div className="font-semibold">{formatValue(tx.value)}</div>
              <div className="text-xs text-dark-text-secondary">
                {parseFloat(tx.gasPrice) / 1e9} Gwei
              </div>
            </div>
          </motion.div>
        ))}

        {transactions.length === 0 && (
          <div className="text-center py-8 text-dark-text-secondary">
            <ArrowsRightLeftIcon className="w-12 h-12 mx-auto mb-4 opacity-50" />
            <p>Loading transactions...</p>
          </div>
        )}
      </div>
    </div>
  )
}
