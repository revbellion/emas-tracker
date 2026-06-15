# GOLD FAMILY TRACKER - DEVELOPMENT RULES

## TECHNOLOGY STACK

Backend:

* Laravel 12
* PHP 8.3+

Frontend:

* Vue 3
* Inertia.js
* Tailwind CSS

Database:

* MySQL

---

## ARCHITECTURE

Use:

* Clean Architecture
* Service Pattern
* Validation Layer
* DTO Pattern (optional)

---

## BACKEND RULES

Controllers:

Thin Controller

Business Logic:

Service Layer

Validation:

Form Request

Database:

Transaction when required

Logging:

Activity Log

---

## FRONTEND RULES

Use:

* Reusable Components
* Composables
* Lazy Loading
* Loading States
* Error Handling

---

## DATABASE RULES

Required:

* Foreign Keys
* Indexes
* Soft Delete

Avoid:

* Duplicate Data
* Hard Delete

---

## SECURITY RULES

Required:

* Authentication
* Authorization
* CSRF Protection
* Validation

---

## AUDIT RULES

All critical actions must be logged.

Examples:

* Create Transaction
* Edit Transaction
* Delete Transaction
* Update Gold Price

---

## PERFORMANCE RULES

Use:

* Eager Loading
* Pagination
* Query Optimization

Avoid:

* N+1 Query

---

## TESTING

Required:

* Feature Tests
* Validation Tests

---

## CODE QUALITY

Required:

* DRY
* SOLID
* Readable Naming
* Consistent Structure

No Hardcoded Values.

No Business Logic In Vue Components.

Business Logic Must Stay In Backend Services.
