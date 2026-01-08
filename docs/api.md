# API Reference

This document provides comprehensive information about the Diora Blockchain API.

## 🔗 Base URLs

### Blockchain API
```
http://localhost:8080/api
```

### Explorer API
```
http://localhost:8000/api.php
```

## 📊 Network Endpoints

### Get Network Status

```http
GET /api/status
```

**Response:**
```json
{
  "status": "running",
  "blocks": 12847,
  "transactions": 3847291,
  "validators": 42,
  "network_id": 1337,
  "gas_price": 20000000000,
  "block_time": 6000
}
```

### Get Latest Blocks

```http
GET /api/blocks
```

**Query Parameters:**
- `limit` (optional): Number of blocks to return (default: 50)
- `offset` (optional): Number of blocks to skip (default: 0)

**Response:**
```json
{
  "blocks": [
    {
      "index": 12847,
      "hash": "0x7f3a9c2d8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
      "timestamp": "2026-01-08T21:45:32Z",
      "data": "Transaction data",
      "previous_hash": "0x4c8e2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
      "transactions": 127
    }
  ]
}
```

### Get Specific Block

```http
GET /api/blocks/{block_number}
```

**Path Parameters:**
- `block_number`: Block number or hash

**Response:**
```json
{
  "index": 12847,
  "hash": "0x7f3a9c2d8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
  "timestamp": "2026-01-08T21:45:32Z",
  "data": "Transaction data",
  "previous_hash": "0x4c8e2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
  "transactions": [
    {
      "hash": "0x8f3c2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
      "from": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
      "to": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
      "amount": "1250000000000000000000",
      "gas": 21000,
      "gas_price": 20000000000,
      "timestamp": "2026-01-08T21:45:32Z"
    }
  ]
}
```

## 💼 Wallet Endpoints

### Get All Wallets

```http
GET /api/wallets
```

**Response:**
```json
{
  "wallets": [
    {
      "address": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
      "name": "Main Wallet",
      "balance": "1000000000000000000000",
      "nonce": 42,
      "created_at": "2026-01-01T12:00:00Z"
    }
  ]
}
```

### Create Wallet

```http
POST /api/wallets
```

**Request Body:**
```json
{
  "name": "My Wallet"
}
```

**Response:**
```json
{
  "wallet": {
    "address": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
    "name": "My Wallet",
    "private_key": "0x8f3c2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
    "public_key": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
    "balance": "0",
    "nonce": 0,
    "created_at": "2026-01-08T21:45:32Z"
  }
}
```

### Get Wallet Balance

```http
GET /api/wallets/{address}/balance
```

**Path Parameters:**
- `address`: Wallet address

**Response:**
```json
{
  "address": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
  "balance": "1000000000000000000000",
  "nonce": 42
}
```

### Send Transaction

```http
POST /api/wallets/{address}/send
```

**Path Parameters:**
- `address`: Sender address

**Request Body:**
```json
{
  "to": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
  "amount": "500000000000000000000"
}
```

**Response:**
```json
{
  "message": "Transaction sent successfully",
  "transaction": {
    "hash": "0x8f3c2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
    "from": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
    "to": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
    "amount": "500000000000000000000",
    "gas": 21000,
    "gas_price": 20000000000,
    "timestamp": "2026-01-08T21:45:32Z"
  }
}
```

## 📝 Transaction Endpoints

### Get All Transactions

```http
GET /api/transactions
```

**Query Parameters:**
- `limit` (optional): Number of transactions to return (default: 50)
- `offset` (optional): Number of transactions to skip (default: 0)
- `address` (optional): Filter by address

**Response:**
```json
{
  "transactions": [
    {
      "hash": "0x8f3c2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
      "from": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
      "to": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
      "amount": "1250000000000000000000",
      "gas": 21000,
      "gas_price": 20000000000,
      "timestamp": "2026-01-08T21:45:32Z",
      "block_number": 12847,
      "status": "success"
    }
  ]
}
```

### Get Specific Transaction

