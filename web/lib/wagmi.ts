import { http, createConfig } from 'wagmi'
import { mainnet, sepolia, polygon, arbitrum, optimism } from 'wagmi/chains'

export const config = createConfig({
  chains: [mainnet, sepolia, polygon, arbitrum, optimism],
  client: ({ chain }) => ({
    transport: http(),
  }),
})

export { config as wagmiConfig }
