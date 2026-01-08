import { useState, useEffect, useRef } from 'react'
import { io, Socket } from 'socket.io-client'

interface NetworkStats {
  totalTransactions: number
  activeValidators: number
  tps: number
  gasPrice: string
  networkHashrate: string
  difficulty: string
}

interface LatestBlock {
  number: number
  hash: string
  timestamp: number
  transactions: number
  gasUsed: number
  gasLimit: number
  miner: string
}

export function useWebSocket() {
  const [isConnected, setIsConnected] = useState(false)
  const [latestBlock, setLatestBlock] = useState<LatestBlock | null>(null)
  const [networkStats, setNetworkStats] = useState<NetworkStats | null>(null)
  const socketRef = useRef<Socket | null>(null)

  useEffect(() => {
    // Initialize WebSocket connection
    const socket = io('ws://localhost:8546', {
      transports: ['websocket'],
      reconnection: true,
      reconnectionAttempts: 5,
      reconnectionDelay: 1000,
    })

    socketRef.current = socket

    socket.on('connect', () => {
      console.log('Connected to Diora network')
      setIsConnected(true)
    })

    socket.on('disconnect', () => {
      console.log('Disconnected from Diora network')
      setIsConnected(false)
    })

    socket.on('newBlock', (block: LatestBlock) => {
      setLatestBlock(block)
    })

    socket.on('networkStats', (stats: NetworkStats) => {
      setNetworkStats(stats)
    })

    socket.on('error', (error) => {
      console.error('WebSocket error:', error)
    })

    // Request initial data
    socket.emit('getLatestBlock')
    socket.emit('getNetworkStats')

    return () => {
      socket.disconnect()
    }
  }, [])

  const subscribeToBlocks = (callback: (block: LatestBlock) => void) => {
    if (socketRef.current) {
      socketRef.current.on('newBlock', callback)
    }
  }

  const subscribeToTransactions = (callback: (tx: any) => void) => {
    if (socketRef.current) {
      socketRef.current.on('newTransaction', callback)
    }
  }

  return {
    isConnected,
    latestBlock,
    networkStats,
    subscribeToBlocks,
    subscribeToTransactions,
  }
}
