# Contributing to Diora Blockchain

Thank you for your interest in contributing to Diora Blockchain! This document provides guidelines and information for contributors.

## 🤝 How to Contribute

### Reporting Issues

- Use [GitHub Issues](https://github.com/DioraFund/diora-blockchain/issues) to report bugs
- Provide detailed information about the issue
- Include steps to reproduce
- Add screenshots if applicable

### Submitting Pull Requests

1. **Fork the repository**
   ```bash
   git clone https://github.com/your-username/diora-blockchain.git
   cd diora-blockchain
   ```

2. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```

3. **Make your changes**
   - Follow the coding standards
   - Add tests for new functionality
   - Update documentation

4. **Commit your changes**
   ```bash
   git commit -m "Add amazing feature"
   ```

5. **Push to your fork**
   ```bash
   git push origin feature/amazing-feature
   ```

6. **Create a Pull Request**
   - Provide a clear description
   - Reference relevant issues
   - Include screenshots if applicable

## 📝 Coding Standards

### Go Code Style

- Follow [Go Code Review Comments](https://github.com/golang/go/wiki/CodeReviewComments)
- Use `gofmt` to format code
- Use `golint` to check for style issues
- Add comments for public functions
- Write unit tests

### JavaScript/TypeScript Code Style

- Use TypeScript for new code
- Follow [ESLint](https://eslint.org/) rules
- Use Prettier for formatting
- Add JSDoc comments for functions
- Write tests with Jest

### PHP Code Style

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) standard
- Use meaningful variable names
- Add PHPDoc comments
- Write clean, readable code

## 🧪 Testing

### Running Tests

```bash
# Go tests
go test ./...

# JavaScript tests
npm test

# PHP tests (if available)
php vendor/bin/phpunit
```

### Test Coverage

- Aim for >80% code coverage
- Write tests for new features
- Update tests when fixing bugs
- Use descriptive test names

## 📚 Documentation

### Updating Documentation

- Update README.md for major changes
- Add inline comments for complex code
- Update API documentation
- Add examples for new features

### Documentation Style

- Use clear, concise language
- Include code examples
- Add screenshots for UI changes
- Keep documentation up-to-date

## 🏗️ Development Setup

### Prerequisites

- Go 1.21+
- Node.js 18+
- PHP 8.0+
- Git

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/DioraFund/diora-blockchain.git
   cd diora-blockchain
   ```

2. **Install dependencies**
   ```bash
   # Go modules
   go mod download
   
   # Node.js
   cd web && npm install
   
   # PHP (if needed)
   composer install
   ```

3. **Run tests**
   ```bash
   go test ./...
   ```

4. **Start development**
   ```bash
   # Start blockchain node
   ./build/diora start
   
   # Start web interface
   cd web && npm start
   
   # Start explorer
   cd explorer && php -S localhost:8000
   ```

## 🐛 Bug Reports

### Bug Report Template

```markdown
## Bug Description
Brief description of the bug

## Steps to Reproduce
1. Go to...
2. Click on...
3. See error

## Expected Behavior
What you expected to happen

## Actual Behavior
What actually happened

## Environment
- OS: [e.g. macOS, Windows, Linux]
- Go version: [e.g. 1.21.0]
- Node version: [e.g. 18.0.0]
- Browser: [e.g. Chrome, Firefox]

## Additional Context
Add any other context about the problem here
```

## 💡 Feature Requests

### Feature Request Template

```markdown
## Feature Description
Brief description of the feature

## Problem Statement
What problem does this feature solve?

## Proposed Solution
How should this feature work

## Alternatives Considered
What other approaches did you consider

## Additional Context
Add any other context about the feature here
```

## 🎯 Areas of Contribution

### High Priority

- [ ] Core blockchain improvements
- [ ] Security enhancements
- [ ] Performance optimizations
- [ ] Bug fixes

### Medium Priority

- [ ] Documentation improvements
- [ ] Test coverage
- [ ] UI/UX enhancements
- [ ] Developer tools

### Low Priority

- [ ] Code refactoring
- [ ] Minor feature additions
- [ ] Documentation typos

## 🏆 Recognition

Contributors will be recognized in:

- README.md contributors section
- Release notes
- Project website
- Community announcements

## 📧 Getting Help

- Create an issue for questions
- Join our [Telegram](https://t.me/DioraFund)
- Follow us on [Twitter](https://twitter.com/DioraCrypto)
- Check [Discord](#) (coming soon)

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.

## 🙏 Thank You

Thank you for contributing to Diora Blockchain! Your contributions help make the project better for everyone.

---

**Remember:** Be respectful, be helpful, and follow the code of conduct.
