# Smart Contract Development

This guide covers smart contract development on the Diora blockchain, which is fully EVM-compatible.

## 🏗️ EVM Compatibility

Diora supports all standard Ethereum Virtual Machine (EVM) features:

- **Solidity** smart contracts
- **Vyper** smart contracts
- **ERC-20** tokens
- **ERC-721** NFTs
- **ERC-1155** multi-tokens
- **Standard Ethereum APIs**

## 📝 Writing Smart Contracts

### Solidity Example

```solidity
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.26;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

contract DioraToken is ERC20, Ownable {
    constructor(
        string memory name,
        string memory symbol,
        uint256 initialSupply
    ) ERC20(name, symbol) {
        _mint(msg.sender, initialSupply);
    }

    function mint(address to, uint256 amount) public onlyOwner {
        _mint(to, amount);
    }

    function burn(uint256 amount) public {
        _burn(msg.sender, amount);
    }
}
```

### Vyper Example

```vyper
# @version >=0.3.0

from vyper.interfaces import ERC20

implements: ERC20

name: public(String[32])
symbol: public(String[32])
decimals: public(uint8)
balanceOf: public(HashMap[address, uint256])
allowance: public(HashMap[address, HashMap[address, uint256]])
totalSupply: public(uint256)

event Transfer:
    sender: indexed(address)
    receiver: indexed(address)
    value: uint256

event Approval:
    owner: indexed(address)
    spender: indexed(address)
    value: uint256

def __init__(name: String[32], symbol: String[32], decimals: uint8, initialSupply: uint256):
    self.name = name
    self.symbol = symbol
    self.decimals = decimals
    self.totalSupply = initialSupply
    self.balanceOf[msg.sender] = initialSupply
    log Transfer(empty_address, msg.sender, initialSupply)

@external
def transfer(to: address, value: uint256) -> bool:
    self.balanceOf[msg.sender] -= value
    self.balanceOf[to] += value
    log Transfer(msg.sender, to, value)
    return True

@external
def approve(spender: address, value: uint256) -> bool:
    self.allowance[msg.sender][spender] = value
    log Approval(msg.sender, spender, value)
    return True

@external
def transferFrom(from_: address, to: address, value: uint256) -> bool:
    if self.allowance[from_][msg.sender] < value:
        raise "Insufficient allowance"
    
    self.allowance[from_][msg.sender] -= value
    self.balanceOf[from_] -= value
    self.balanceOf[to] += value
    log Transfer(from_, to, value)
    return True
```

## 🪙 Standard Token Contracts

### ERC-20 Token

```solidity
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.26;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/token/ERC20/extensions/ERC20Burnable.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

contract MyToken is ERC20, ERC20Burnable, Ownable {
    constructor(
        string memory name,
        string memory symbol,
        uint256 initialSupply
    ) ERC20(name, symbol) {
        _mint(msg.sender, initialSupply);
    }

    function mint(address to, uint256 amount) public onlyOwner {
        _mint(to, amount);
    }
}
```

### ERC-721 NFT

```solidity
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.26;

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/Counters.sol";

contract MyNFT is ERC721, ERC721URIStorage, Ownable {
    using Counters for Counters.Counter;
    Counters.Counter private _tokenIdCounter;

    constructor() ERC721("MyNFT", "MNFT") {}

    function safeMint(address to, string memory uri) public onlyOwner {
        uint256 tokenId = _tokenIdCounter.current();
        _tokenIdCounter.increment();
        _safeMint(to, tokenId);
        _setTokenURI(tokenId, uri);
    }

    function tokenURI(uint256 tokenId)
        public
        view
        override(ERC721, ERC721URIStorage)
        returns (string memory)
    {
        return super.tokenURI(tokenId);
    }

    function _burn(uint256 tokenId)
        internal
        override(ERC721, ERC721URIStorage)
    {
        super._burn(tokenId);
    }
}
```

### ERC-1155 Multi-Token

```solidity
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.26;

import "@openzeppelin/contracts/token/ERC1155/ERC1155.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

contract MyMultiToken is ERC1155, Ownable {
    constructor() ERC1155("https://api.example.com/api/token/{id}.json") {}

    function mint(
        address account,
        uint256 id,
        uint256 amount,
        bytes memory data
    ) public onlyOwner {
        _mint(account, id, amount, data);
    }

    function mintBatch(
        address to,
        uint256[] memory ids,
        uint256[] memory amounts,
        bytes memory data
    ) public onlyOwner {
        _mintBatch(to, ids, amounts, data);
    }
}
```

## 🔧 Development Tools

### Compiler Setup

```bash
# Install Solidity compiler
npm install -g solc

# Install Vyper compiler
pip install vyper

# Verify installation
solc --version
vyper --version
```

### Project Structure

```
contracts/
├── src/
│   ├── Token.sol
│   ├── NFT.sol
│   └── MultiToken.sol
├── test/
│   ├── Token.test.js
│   └── NFT.test.js
├── scripts/
│   ├── deploy.js
│   └── verify.js
├── hardhat.config.js
└── package.json
```

### Hardhat Configuration

