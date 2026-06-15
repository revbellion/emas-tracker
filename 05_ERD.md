# GOLD FAMILY TRACKER - ERD

## OVERVIEW

family_members
│
└── gold_transactions

users
│
└── activity_logs

gold_prices

settings

---

# RELATIONSHIPS

## family_members

1 Member

HAS MANY

gold_transactions

Relationship:

family_members.id
↓
gold_transactions.member_id

---

## users

1 User

HAS MANY

activity_logs

Relationship:

users.id
↓
activity_logs.user_id

---

# GOLD TRANSACTION FLOW

Family Member
↓
Buy Gold
↓
Gold Transaction
↓
Portfolio Updated

---

Family Member
↓
Sell Gold
↓
Gold Transaction
↓
Portfolio Updated

---

Family Member
↓
Transfer Gold
↓
Transfer Out Transaction

Receiving Member
↓
Transfer In Transaction

---

# GOLD PRICE FLOW

Gold Price Provider
↓
Gold Prices
↓
Current Price
↓
Portfolio Valuation

---

# DASHBOARD CALCULATION

Total Gold

=
SUM(all member gold balance)

---

Total Capital

=
SUM(all buy transactions)

---

Current Value

=
Total Gold
×
Latest Sell Price

---

Profit

=
Current Value
-------------

Total Capital

---

Profit Percentage

=
Profit
/
Total Capital
× 100

---

# CORE PRINCIPLES

Every gram of gold must be traceable.

Every transaction must be auditable.

Current valuation always uses latest gold sell price.

Transfer must not change total family gold.

Historical data must never be lost.
