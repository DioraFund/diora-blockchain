# CLI Usage Guide

This guide covers the Command Line Interface (CLI) for the Diora blockchain.

## 🚀 Installation

### Build from Source

```bash
git clone https://github.com/DioraFund/diora-blockchain.git
cd diora-blockchain
make build
```

### Binary Installation

```bash
# Download latest release
curl -L https://github.com/DioraFund/diora-blockchain/releases/latest/download/diora-darwin-amd64 -o diora
chmod +x diora

# Move to PATH
sudo mv diora /usr/local/bin/
```

## 📋 Commands Overview

```bash
diora [command] [subcommand] [flags]
```

### Available Commands

- `node` - Node management
- `account` - Account management
- `wallet` - Wallet operations
- `contract` - Smart contract deployment
- `stake` - Staking operations
- `token` - Token operations
- `config` - Configuration management
- `version` - Version information
- `help` - Help documentation

## 🔧 Node Management

### Start Node

```bash
# Start with default settings
diora node start

# Start with custom config
diora node start --config /path/to/config.json

# Start in development mode
diora node start --dev

# Start with specific network
diora node start --network testnet
```

### Stop Node

```bash
# Stop gracefully
diora node stop

# Force stop
diora node stop --force
```

### Node Status

```bash
# Check node status
diora node status

# Detailed status
diora node status --detailed

# Check network connectivity
diora node status --network
```

### Node Configuration

```bash
# Show current config
diora node config

# Set config value
diora node config set key value

# Get config value
diora node config get key

# Reset config to defaults
diora node config reset
```

## 👛 Account Management

### Create Account

```bash
# Create new account
diora account create

# Create with name
diora account create --name "My Account"

# Create with password
diora account create --password

# Create and save to file
diora account create --save /path/to/account.json
```

### List Accounts

```bash
# List all accounts
diora account list

# List with balances
diora account list --balance

# List with details
diora account list --detailed
```

### Account Information

```bash
# Get account balance
diora account balance <address>

# Get account details
diora account info <address>

# Get account transactions
diora account transactions <address>

# Get account nonce
diora account nonce <address>
```

### Import/Export Account

```bash
# Import from private key
diora account import --private-key "0x..."

# Import from keystore
diora account import --keystore /path/to/keystore.json

# Export private key
diora account export <address> --private-key

# Export keystore
diora account export <address> --keystore /path/to/keystore.json
```

## 💼 Wallet Operations

### Create Wallet

```bash
# Create new wallet
diora wallet create

# Create with name
diora wallet create --name "My Wallet"

# Create with encryption
diora wallet create --encrypt
```

### List Wallets

```bash
# List all wallets
diora wallet list

# List with balances
diora wallet list --balance
```

### Wallet Operations

```bash
# Get wallet balance
diora wallet balance <address>

# Send transaction
diora wallet send <from> <to> <amount>

# Send with custom gas
diora wallet send <from> <to> <amount> --gas 21000 --gas-price 20000000000

# Send with password
diora wallet send <from> <to> <amount> --password

# Estimate gas
diora wallet estimate <from> <to> <amount>
```

## 📜 Smart Contracts

### Compile Contract

```bash
# Compile Solidity contract
diora contract compile Token.sol

# Compile with specific version
diora contract compile Token.sol --solc-version 0.8.26

# Compile with optimization
diora contract compile Token.sol --optimize --runs 200

# Compile to specific output directory
diora contract compile Token.sol --output /path/to/output
```

### Deploy Contract

```bash
# Deploy contract
diora contract deploy Token.sol

# Deploy with constructor arguments
diora contract deploy Token.sol --args "MyToken" "MTK" "1000000000000000000000000"

# Deploy with specific account
diora contract deploy Token.sol --from <address>

# Deploy with custom gas
diora contract deploy Token.sol --gas 2000000 --gas-price 20000000000
```

### Contract Interaction

```bash
# Call contract method
diora contract call <address> balanceOf <address>

# Send transaction to contract
diora contract send <address> transfer <to> <amount>

# Get contract ABI
diora contract abi <address>

# Get contract source
diora contract source <address>
```

### Contract Verification

```bash
# Verify contract
diora contract verify <address> --source Token.sol

# Verify with compiler settings
diora contract verify <address> --source Token.sol --compiler-version 0.8.26 --optimization true

# Verify with constructor arguments
diora contract verify <address> --source Token.sol --args "MyToken" "MTK" "1000000000000000000000000"
```

## 🎯 Staking Operations

### Stake Tokens

```bash
# Stake tokens
diora stake <amount> <validator>

# Stake with lock period
diora stake <amount> <validator> --lock-period 30

# Stake from specific account
diora stake <amount> <validator> --from <address>

# Stake with auto-compound
diora stake <amount> <validator> --compound
```

### Unstake Tokens

