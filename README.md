# Payment-Api

Payment API is a RESTful web service for managing payment transactions.

[![Build Status](https://github.com/Appleeyes/Payment-Api/workflows/CI/CD%20Workflow/badge.svg)](https://github.com/Appleeyes/Payment-Api/actions) [![License](https://img.shields.io/badge/License-Apache-blue.svg)](license) [![Repo Size](https://img.shields.io/github/repo-size/Appleeyes/Payment-Api.svg)]() [![Contributors](https://img.shields.io/github/contributors/Appleeyes/Payment-Api.svg)]() ![Code Coverage](https://img.shields.io/badge/coverage-90%25-brightgreen) ![Last Commit](https://img.shields.io/github/last-commit/Appleeyes/Payment-Api)

## Table of Contents

- [Features](#features)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
- [JWT Authentication](#jwt-authentication)
  - [Generating a JWT Token](#generating-a-jwt-token)
  - [Protecting Endpoints](#protecting-endpoints)
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

## JWT Authentication

JWT (JSON Web Tokens) is used for user authentication in the Payment API. JWT is a secure means of representing claims between two parties. In this project, you can generate a JWT token and protect your API endpoints with it.

### Generating a JWT Token

To generate a JWT token, you can use the provided script generateToken.php. This script takes care of encoding the JWT token with your secret key. You can access the token via the `/generate-token` endpoint. Here's how you can generate a token:

First, make sure you have generated your `JWT_SECRET_KEY` in your `.env` file. You can use the `generate.php` file to generate a random one by running `php generate.php` in your terminal, add the result to `JWT_SECRET_KEY=` in your `.env` file. Make sure you are in the base directory whil e running the command.

Then you can go ahead and generate a token by running:

- CURL request:

  ```shell
   curl -X GET http://localhost:your-port/generate-token

- API Client request:

   ```shell
   GET http://localhost:your-port/generate-token

Replace `your-port` with the actual port number where your API is running.

### Protecting Endpoints

JWT is used to protect the API endpoints. To access protected endpoints, you need to include the generated JWT token in the Authorization header of your requests.

#### Example Usage

Here is an example of how you can make authenticated requests to the API:

- CURL request:
   ```
   curl -X GET -H "Authorization: your-token" http://localhost:your-port/v1/methods

- API Client request: (manually add an header before making yor request).
  ``` 
  >> header= Authorization
  >> value = your_token
   
Replace `your-token` with the actual JWT token you generate from the `\generate-token` endpoint.

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

This project is licensed under the [Apache](license) License.