# Diora Blockchain - Independent Web3 Ecosystem

Diora is a modern, EVM-compatible Layer 1 blockchain built for community-driven growth, transparency, and digital value creation.

## 🚀 Project Overview

Diora is a comprehensive Web3 ecosystem featuring:

- **EVM-Compatible Blockchain**: Full Ethereum Virtual Machine compatibility
- **Proof-of-Stake Consensus**: Energy-efficient with DPoS delegation
- **Native DIO Token**: Utility, governance, staking, and deflationary mechanics
- **Built-in DeFi**: Staking, DAO governance, reputation system
- **Modern Web Interface**: Phantom Wallet-style UI/UX
- **Blockchain Explorer**: Real-time network monitoring
- **Developer Tools**: API, SDK, CLI, and comprehensive documentation

## 📁 Project Structure

```
diora-blockchain/
├── core/                 # Core blockchain implementation
│   ├── blockchain.go      # Main blockchain logic
│   ├── state.go          # State management
│   └── types.go          # Core types and interfaces
├── consensus/            # PoS/DPoS consensus mechanism
│   └── pos.go           # Proof-of-Stake implementation
├── vm/                   # EVM implementation
│   └── evm.go           # Ethereum Virtual Machine
├── crypto/               # Cryptographic primitives
│   └── crypto.go        # Hashing, signatures, keys
├── p2p/                  # Peer-to-peer networking
├── api/                  # REST and WebSocket APIs
│   └── server.go        # API server implementation
├── cli/                  # Command-line interface
│   └── main.go          # CLI commands
├── web/                  # Official website
│   ├── app/             # Next.js app
│   ├── components/       # React components
│   └── lib/             # Utilities
├── explorer/             # Blockchain explorer
│   ├── app/             # Explorer interface
│   ├── components/       # Explorer components
│   └── hooks/           # React hooks
├── contracts/            # Smart contracts
│   ├── DIO.sol          # Native token contract
│   └── NFT.sol          # NFT marketplace contract
├── sdk/                  # Developer SDK
├── docs/                 # Documentation
└── tests/                # Test suite
```

## 🛠️ Technology Stack

### Core Blockchain
- **Language**: Go (Golang)
- **Consensus**: Proof-of-Stake with DPoS delegation
- **VM**: EVM-compatible with optimizations
- **Database**: LevelDB for state storage
- **Networking**: libp2p for P2P communication

### Web Interface
- **Framework**: Next.js 14 with TypeScript
- **Styling**: TailwindCSS with custom dark theme
- **Web3**: Wagmi, RainbowKit, ethers.js
- **Animations**: Framer Motion
- **Icons**: Heroicons

### API & Infrastructure
- **REST API**: Gin framework with WebSocket support
- **CLI**: Cobra with Viper configuration
- **Documentation**: Markdown with comprehensive guides

## 🌐 Key Features

### Blockchain Features
- **6-second block times** with instant finality
- **1000+ TPS** throughput capacity
- **Low gas fees** under $0.01
- **EVM compatibility** for seamless dApp migration
- **Modular architecture** supporting rollups and sharding

### DIO Tokenomics
- **Total Supply**: 1 billion DIO
- **Utility**: Governance, staking, transaction fees
- **Staking Rewards**: 10% APY base rate
- **Anti-Whale**: 5% max wallet, 2% max transaction
- **Tax System**: 6% total (2% liquidity, 3% staking, 1% burn)
- **Deflationary**: Automatic burn mechanism

### Smart Contracts
- **ERC-20 Token**: DIO with advanced features
- **ERC-721/1155 NFT**: Marketplace with royalties
- **DAO Governance**: On-chain voting and proposals
- **Staking Contracts**: Flexible lock periods
- **Reputation System**: On-chain user scores

### Developer Experience
- **Comprehensive API**: REST + WebSocket
- **TypeScript SDK**: Full type safety
- **CLI Tools**: Account management, deployment
- **Documentation**: Guides, examples, API reference
- **Testnet**: Free faucet for testing

## 🚀 Quick Start

### Prerequisites
- Go 1.21+
- Node.js 18+
- Git

### Installation

```bash
# Clone the repository
git clone https://github.com/diora-blockchain/diora.git
cd diora

# Build the core node
make build

# Start a development node
./build/diora --dev

# Install web dependencies
cd web
npm install
npm start

# Start the API server
cd ../api
go run server.go

# Use the CLI
go run cli/main.go --help
```

### Basic Usage

#### Start a Node
```bash
./diora node start --network=testnet
```

#### Create Account
```bash
diora account create
```

#### Send Tokens
```bash
diora account send 0xFrom... 0xTo... 100
```

#### Stake Tokens
```bash
diora stake 1000 0xValidator...
```

#### Deploy Contract
```bash
diora contract deploy MyContract.sol
```

## 📖 Documentation

- [Getting Started Guide](./docs/getting-started.md)
- [API Reference](./docs/api.md)
- [Smart Contract Development](./docs/contracts.md)
- [CLI Usage](./docs/cli.md)
- [Network Parameters](./docs/network.md)

## 🔒 Security

- Regular security audits by reputable firms
- Multi-signature treasury management
- Rate limiting and DDoS protection
- Bug bounty program
- Comprehensive test coverage

## 🌍 Community

- **Discord**: [Coming Soon](#)
- **Telegram**: [@DioraFund](https://t.me/DioraFund)
- **Twitter**: [@DioraCrypto](https://twitter.com/DioraCrypto)
- **GitHub**: [github.com/DioraFund](https://github.com/DioraFund)

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](./CONTRIBUTING.md) for details.

### Development Workflow
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.

## 🎯 Roadmap

### Phase 1: Foundation (Q1 2024)
- [x] Core blockchain implementation
- [x] PoS consensus mechanism
- [x] Native DIO token
- [x] Basic web interface
- [x] Blockchain explorer
- [x] API and CLI tools

### Phase 2: Ecosystem (Q2 2024)
- [ ] Mobile wallet application
- [ ] Advanced DeFi protocols
- [ ] Cross-chain bridges
- [ ] Developer grant program
- [ ] Security audits

### Phase 3: Scaling (Q3 2024)
- [ ] Layer 2 rollups
- [ ] Sharding implementation
- [ ] Enterprise solutions
- [ ] Governance optimization
- [ ] Mainnet launch

## 📊 Network Status

- **Network**: Testnet
- **Chain ID**: 1337
- **Block Time**: 6 seconds
- **Gas Price**: ~1 Gwei
- **Active Validators**: 100
- **Total Staked**: 45.2M DIO

---

Built with ❤️ by the Diora community for a decentralized future.
