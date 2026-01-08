# Getting Started Guide

Welcome to Diora Blockchain! This guide will help you get started with the Diora ecosystem.

## 🚀 Quick Start

### Prerequisites

- Go 1.21 or higher
- Node.js 18 or higher
- PHP 8.0 or higher (for explorer)
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/DioraFund/diora-blockchain.git
   cd diora-blockchain
   ```

2. **Build the blockchain**
   ```bash
   make build
   ```

3. **Start a development node**
   ```bash
   ./build/diora start --dev
   ```

4. **Start the explorer**
   ```bash
   cd explorer
   php -S localhost:8000
   ```

5. **Open your browser**
   ```
   http://localhost:8000
   ```

## 📚 Basic Concepts

### Blockchain Basics

Diora is an EVM-compatible Layer 1 blockchain with:
- **6-second block times**
- **Proof-of-Stake consensus**
- **Low gas fees**
- **Smart contract support**

### Native Token (DIO)

- **Symbol**: DIO
- **Decimals**: 18
- **Total Supply**: 1 billion DIO
- **Use Cases**: Governance, staking, transaction fees

## 🛠️ Development Tools

### CLI Commands

```bash
# Create account
diora account create

# Check balance
diora account balance <address>

# Send tokens
diora account send <from> <to> <amount>

# Stake tokens
diora stake <amount> <validator>

# Deploy contract
diora contract deploy <contract.sol>
```

### API Usage

```bash
# Get network status
curl http://localhost:8080/api/status

# Get latest blocks
curl http://localhost:8080/api/blocks

# Get transactions
curl http://localhost:8080/api/transactions
```

## 💼 Wallet Management

### Creating a Wallet

```bash
# Create new wallet
diora wallet create

# Import existing wallet
diora wallet import <private-key>

# List wallets
diora wallet list
```

### Using the Web Wallet

1. Open http://localhost:8000
2. Click "Connect Wallet"
3. Choose "Create New Wallet"
4. Save your private key securely
5. Start using your wallet

## 🔍 Using the Explorer

### Features

- **Real-time statistics**
- **Transaction search**
- **Block explorer**
- **Address tracking**
- **Smart contract verification**

### Navigation

- **Home**: Network overview and statistics
- **Blocks**: Latest blocks with details
- **Transactions**: Recent transactions
- **Contracts**: Smart contracts
- **Tokens**: Token information
- **Validators**: Network validators
- **Analytics**: Network analytics

## 📝 Smart Contracts

### Writing Contracts

Diora supports Ethereum-compatible smart contracts:

```solidity
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract MyContract {
    mapping(address => uint256) public balances;
    
    function deposit() public payable {
        balances[msg.sender] += msg.value;
    }
    
    function withdraw(uint256 amount) public {
        require(balances[msg.sender] >= amount);
        balances[msg.sender] -= amount;
        payable(msg.sender).transfer(amount);
    }
}
```

### Deploying Contracts

```bash
# Compile contract
solc MyContract.sol --bin --abi

# Deploy using CLI
diora contract deploy MyContract.sol

# Deploy using web interface
# 1. Go to Contracts page
# 2. Upload contract source
# 3. Click Deploy
```

## 🔧 Configuration

### Network Configuration

Edit `config.json` to adjust network parameters:

```json
{
  "network": {
    "chain_id": 1337,
    "block_time": 6000,
    "gas_limit": 30000000
  },
  "consensus": {
    "type": "pos",
    "validators": 42
  }
}
```

### Explorer Configuration

Edit `explorer/config.php` to adjust explorer settings:

```php
define('BLOCKCHAIN_API_URL', 'http://localhost:8080/api');
define('NETWORK_NAME', 'Diora Testnet');
define('CACHE_ENABLED', true);
```

## 🧪 Testing

### Running Tests

```bash
# Go tests
go test ./...

# JavaScript tests
cd web && npm test

# Integration tests
go test -tags=integration ./tests/...
```

### Writing Tests

```go
func TestBlockCreation(t *testing.T) {
    blockchain := NewBlockchain()
    block := blockchain.CreateBlock("test data")
    
    if block.Index != 1 {
        t.Errorf("Expected block index 1, got %d", block.Index)
    }
}
```

## 🚀 Deployment

### Local Deployment

```bash
# Build for production
make build-prod

# Start with production config
./build/diora start --config=config.prod.json
```

### Docker Deployment

```bash
# Build Docker image
docker build -t diora-blockchain .

# Run with Docker
docker run -p 8080:8080 diora-blockchain
```

### Cloud Deployment

See [DEPLOYMENT.md](../DEPLOYMENT.md) for cloud deployment instructions.

## 📊 Monitoring

### Health Checks

```bash
# Check node status
curl http://localhost:8080/api/health

# Check network status
curl http://localhost:8080/api/status
```

### Metrics

- **Block production rate**
- **Transaction throughput**
- **Network latency**
- **Validator performance**

## 🔍 Troubleshooting

### Common Issues

**Node won't start**
```bash
# Check logs
./diora logs

# Check configuration
./diora config validate
```

**Transactions not confirming**
```bash
# Check gas price
curl http://localhost:8080/api/gas-price

# Check network status
curl http://localhost:8080/api/status
```

**Explorer not loading**
```bash
# Check API connection
curl http://localhost:8080/api/status

# Check PHP logs
tail -f /var/log/php/error.log
```

### Getting Help

- Check [GitHub Issues](https://github.com/DioraFund/diora-blockchain/issues)
- Join [Telegram](https://t.me/DioraFund)
- Follow [Twitter](https://twitter.com/DioraCrypto)

## 🎯 Next Steps

1. **Explore the explorer** at http://localhost:8000
2. **Create a wallet** and make your first transaction
3. **Deploy a smart contract**
4. **Join the community** and contribute
5. **Build your dApp** on Diora

## 📚 Additional Resources

- [API Reference](./api.md)
- [Smart Contract Development](./contracts.md)
- [CLI Usage](./cli.md)
- [Network Parameters](./network.md)

---

Welcome to the Diora ecosystem! Happy building! 🚀
