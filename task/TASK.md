## Реалізуй АРІ, який дозволить підписатися на email-сповіщення про нові релізи обраного GitHub-репозиторію.

### Вимоги:
1. Сервіс має відповідати описаному API у вигляді Swagger-документації. Для зручного перегляду можна скористатися сервісом https://editor.swagger.io/. Змінювати контракти не можна.
2. Весь функціонал (API, Scanner, Notifier) має бути реалізований у межах одного сервісу — моноліту. Розділення на мікросервіси на даному етапі заборонено.
3. Всі дані для роботи додатка повинні зберігатися в базі даних. Також потрібно реалізувати виконання міграції структури БД при піднятті сервісу.
4. У репозиторії повинні бути Dockerfile та docker-compose.yml, які дозволяють запустити всю систему в Docker. З матеріалом щодо Docker необхідно ознайомитися самостійно.
5. Сервіс повинен регулярно перевіряти нові релізи для всіх активних підписок. При виявленні нового релізу — надсилати email підписнику. Для кожного репозиторію необхідно зберігати last_seen_tag і надсилати сповіщення лише якщо з'явився новий реліз.
6. При створенні підписки сервіс повинен перевіряти існування репозиторію через GitHub API. Формат параметра: owner/repo (наприклад, golang/go). Якщо репозиторій не знайдено — повертати 404. Якщо формат некоректний — 400.
7. Обробка помилок зовнішнього API. Сервіс повинен коректно обробляти 429 Too Many Requests від GitHub API (rate limit: 60 req/год без токена, 5000 з токеном).
8. Можна використовувати фреймворки, але лише «тонкі» рішення. Забороняється використання високорівневих фреймворків: Nest.js (Node.js), Revel або Fx (Go), Laravel (PHP). Дозволено: Fastify або Express (Node.js), Gin / Chi / net/http (Go), Slim або вбудовані можливості мови (PHP).
9. Наявність юніт-тестів на бізнес-логіку є обов'язковою вимогою. Інтеграційні тести — бонусом.
10. Також ти можеш додавати коментарі чи опис логіки виконання роботи в README.md документі. Правильна логіка може стати перевагою при оцінюванні, якщо ти не повністю виконаєш завдання.


## Ти можеш отримати додаткові бали за наступні покращення:
- Деплой API на хостинг + HTML-сторінка для підписки на релізи.
- gRPC-інтерфейс як альтернатива або доповнення до REST API.
- Redis-кешування відповідей від GitHub API з TTL 10 хвилин.
- API key автентифікація: ендпоїнти захищені токеном у заголовку.
- Prometheus-метрики — ендпоїнт /metrics з базовими показниками сервісу.
- GitHub Actions CI pipeline: запуск лінтера та тестів при кожному push 

GitHub Release Notification API

Build an API that allows users to subscribe to email notifications about new releases of a chosen GitHub repository.
Endpoints

Implement the following endpoints:

    POST /api/subscribe - Subscribe an email to release notifications for a given GitHub repository (format: owner/repo)
    GET /api/confirm/{token} - Confirm email subscription
    GET /api/unsubscribe/{token} - Unsubscribe from release notifications
    GET /api/subscriptions?email={email} - Get all active subscriptions for a given email

    Note: swagger.yaml file contains API documentation. You can view it using Swagger Editor. Changing the contracts is not allowed.

Requirements

    The service must match the API described in the swagger documentation.
    All functionality (API, Scanner, Notifier) must be implemented within a single service (monolith). Splitting into microservices is not allowed at this stage.
    All application data must be stored in a database. Database schema migrations must run on service startup.
    The repository must contain Dockerfile and docker-compose.yml that allow running the entire system in Docker.
    The service must regularly check for new releases for all active subscriptions. When a new release is detected, send an email to the subscriber. For each repository, store last_seen_tag and only notify if a new release appears.
    When creating a subscription, the service must verify the repository exists via GitHub API. Parameter format: owner/repo (e.g., golang/go). If the repository is not found - return 404. If the format is invalid - return 400.
    The service must correctly handle 429 Too Many Requests from GitHub API (rate limit: 60 req/hour without token, 5000 with token).
    You may use frameworks, but only “thin” solutions. High-level frameworks are prohibited: Nest.js (Node.js), Revel or Fx (Go), Laravel (PHP). Allowed: Fastify or Express (Node.js), Gin / Chi / net/http (Go), Slim or built-in language capabilities (PHP).
    Unit tests for business logic are mandatory. Integration tests are a bonus.
    You may add comments or logic descriptions in README.md. Correct logic can be an advantage in evaluation if you don’t fully complete the task.

Expected languages: Golang, Node.js, or PHP.
Extras

    Deploy the API to a hosting + HTML page for subscribing to releases
    gRPC interface as an alternative or addition to REST API
    Redis caching of GitHub API responses with TTL 10 minutes
    API key authentication: endpoints secured with a token in the header
    Prometheus metrics - /metrics endpoint with basic service indicators
    GitHub Actions CI pipeline: run linter and tests on every push