```javascript
require("@nomicfoundation/hardhat-toolbox");

module.exports = {
  solidity: {
    version: "0.8.26",
    settings: {
      optimizer: {
        enabled: true,
        runs: 200
      }
    }
  },
  networks: {
    diora: {
      url: "http://localhost:8080",
      chainId: 1337,
      gasPrice: 20000000000,
      accounts: [
        "0x..." // Your private key
      ]
    }
  },
  etherscan: {
    apiKey: "your-api-key",
    customChains: [
      {
        network: "diora",
        chainId: 1337,
        urls: {
          apiURL: "http://localhost:8000/api",
          browserURL: "http://localhost:8000"
        }
      }
    ]
  }
};
```

## 🚀 Deployment

### CLI Deployment

```bash
# Compile contract
solc --bin --abi Token.sol -o build/

# Deploy contract
diora contract deploy Token.sol

# Deploy with constructor arguments
diora contract deploy Token.sol --args "MyToken" "MTK" "1000000000000000000000000"
```

### Hardhat Deployment

```javascript
// scripts/deploy.js
const { ethers } = require("hardhat");

async function main() {
  const Token = await ethers.getContractFactory("Token");
  const token = await Token.deploy("MyToken", "MTK", "1000000000000000000000000");
  await token.deployed();

  console.log("Token deployed to:", token.address);
}

main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });
```

```bash
# Deploy to Diora network
npx hardhat run scripts/deploy.js --network diora
```

### Web3.js Deployment

```javascript
const Web3 = require('web3');
const web3 = new Web3('http://localhost:8080');

const contractABI = [
  // ... ABI array
];

const contractBytecode = '0x...';

async function deploy() {
  const accounts = await web3.eth.getAccounts();
  const contract = new web3.eth.Contract(contractABI);
  
  const deployTx = contract.deploy({
    data: contractBytecode,
    arguments: ['MyToken', 'MTK', '1000000000000000000000000']
  });
  
  const deployedContract = await deployTx.send({
    from: accounts[0],
    gas: 2000000
  });
  
  console.log('Contract deployed to:', deployedContract.options.address);
  return deployedContract;
}
```

## 🧪 Testing

### Unit Tests with Hardhat

```javascript
// test/Token.test.js
const { expect } = require("chai");
const { ethers } = require("hardhat");

describe("Token", function () {
  it("Should have correct name and symbol", async function () {
    const Token = await ethers.getContractFactory("Token");
    const token = await Token.deploy("MyToken", "MTK", "1000000000000000000000000");
    await token.deployed();

    expect(await token.name()).to.equal("MyToken");
    expect(await token.symbol()).to.equal("MTK");
  });

  it("Should mint initial supply", async function () {
    const Token = await ethers.getContractFactory("Token");
    const token = await Token.deploy("MyToken", "MTK", "1000000000000000000000000");
    await token.deployed();

    const ownerBalance = await token.balanceOf(await token.owner());
    expect(await token.totalSupply()).to.equal(ownerBalance);
  });

  it("Should transfer tokens between accounts", async function () {
    const Token = await ethers.getContractFactory("Token");
    const token = await Token.deploy("MyToken", "MTK", "1000000000000000000000000");
    await token.deployed();

    const [owner, addr1] = await ethers.getSigners();

    await token.transfer(addr1.address, 50);
    const addr1Balance = await token.balanceOf(addr1.address);
    expect(addr1Balance).to.equal(50);
  });
});
```

```bash
# Run tests
npx hardhat test

# Run specific test
npx hardhat test test/Token.test.js

# Run tests with coverage
npx hardhat coverage
```

## 📊 Gas Optimization

### Gas Optimization Tips

1. **Use `uint256` instead of smaller uints**
2. **Pack struct variables efficiently**
3. **Use events instead of storage for logs**
4. **Minimize external calls**
5. **Use libraries for reusable code**

### Optimized Example

```solidity
// Gas-optimized contract
contract OptimizedContract {
    // Pack variables efficiently
    struct PackedData {
        uint128 amount1;
        uint128 amount2;
        bool flag1;
        bool flag2;
    }
    
    // Use events for logging
    event Transfer(address indexed from, address indexed to, uint256 value);
    
    // Use internal functions
    function _transfer(address from, address to, uint256 value) internal {
        // Implementation
        emit Transfer(from, to, value);
    }
    
    // Batch operations
    function batchTransfer(address[] calldata recipients, uint256[] calldata amounts) external {
        require(recipients.length == amounts.length, "Array length mismatch");
        
        for (uint256 i = 0; i < recipients.length; i++) {
            _transfer(msg.sender, recipients[i], amounts[i]);
        }
    }
}
```

## 🔍 Contract Verification

### Manual Verification

1. Go to the Contracts page on the explorer
2. Enter contract address
3. Upload source code
4. Select compiler version and settings
5. Click "Verify"

### Automated Verification

```bash
# Verify contract
diora contract verify <address> --source Token.sol --compiler-version 0.8.26

# Verify with constructor arguments
diora contract verify <address> --source Token.sol --args "MyToken" "MTK" "1000000000000000000000000"
```

