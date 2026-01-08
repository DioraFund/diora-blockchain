'use client'

import { useState, useEffect } from 'react'
import { useAccount, useConnect, useDisconnect } from 'wagmi'
import { Button } from './ui/Button'
import { Modal } from './ui/Modal'
import { WalletIcon, ArrowRightOnRectangleIcon } from '@heroicons/react/24/outline'

export function WalletConnect() {
  const { address, isConnected } = useAccount()
  const { connect, connectors, isPending } = useConnect()
  const { disconnect } = useDisconnect()
  const [showModal, setShowModal] = useState(false)

  const formatAddress = (addr: string) => {
    return `${addr.slice(0, 6)}...${addr.slice(-4)}`
  }

  if (isConnected && address) {
    return (
      <div className="flex items-center gap-4">
        <div className="glass px-4 py-2 rounded-lg">
          <span className="text-sm text-dark-text-secondary">Connected:</span>
          <span className="ml-2 font-mono">{formatAddress(address)}</span>
        </div>
        <Button
          variant="ghost"
          size="sm"
          onClick={() => disconnect()}
          className="flex items-center gap-2"
        >
          <ArrowRightOnRectangleIcon className="w-4 h-4" />
          Disconnect
        </Button>
      </div>
    )
  }

  return (
    <>
      <Button
        onClick={() => setShowModal(true)}
        className="flex items-center gap-2"
      >
        <WalletIcon className="w-5 h-5" />
        Connect Wallet
      </Button>

      <Modal
        isOpen={showModal}
        onClose={() => setShowModal(false)}
        title="Connect Wallet"
      >
        <div className="space-y-4">
          <p className="text-dark-text-secondary">
            Choose your preferred wallet to connect to Diora Network
          </p>
          
          <div className="space-y-2">
            {connectors.map((connector) => (
              <Button
                key={connector.uid}
                variant="secondary"
                className="w-full justify-start"
                onClick={() => {
                  connect({ connector })
                  setShowModal(false)
                }}
                disabled={isPending}
              >
                <div className="flex items-center gap-3">
                  <div className="w-8 h-8 bg-gradient-to-r from-diora-purple to-diora-blue rounded-lg flex items-center justify-center">
                    <WalletIcon className="w-4 h-4 text-white" />
                  </div>
                  <div className="text-left">
                    <div className="font-medium">{connector.name}</div>
                    <div className="text-sm text-dark-text-secondary">
                      {connector.type === 'injected' ? 'Browser Wallet' : connector.type}
                    </div>
                  </div>
                </div>
              </Button>
            ))}
          </div>

          <div className="pt-4 border-t border-dark-border">
            <div className="text-sm text-dark-text-secondary">
              <p className="mb-2">New to Web3?</p>
              <p>
                Learn more about wallets and how to get started with Diora Network.
              </p>
            </div>
          </div>
        </div>
      </Modal>
    </>
  )
}
