# Diora Blockchain Deployment Guide

This guide shows how to deploy the complete Diora blockchain ecosystem locally for development and testing.

## Prerequisites

### System Requirements
- **OS**: Linux, macOS, or Windows (WSL2)
- **RAM**: Minimum 8GB, Recommended 16GB
- **Storage**: Minimum 50GB free space
- **Network**: Stable internet connection

### Software Requirements
- **Go**: 1.21 or later
- **Node.js**: 18.0 or later
- **Git**: Latest version
- **Docker**: Optional, for containerized deployment

## Quick Start (Docker)

### 1. Clone Repository
```bash
git clone https://github.com/diora-blockchain/diora.git
cd diora
```

### 2. Using Docker Compose
```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Stop services
docker-compose down
```

### 3. Access Services
- **Blockchain Node**: http://localhost:8545
- **API Server**: http://localhost:8080
- **Web Interface**: http://localhost:3000
- **Blockchain Explorer**: http://localhost:3001
- **WebSocket**: ws://localhost:8546

## Manual Deployment

### 1. Build Core Blockchain

```bash
# Navigate to project root
cd /path/to/diora-blockchain

# Build the blockchain node
go build -o build/diora ./core

# Verify build
./build/diora --version
```

### 2. Initialize Blockchain

```bash
# Create data directory
mkdir -p ./data

# Initialize genesis block
./build/diora init --network=testnet

# Create validator account (optional)
./build/diora account create --save-validator
```

### 3. Start Blockchain Node

```bash
# Start in development mode
./build/diora node start \
  --network=testnet \
  --datadir=./data \
  --rpc-port=8545 \
  --ws-port=8546 \
  --validator-key=validator_key.json \
  --log-level=info

# Start with custom configuration
./build/diora node start \
  --config=./config.toml
```

### 4. Start API Server

```bash
# Navigate to API directory
cd api

# Install dependencies
go mod tidy

# Start API server
go run server.go \
  --blockchain-rpc=http://localhost:8545 \
  --port=8080 \
  --cors-enabled=true
```

### 5. Deploy Web Interface

```bash
# Navigate to web directory
cd web

# Install dependencies
npm install

# Configure environment
cp .env.example .env.local
# Edit .env.local with your configuration

# Start development server
npm run dev

# Or build for production
npm run build
npm start
```

### 6. Deploy Blockchain Explorer

```bash
# Navigate to explorer directory
cd explorer

# Install dependencies
npm install

# Configure environment
cp .env.example .env.local

# Start development server
npm run dev

# Or build for production
npm run build
npm start
```

## Configuration Files

### 1. Blockchain Configuration (config.toml)

```toml
[network]
chain_id = 1337
network_id = 1
block_time = 6  # seconds
gas_limit = 30000000
min_gas_price = 1000000000  # 1 Gwei

[consensus]
stake_amount = 1000000  # 1 DIO
validator_count = 100
unbonding_period = 604800  # 7 days in seconds

[api]
rpc_port = 8545
ws_port = 8546
max_connections = 1000
rate_limit = 100  # requests per minute

[storage]
data_dir = "./data"
db_type = "leveldb"
cache_size = 1024  # MB

[logging]
level = "info"
file = "./logs/diora.log"
max_size = 100  # MB
```

### 2. API Configuration (.env)

```bash
# API Server
PORT=8080
HOST=localhost
CORS_ENABLED=true
RATE_LIMIT=100

# Blockchain Connection
BLOCKCHAIN_RPC_URL=http://localhost:8545
BLOCKCHAIN_WS_URL=ws://localhost:8546

# Security
JWT_SECRET=your-secret-key
API_KEY_REQUIRED=false

# Features
ENABLE_WEBSOCKET=true
ENABLE_METRICS=true
METRICS_PORT=9090
```

### 3. Web Interface Configuration (.env.local)