### Hardhat Verification

```javascript
// scripts/verify.js
const { run } = require("hardhat");

async function main() {
  await run("verify:verify", {
    address: "0x...",
    constructorArguments: ["MyToken", "MTK", "1000000000000000000000000"],
  });
}

main()
  .then(() => process.exit(0))
  .catch((error) => {
    console.error(error);
    process.exit(1);
  });
```

```bash
npx hardhat run scripts/verify.js --network diora
```

## 🔒 Security Best Practices

### Security Checklist

- [ ] Use latest Solidity version
- [ ] Implement access controls
- [ ] Check for integer overflow/underflow
- [ ] Use Reentrancy Guard on external calls
- [ ] Validate all inputs
- [ ] Use SafeMath for arithmetic operations
- [ ] Implement emergency pause mechanism
- [ ] Add comprehensive tests

### Security Example

```solidity
// SPDX-License-Identifier: MIT
pragma solidity ^0.8.26;

import "@openzeppelin/contracts/security/ReentrancyGuard.sol";
import "@openzeppelin/contracts/security/Pausable.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/utils/math/SafeMath.sol";

contract SecureContract is ReentrancyGuard, Pausable, Ownable {
    using SafeMath for uint256;
    
    mapping(address => uint256) public balances;
    
    event Withdrawal(address indexed to, uint256 amount);
    
    function deposit() external payable {
        balances[msg.sender] = balances[msg.sender].add(msg.value);
    }
    
    function withdraw(uint256 amount) external nonReentrant whenNotPaused {
        require(balances[msg.sender] >= amount, "Insufficient balance");
        
        balances[msg.sender] = balances[msg.sender].sub(amount);
        
        (bool success, ) = msg.sender.call{value: amount}("");
        require(success, "Transfer failed");
        
        emit Withdrawal(msg.sender, amount);
    }
    
    function pause() external onlyOwner {
        _pause();
    }
    
    function unpause() external onlyOwner {
        _unpause();
    }
    
    receive() external payable {
        deposit();
    }
}
```

## 📚 Libraries and Frameworks

### OpenZeppelin

```solidity
import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/access/Ownable.sol";
import "@openzeppelin/contracts/security/ReentrancyGuard.sol";
```

### Custom Libraries

```solidity
library SafeMath {
    function add(uint256 a, uint256 b) internal pure returns (uint256) {
        uint256 c = a + b;
        require(c >= a, "SafeMath: addition overflow");
        return c;
    }
    
    function sub(uint256 a, uint256 b) internal pure returns (uint256) {
        require(b <= a, "SafeMath: subtraction underflow");
        uint256 c = a - b;
        return c;
    }
}
```

## 🌐 Interacting with Contracts

### Web3.js

```javascript
const contract = new web3.eth.Contract(ABI, contractAddress);

// Read contract state
const balance = await contract.methods.balanceOf(address).call();

// Write to contract
const tx = await contract.methods.transfer(to, amount).send({
  from: fromAddress,
  gas: 100000
});

// Listen to events
contract.events.Transfer({
  fromBlock: 0
}, function(error, event) {
  console.log(event);
});
```

### Ethers.js

```javascript
const contract = new ethers.Contract(contractAddress, ABI, signer);

// Read contract state
const balance = await contract.balanceOf(address);

// Write to contract
const tx = await contract.transfer(to, amount);

// Listen to events
contract.on("Transfer", (from, to, amount) => {
  console.log(`Transfer: ${from} -> ${to}, amount: ${amount}`);
});
```

## 🚨 Common Issues

### Gas Limit Exceeded

```solidity
// Bad: Too much computation in one transaction
function badFunction() external {
    for (uint256 i = 0; i < 1000; i++) {
        // Expensive operations
    }
}

// Good: Batch operations or use pagination
function goodFunction(uint256 start, uint256 end) external {
    require(end - start <= 100, "Too many operations");
    for (uint256 i = start; i < end; i++) {
        // Limited operations
    }
}
```

### Reentrancy

```solidity
// Bad: Vulnerable to reentrancy
function badWithdraw(uint256 amount) external {
    require(balances[msg.sender] >= amount);
    (bool success,) = msg.sender.call{value: amount}("");
    require(success);
    balances[msg.sender] -= amount; // State update after external call
}

// Good: Use ReentrancyGuard
function goodWithdraw(uint256 amount) external nonReentrant {
    require(balances[msg.sender] >= amount);
    balances[msg.sender] -= amount; // State update before external call
    (bool success,) = msg.sender.call{value: amount}("");
    require(success);
}
```

## 📖 Additional Resources

- [Solidity Documentation](https://docs.soliditylang.org/)
- [Vyper Documentation](https://vyper.readthedocs.io/)
- [OpenZeppelin Contracts](https://docs.openzeppelin.com/contracts/)
- [Hardhat Framework](https://hardhat.org/docs)
- [Ethereum Smart Contract Best Practices](https://consensys.github.io/smart-contract-best-practices/)

---

For more information, join our [Telegram](https://t.me/DioraFund) or follow us on [Twitter](https://twitter.com/DioraCrypto).