```http
GET /api/transactions/{hash}
```

**Path Parameters:**
- `hash`: Transaction hash

**Response:**
```json
{
  "hash": "0x8f3c2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
  "from": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
  "to": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
  "amount": "1250000000000000000000",
  "gas": 21000,
  "gas_price": 20000000000,
  "timestamp": "2026-01-08T21:45:32Z",
  "block_number": 12847,
  "status": "success",
  "logs": [],
  "events": []
}
```

## 🔍 Explorer API Endpoints

### Get Network Statistics

```http
GET /api.php?action=stats
```

**Response:**
```json
{
  "latest_block": 12847,
  "tps": 24.7,
  "avg_block_time": 2.1,
  "network_load": 67,
  "validators": 42,
  "total_transactions": 3847291,
  "tx_24h": 284,
  "gas_price": 20,
  "market_cap": 50000000,
  "circulating_supply": 1000000000000000000000000000,
  "staked_amount": 250000000000000000000000000,
  "mempool_size": 12
}
```

### Get Blocks with Pagination

```http
GET /api.php?action=blocks&page=1&limit=50
```

**Query Parameters:**
- `page`: Page number (default: 1)
- `limit`: Items per page (default: 50, max: 100)

**Response:**
```json
{
  "data": [
    {
      "number": 12847,
      "hash": "0x7f3a9c2d8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
      "timestamp": "2026-01-08T21:45:32Z",
      "miner": "0x8e2b4f5c6d7e8f9a0b1c2d3e4f5a6b7c8d9e0f1a2b3c4d5e6f7a8b9c0d1e2f3",
      "transactions": 127,
      "gas_used": 2670000,
      "gas_limit": 30000000,
      "difficulty": "N/A",
      "size": 15420,
      "parent_hash": "0x4c8e2a7b6c5d4e3f2a1b0c9d8e7f6a5b4c3d2e1f0a9b8c7d6e5f4a3b2c1d0e",
      "reward": "2 DIO"
    }
  ],
  "pagination": {
    "page": 1,
    "limit": 50,
    "total": 12847,
    "pages": 257,
    "has_next": true,
    "has_prev": false
  }
}
```

### Search

```http
GET /api.php?action=search&q={query}
```

**Query Parameters:**
- `q`: Search query (address, transaction hash, block number)

**Response:**
```json
{
  "query": "0x7d3a9b",
  "results": [
    {
      "type": "address",
      "address": "0x7d3a9b2c8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3",
      "name": "Main Wallet",
      "balance": "1000000000000000000000"
    }
  ],
  "count": 1
}
```

## 📊 Analytics Endpoints

### Get Network Analytics

```http
GET /api.php?action=analytics
```

**Response:**
```json
{
  "network_stats": {
    "total_blocks": 12847,
    "total_transactions": 3847291,
    "active_addresses": 15420,
    "network_hashrate": "1.2 TH/s",
    "difficulty": "2.5T"
  },
  "daily_stats": [
    {
      "date": "2026-01-08",
      "transactions": 5420,
      "blocks": 43200,
      "gas_used": 1080000000000,
      "active_addresses": 1250,
      "new_addresses": 89
    }
  ],
  "hourly_stats": [
    {
      "hour": "21:00",
      "tps": 24.7,
      "gas_price": 20,
      "block_time": 2.1
    }
  ],
  "gas_stats": {
    "current_gas_price": 20,
    "avg_gas_price_24h": 18.5,
    "gas_limit": 30000000,
    "gas_used_24h": 2400000000000000,
    "gas_efficiency": 92
  },
  "validator_stats": {
    "total_validators": 42,
    "active_validators": 40,
    "total_staked": 250000000000000000000000000,
    "avg_apr": "8.5%"
  }
}
```

## 🔧 WebSocket API

### Connect to WebSocket

```
ws://localhost:8080/ws
```

### Subscribe to Events

```json
{
  "action": "subscribe",
  "events": ["new_block", "new_transaction", "validator_update"]
}
```

