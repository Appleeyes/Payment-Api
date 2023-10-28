# Payment API

Payment API is a RESTful web service for managing payment transactions.

## Table of Contents

- [Features](#features)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Documentation](#documentation)
- [Docker Setup](#docker-setup)
- [Contributing](#contributing)
- [License](#license)

## Features
- Create, retrieve, update, reactivate, deactivate and delete payment methods.
- Create, retrieve, update, reactivate, deactivate and delete customers.
- Create, retrieve, update, and delete payment transactions.
- Store payment data including customer information, payment methods, and transaction details.
- Error handling and validation for data input.
- Swagger/OpenAPI documentation for API endpoints.

## Getting Started

### Prerequisites

Before you begin, ensure you have met the following requirements:

- **Docker:** You need Docker and Docker Compose installed on your local machine.

### Installation

1. Clone the repository:

   ```shell
   git clone https://github.com/Appleeyes/payment-api.git
   cd payment-api

2. Create a `.env` file and copy environment variables for your database in the `.env.example` file and make sure    you assign your preferred value.

3. Build and start the Docker containers:

   ```shell
   docker-compose up -d

4. Install project dependencies:

   ```shell
   docker-compose run composer install

5. Run the database migrations to create the required tables:

   ```shell
   docker-compose run php-fpm php vendor/bin/doctrine orm:schema-tool:create

6. Start the development server:

   ```shell
   docker-compose up


## Usage

You can start making API requests to manage payment transactions. You can use tools like curl, Postman, or any HTTP client to interact with the API.
   
## API Endpoints

The API supports the following endpoints:

- Methods Endpoints:
    - `GET /v1/methods:` Retrieve a list of payment methods.
    - `POST /v1/methods:` Create a new payment method.
    - `PUT /v1/methods/{id:[0-9]+}:` Update a payment method by ID.
    - `DELETE /v1/methods/{id:[0-9]+}:` Delete a payment method by ID.
    - `GET /v1/methods/deactivate/{id:[0-9]+}:` Deactivate a payment method by ID.
    - `GET /v1/methods/methods/{id:[0-9]+}:` Reactivate a payment method by ID.

- Customers Endpoints:
    - `GET /v1/customers:` Retrieve a list of customers.
    - `POST /v1/customers:` Create a new custormers.
    - `PUT /v1/customers/{id:[0-9]+}:` Update a customers by ID.
    - `DELETE /v1/customers/{id:[0-9]+}:` Delete a customers by ID.
    - `GET /v1/customers/deactivate/{id:[0-9]+}:` Deactivate a customer by ID.
    - `GET /v1/customers/reactivate/{id:[0-9]+}:` Reactivate a customer by ID.

- Payments Endpoints:
    - `GET /v1/payments:` Retrieve a list of payment transactions.
    - `POST /v1/payments:` Create a new payment transaction.
    - `PUT /v1/payments/{id:[0-9]+}:` Update a payment transaction by ID.
    - `DELETE /v1/payments/{id:[0-9]+}:` Delete a payment transaction by ID.

For detailed information about these endpoints and request/response formats, please refer to the API documentation.

## Documentation

The API documentation is available using Swagger/OpenAPI. Access it by visiting http://localhost:4000/api-docs in your web browser.

## Docker Setup

This project is Dockerized for easier setup. Ensure you have Docker and Docker Compose installed, and then follow the installation instructions above to set up the environment.


## Contributing

Contributions are welcome! Feel free to open an issue or submit a pull request.

## License

This project is licensed under the MIT License.