```bash
# Unstake tokens
diora unstake <amount> <validator>

# Unstake with penalty
diora unstake <amount> <validator> --accept-penalty

# Unstake from specific account
diora unstake <amount> <validator> --from <address>
```

### Staking Information

```bash
# Get staking info
diora stake info <address>

# Get validator rewards
diora stake rewards <validator>

# Get total staked
diora stake total

# List validators
diora stake validators
```

## 🪙 Token Operations

### Token Information

```bash
# Get token info
diora token info <address>

# Get token balance
diora token balance <address> <token>

# Get token holders
diora token holders <address>

# Get token transactions
diora token transactions <address>
```

### Token Operations

```bash
# Transfer tokens
diora token transfer <token> <to> <amount>

# Approve tokens
diora token approve <token> <spender> <amount>

# Transfer from
diora token transfer-from <token> <from> <to> <amount>
```

## ⚙️ Configuration

### Configuration Files

```bash
# Show config location
diora config path

# Show current config
diora config show

# Set config value
diora config set network.chain_id 1337

# Get config value
diora config get network.chain_id

# Reset config
diora config reset
```

### Environment Variables

```bash
# Set environment variable
export DIORA_DATA_DIR=/path/to/data
export DIORA_LOG_LEVEL=info
export DIORA_NETWORK_ID=1337

# Show environment
diora config env
```

## 📊 Monitoring

### Logs

```bash
# Show logs
diora logs

# Follow logs
diora logs --follow

# Show logs for specific module
diora logs --module blockchain

# Show logs with level
diora logs --level debug
```

### Metrics

```bash
# Show metrics
diora metrics

# Show specific metric
diora metrics tps

# Show metrics in JSON format
diora metrics --json

# Export metrics
diora metrics --export /path/to/metrics.json
```

### Health Check

```bash
# Health check
diora health

# Detailed health check
diora health --detailed

# Check specific component
diora health --component blockchain
```

## 🔍 Search and Query

### Search

```bash
# Search by address
diora search address <address>

# Search by transaction hash
diora search tx <hash>

# Search by block number
diora search block <number>

# Search by contract
diora search contract <address>
```

### Query

```bash
# Query blocks
diora query blocks --from 1000 --to 1100

# Query transactions
diora query transactions --from <address>

# Query contracts
diora query contracts --verified true
```

## 🔧 Development Tools

### Test Commands

```bash
# Run tests
diora test

# Run specific test
diora test blockchain

# Run tests with coverage
diora test --coverage

# Run integration tests
diora test --integration
```

### Build Commands

```bash
# Build project
diora build

# Build with optimization
diora build --optimize

# Build for production
diora build --prod

# Clean build
diora build --clean
```

### Lint Commands

```bash
# Lint code
diora lint

# Lint specific file
diora lint main.go

# Fix lint issues
diora lint --fix
```

## 📝 Examples

### Complete Workflow

```bash
# 1. Create account
diora account create --name "My Account"

# 2. Check balance
diora account balance <address>

# 3. Deploy contract
diora contract deploy Token.sol --args "MyToken" "MTK" "1000000000000000000000000"

# 4. Stake tokens
diora stake 1000000000000000000000 <validator>

# 5. Monitor status
diora node status --detailed
```

### Batch Operations

```bash
# Create multiple accounts
for i in {1..5}; do
  diora account create --name "Account $i"
done

# Check all balances
diora account list --balance

# Batch stake
diora stake 1000000000000000000000 <validator> --from <address1>
diora stake 1000000000000000000000 <validator> --from <address2>
```

## 🚨 Troubleshooting

### Common Issues

```bash
# Check node status
diora node status

# Check logs
diora logs --follow

# Check configuration
diora config show

# Health check
diora health --detailed
```

### Reset Commands

```bash
# Reset node data
diora node reset

# Reset configuration
diora config reset

# Clear cache
diora cache clear
```

## 📚 Help

### General Help

```bash
# Show help
diora help

# Show command help
diora account help

# Show subcommand help
diora account create help
```

### Examples

```bash
# Show examples
diora examples

# Show examples for specific command
diora examples account create
```

## 🔧 Advanced Usage

### Custom Scripts

```bash
# Run custom script
diora run /path/to/script.js

# Run inline script
diora run --js "console.log('Hello World')"
```

### Plugin System

```bash
# List plugins
diora plugin list

# Install plugin
diora plugin install <plugin-name>

# Remove plugin
diora plugin remove <plugin-name>
```

### RPC Commands

```bash
# Send RPC request
diora rpc eth_blockNumber

# Send custom RPC
diora rpc --method eth_getBalance --params '["0x...", "latest"]'
```

## 📖 Additional Resources

- [Getting Started Guide](./getting-started.md)
- [API Reference](./api.md)
- [Smart Contract Development](./contracts.md)
- [Network Parameters](./network.md)

---

For more information, join our [Telegram](https://t.me/DioraFund) or follow us on [Twitter](https://twitter.com/DioraCrypto).