### Event Messages

**New Block:**
```json
{
  "event": "new_block",
  "data": {
    "number": 12848,
    "hash": "0x9f2e8c4b5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2",
    "timestamp": "2026-01-08T21:45:38Z"
  }
}
```

**New Transaction:**
```json
{
  "event": "new_transaction",
  "data": {
    "hash": "0x2a7f9d8e4b5f6a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6e7f8a9b0c1d2e3f4",
    "from": "0x4b6c2e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2a3",
    "to": "0x8e3f9a2b4c5d6e7f8a9b0c1d2e3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2",
    "amount": "750000000000000000000"
  }
}
```

## 🚨 Error Handling

### Error Response Format

```json
{
  "error": "Error message",
  "code": 400,
  "details": "Additional error details"
}
```

### Common Error Codes

- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `429`: Rate Limit Exceeded
- `500`: Internal Server Error

### Rate Limiting

- **Blockchain API**: 1000 requests per hour
- **Explorer API**: 100 requests per minute
- **WebSocket**: 100 connections per IP

## 🔐 Authentication

### API Keys (Future Feature)

```http
GET /api/blocks?api_key=your_api_key
```

### JWT Authentication (Future Feature)

```http
Authorization: Bearer <jwt_token>
```

## 📝 Code Examples

### JavaScript/Node.js

```javascript
// Fetch network status
async function getNetworkStatus() {
  const response = await fetch('http://localhost:8080/api/status');
  const data = await response.json();
  return data;
}

// Create wallet
async function createWallet(name) {
  const response = await fetch('http://localhost:8080/api/wallets', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ name }),
  });
  const data = await response.json();
  return data;
}

// Send transaction
async function sendTransaction(from, to, amount) {
  const response = await fetch(`http://localhost:8080/api/wallets/${from}/send`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ to, amount }),
  });
  const data = await response.json();
  return data;
}
```

### Python

```python
import requests

# Get network status
def get_network_status():
    response = requests.get('http://localhost:8080/api/status')
    return response.json()

# Create wallet
def create_wallet(name):
    response = requests.post(
        'http://localhost:8080/api/wallets',
        json={'name': name}
    )
    return response.json()

# Send transaction
def send_transaction(from_address, to_address, amount):
    response = requests.post(
        f'http://localhost:8080/api/wallets/{from_address}/send',
        json={'to': to_address, 'amount': amount}
    )
    return response.json()
```

### Go

```go
package main

import (
    "encoding/json"
    "net/http"
)

type NetworkStatus struct {
    Status      string `json:"status"`
    Blocks      int    `json:"blocks"`
    TPS         float64 `json:"tps"`
    Validators  int    `json:"validators"`
}

func getNetworkStatus() (*NetworkStatus, error) {
    resp, err := http.Get("http://localhost:8080/api/status")
    if err != nil {
        return nil, err
    }
    defer resp.Body.Close()

    var status NetworkStatus
    err = json.NewDecoder(resp.Body).Decode(&status)
    if err != nil {
        return nil, err
    }

    return &status, nil
}
```

## 🧪 Testing

### API Testing with curl

```bash
# Test network status
curl http://localhost:8080/api/status

# Test wallet creation
curl -X POST http://localhost:8080/api/wallets \
  -H "Content-Type: application/json" \
  -d '{"name": "Test Wallet"}'

# Test transaction
curl -X POST http://localhost:8080/api/wallets/0x.../send \
  -H "Content-Type: application/json" \
  -d '{"to": "0x...", "amount": "1000000000000000000000"}'
```

### Postman Collection

Import the Postman collection from `docs/api-postman.json` for easy API testing.

## 📚 Additional Resources

- [Getting Started Guide](./getting-started.md)
- [Smart Contract Development](./contracts.md)
- [CLI Usage](./cli.md)
- [Network Parameters](./network.md)

---

For more information, join our [Telegram](https://t.me/DioraFund) or follow us on [Twitter](https://twitter.com/DioraCrypto).