```bash
# Next.js Configuration
NEXT_PUBLIC_CHAIN_ID=1337
NEXT_PUBLIC_NETWORK_NAME="Diora Testnet"
NEXT_PUBLIC_RPC_URL=http://localhost:8545
NEXT_PUBLIC_WS_URL=ws://localhost:8546
NEXT_PUBLIC_API_URL=http://localhost:8080

# Wallet Configuration
NEXT_PUBLIC_WALLET_CONNECT_PROJECT_ID=diora-testnet
NEXT_PUBLIC_INFURA_ID=your-infura-id

# Features
NEXT_PUBLIC_ENABLE_TESTNET=true
NEXT_PUBLIC_ENABLE_FAUCET=true
```

## Service Management

### 1. Using Systemd (Linux)

```bash
# Create service file
sudo nano /etc/systemd/system/diora-node.service
```

```ini
[Unit]
Description=Diora Blockchain Node
After=network.target

[Service]
Type=simple
User=diora
WorkingDirectory=/home/diora/diora-blockchain
ExecStart=/home/diora/diora-blockchain/build/diora node start --config=/home/diora/diora-blockchain/config.toml
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start service
sudo systemctl enable diora-node
sudo systemctl start diora-node

# Check status
sudo systemctl status diora-node

# View logs
sudo journalctl -u diora-node -f
```

### 2. Using PM2

```bash
# Install PM2
npm install -g pm2

# Start blockchain node
pm2 start build/diora --name diora-node -- --node-start --config ./config.toml

# Start API server
pm2 start api/server.js --name diora-api -- --port 8080

# Start web interface
pm2 start web --name diora-web --cwd ./web -- npm start

# Start explorer
pm2 start explorer --name diora-explorer --cwd ./explorer -- npm start

# List all processes
pm2 list

# Stop all processes
pm2 stop all
```

### 3. Using Docker

```bash
# Build Docker images
docker build -t diora-node ./core
docker build -t diora-api ./api
docker build -t diora-web ./web
docker build -t diora-explorer ./explorer

# Run individual containers
docker run -d --name diora-node -p 8545:8545 -v $(pwd)/data:/data diora-node
docker run -d --name diora-api -p 8080:8080 --link diora-node:node diora-api
docker run -d --name diora-web -p 3000:3000 --link diora-api:api diora-web
docker run -d --name diora-explorer -p 3001:3001 --link diora-api:api diora-explorer
```

## Docker Compose Configuration

### docker-compose.yml

```yaml
version: '3.8'

services:
  # Blockchain Node
  diora-node:
    build:
      context: .
      dockerfile: core/Dockerfile
    container_name: diora-node
    ports:
      - "8545:8545"
      - "8546:8546"
    volumes:
      - ./data:/app/data
      - ./config:/app/config
    environment:
      - DIORA_NETWORK=testnet
      - DIORA_LOG_LEVEL=info
    networks:
      - diora-network
    restart: unless-stopped

  # API Server
  diora-api:
    build:
      context: .
      dockerfile: api/Dockerfile
    container_name: diora-api
    ports:
      - "8080:8080"
    environment:
      - BLOCKCHAIN_RPC_URL=http://diora-node:8545
      - BLOCKCHAIN_WS_URL=ws://diora-node:8546
      - PORT=8080
      - CORS_ENABLED=true
    depends_on:
      - diora-node
    networks:
      - diora-network
    restart: unless-stopped

  # Web Interface
  diora-web:
    build:
      context: ./web
      dockerfile: Dockerfile
    container_name: diora-web
    ports:
      - "3000:3000"
    environment:
      - NEXT_PUBLIC_RPC_URL=http://diora-node:8545
      - NEXT_PUBLIC_API_URL=http://diora-api:8080
      - NEXT_PUBLIC_CHAIN_ID=1337
    depends_on:
      - diora-api
    networks:
      - diora-network
    restart: unless-stopped

  # Blockchain Explorer
  diora-explorer:
    build:
      context: ./explorer
      dockerfile: Dockerfile
    container_name: diora-explorer
    ports:
      - "3001:3001"
    environment:
      - NEXT_PUBLIC_API_URL=http://diora-api:8080
      - NEXT_PUBLIC_RPC_URL=http://diora-node:8545
    depends_on:
      - diora-api
    networks:
      - diora-network
    restart: unless-stopped

  # Reverse Proxy (Optional)
  nginx:
    image: nginx:alpine
    container_name: diora-nginx
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - diora-web
      - diora-explorer
    networks:
      - diora-network
    restart: unless-stopped

networks:
  diora-network:
    driver: bridge

volumes:
  diora-data:
    driver: local
  diora-config:
    driver: local
```

