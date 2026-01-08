# GitHub Setup Instructions

## 🚀 Как залить Diora Explorer на GitHub

### Шаг 1: Создайте репозиторий на GitHub

1. Перейдите на [GitHub](https://github.com)
2. Нажмите **"New repository"** (зеленая кнопка)
3. Настройте репозиторий:
   - **Repository name**: `diora-blockchain`
   - **Description**: `Professional Diora Blockchain Explorer with Web3 interface`
   - **Visibility**: Public или Private
   - **Не ставьте галочки** "Add a README file", "Add .gitignore", "Choose a license"
4. Нажмите **"Create repository"**

### Шаг 2: Свяжите локальный репозиторий с GitHub

После создания репозитория скопируйте URL и выполните команды:

```bash
# Перейдите в папку проекта
cd /Users/qwerty/CascadeProjects/diora-blockchain

# Добавьте удаленный репозиторий (замените YOUR_USERNAME на ваш никнейм)
git remote add origin https://github.com/YOUR_USERNAME/diora-blockchain.git

# Отправьте код на GitHub
git push -u origin main
```

### Шаг 3: Настройте GitHub Pages (для деплоя)

1. В репозитории на GitHub перейдите в **Settings**
2. В меню слева выберите **Pages**
3. В разделе "Build and deployment" выберите:
   - **Source**: Deploy from a branch
   - **Branch**: main
   - **Folder**: /(root) или /explorer
4. Нажмите **Save**

### Шаг 4: Добавьте README.md

Создайте красивый README.md файл:

```markdown
# 🌐 Diora Blockchain Explorer

Professional blockchain explorer for Diora network with modern Web3 interface.

## ✨ Features

- 🎨 **Professional Dark Theme** - Corporate design with high contrast
- 📊 **Real-time Statistics** - Network metrics and monitoring
- 💳 **Integrated Wallet** - Secure non-custodial wallet
- 🔍 **Advanced Search** - Transactions, blocks, addresses, contracts
- 📈 **Analytics** - Charts and network insights
- 👥 **Validators** - Staking and performance tracking
- 🪙 **Tokens & NFTs** - Complete token management
- 📱 **Responsive Design** - Works on all devices

## 🚀 Quick Start

### Prerequisites

- Go 1.21+
- PHP 8.0+
- Node.js 18+

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/YOUR_USERNAME/diora-blockchain.git
cd diora-blockchain
```

2. **Build the blockchain**
```bash
make build
```

3. **Start the blockchain node**
```bash
./build/diora start
```

4. **Start the explorer**
```bash
cd explorer
php -S localhost:8000
```

5. **Open in browser**
```
http://localhost:8000
```

## 📁 Project Structure

```
diora-blockchain/
├── explorer/              # PHP blockchain explorer
│   ├── index.php         # Main explorer interface
│   ├── api.php           # API endpoints
│   ├── pages/            # Explorer pages
│   └── wallet.php        # Wallet functions
├── web/                  # Next.js web interface
├── core/                 # Blockchain core
├── consensus/            # Proof of Stake
├── crypto/               # Cryptography
├── contracts/            # Smart contracts
├── vm/                   # Virtual Machine
└── main.go              # Application entry
```

## 🔧 Configuration

### Blockchain Configuration
Edit `config.json` to adjust:
- Network settings
- Consensus parameters
- Gas limits

### Explorer Configuration
Edit `explorer/config.php` to adjust:
- Database connection
- API endpoints
- Cache settings

## 🌐 API Documentation

### Blockchain API
- `GET /api/status` - Network status
- `GET /api/blocks` - Latest blocks
- `GET /api/transactions` - Latest transactions
- `GET /api/wallets` - Wallet list

### Explorer API
- `GET /api.php?action=stats` - Network statistics
- `GET /api.php?action=blocks` - Block data
- `GET /api.php?action=transactions` - Transaction data

## 🛡️ Security

- Rate limiting protection
- CSRF token validation
- Input sanitization
- Encrypted private keys
- Audit logging

## 📊 Monitoring

- Real-time TPS monitoring
- Network utilization tracking
- Validator performance metrics
- Gas price analysis

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Ethereum Foundation for inspiration
- Go-Ethereum for reference implementation
- Web3.js for blockchain interaction
- Tailwind CSS for styling

## 📞 Support

- 📧 Email: support@diora.io
- 💬 Discord: [Coming Soon](#)
- 🐦 Twitter: [@DioraCrypto](https://twitter.com/DioraCrypto)
- 📱 Telegram: [@DioraFund](https://t.me/DioraFund)

---

⭐ **Star this repository if it helped you!**
```

### Шаг 5: Деплой на VPS (опционально)

Для продакшена используйте Docker:

```bash
# Сборка Docker образа
docker build -t diora-explorer .

# Запуск с docker-compose
docker-compose up -d
```

### Шаг 6: Настройте CI/CD (опционально)

Создайте `.github/workflows/deploy.yml` для автоматического деплоя.

---

## 🎉 Готово!

После выполнения этих шагов ваш Diora Blockchain Explorer будет доступен на GitHub и готов к использованию!

### 📞 Если нужна помощь:

1. **Проверьте статус**: `git status`
2. **Просмотрите коммиты**: `git log --oneline`
3. **Отправьте изменения**: `git push origin main`

### 🔗 Полезные ссылки:

- [GitHub Documentation](https://docs.github.com)
- [Git Documentation](https://git-scm.com/doc)
- [Docker Hub](https://hub.docker.com)

---

**💡 Совет**: Добавьте в README скриншоты вашего эксплорера для лучшего представления!
