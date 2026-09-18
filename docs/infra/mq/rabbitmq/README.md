# 🐇 RabbitMQ Infrastructure Documentation

This directory contains the architecture specifications, design decisions, and operational guidelines for RabbitMQ in our E-Commerce Modular Monolith.

## 📖 Primary Documentation

- **[RabbitMQ Architecture & Specification](./rabbitmq-architecture-specification.md)**
    - Broker Comparison (RabbitMQ vs Kafka vs Redis Streams vs Laravel Native Queue)
    - Core AMQP 0-9-1 Concepts & Lifecycle
    - ACK vs NACK Deep Dive (`requeue: true` vs `requeue: false`)
    - Dead Letter Exchange (DLX) & Exponential Retry Loops with TTL (Mermaid Diagrams)
    - Clean Architecture / DDD Implementation in PHP (`infrastructure/MQ/`)
    - Operational Runbook (Artisan commands, Docker Shovel plugin, UI monitoring)
    - Architectural FAQ (Push vs Poll, Concurrency limits, Socket pooling)

---

## 🚀 Quick Commands Reference

```bash
# Publish a test message
docker compose -f environments/docker-compose.dev.yml exec app php artisan mq:publish order_created "Test Payload"

# Publish a simulated failure to verify DLQ routing
docker compose -f environments/docker-compose.dev.yml exec app php artisan mq:publish order_created "Poisoned Order" --fail

# Start worker listening on a queue
docker compose -f environments/docker-compose.dev.yml exec app php artisan mq:consume order_created --prefetch=1
```

- RabbitMQ Management UI: [http://localhost:15672](http://localhost:15672) (User: `ecommerce`, Password: `secret`)