## Monitoring and Logs

### 1. Log Management

```bash
# View blockchain logs
tail -f ./logs/diora.log

# View API logs
tail -f ./logs/api.log

# View web logs
npm run logs --prefix=web

# Rotate logs
logrotate -f /etc/logrotate.d/diora
```

### 2. Health Checks

```bash
# Check blockchain node health
curl http://localhost:8545/health

# Check API server health
curl http://localhost:8080/health

# Check web interface
curl http://localhost:3000

# Check explorer
curl http://localhost:3001

# System metrics
curl http://localhost:9090/metrics
```

### 3. Monitoring Setup

```bash
# Install monitoring tools
npm install -g prometheus grafana

# Start Prometheus
prometheus --config.file=./monitoring/prometheus.yml

# Start Grafana
grafana server --config=./monitoring/grafana.yml

# Access dashboards
# Prometheus: http://localhost:9090
# Grafana: http://localhost:3001
```

## Testing the Deployment

### 1. Node Connectivity

```bash
# Test RPC connection
curl -X POST -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}' \
  http://localhost:8545

# Test WebSocket connection
wscat -c ws://localhost:8546

# Test API endpoints
curl http://localhost:8080/api/v1/network/stats
curl http://localhost:8080/api/v1/block/latest
```

### 2. Web Interface Testing

```bash
# Open web interface
open http://localhost:3000

# Test wallet connection
# Use browser developer tools to test Web3 functionality

# Test transaction sending
# Use the web interface to send test transactions
```

### 3. Explorer Testing

```bash
# Open explorer
open http://localhost:3001

# Test block search
# Search for blocks, transactions, and addresses

# Test real-time updates
# Monitor WebSocket connections and live updates
```

## Troubleshooting

### Common Issues

#### 1. Port Conflicts
```bash
# Check port usage
netstat -tulpn | grep :8545
netstat -tulpn | grep :8080
netstat -tulpn | grep :3000

# Kill conflicting processes
sudo kill -9 $(lsof -ti:8545)
```

#### 2. Permission Issues
```bash
# Fix file permissions
sudo chown -R $USER:$USER ./data
sudo chmod -R 755 ./data

# Fix Docker permissions
sudo usermod -aG $USER docker
```

#### 3. Memory Issues
```bash
# Check memory usage
free -h
htop

# Increase swap space
sudo fallocate -l 4G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

#### 4. Network Issues
```bash
# Check firewall status
sudo ufw status
sudo firewall-cmd --list-all

# Open required ports
sudo ufw allow 8545
sudo ufw allow 8080
sudo ufw allow 3000
sudo ufw allow 3001
```

### Debug Mode

```bash
# Start blockchain in debug mode
./build/diora node start \
  --debug \
  --log-level=debug \
  --pprof=:6060

# Start API in debug mode
go run server.go \
  --debug=true \
  --log-level=debug

# Access profiling
# Go to http://localhost:6060/debug/pprof/
```

## Production Deployment

### 1. Security Considerations

- Use HTTPS in production
- Enable firewall rules
- Use environment variables for secrets
- Enable rate limiting
- Set up monitoring and alerting
- Regular security updates

### 2. Performance Optimization

- Use reverse proxy (nginx)
- Enable caching
- Optimize database settings
- Use load balancer for high availability
- Monitor resource usage

### 3. Backup Strategy

- Regular database backups
- Configuration backups
- Disaster recovery plan
- Test restore procedures

## Support

### Documentation
- [Full Documentation](./docs/)
- [API Reference](./docs/api.md)
- [Troubleshooting Guide](./docs/troubleshooting.md)

### Community
- [Discord](https://discord.gg/diora)
- [GitHub Issues](https://github.com/diora-blockchain/diora/issues)
- [Documentation](https://docs.diora.io)

---

For additional help, join our community or create an issue on GitHub.
