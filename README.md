# BetterWag — Dog Shelter Donation Platform

A Laravel + Inertia SPA for dog shelters to run fundraising campaigns.
Accepts one-time emergency donations and recurring monthly sponsorships via Stripe.

---

## Tech Stack

```
Backend:    Laravel 13
Auth:       Laravel Fortify (auth flows: login, register, reset) + Sanctum (session/cookie guard)
Payments:   Laravel Cashier + Stripe
Email:      AWS SES via Laravel Mail
Storage:    AWS S3 via Laravel Storage
Queue:      Redis
DB:         PostgreSQL
Frontend:   Vue 3 + Inertia v3 + Tailwind CSS v4
Testing:    Pest
```

---

## Tooling

> Cross-stack tooling (git hooks, commit conventions) deliberately uses npm packages — the Node ecosystem is more mature and battle-tested for these concerns than PHP equivalents like CaptainHook or GrumPHP.

| Concern                | Tool                                                                                                   | Source   | When            |
| ---------------------- | ------------------------------------------------------------------------------------------------------ | -------- | --------------- |
| Formatting (PHP)       | [Pint](https://github.com/laravel/pint)                                                                | Composer | pre-commit, CI  |
| Formatting (JS/TS/Vue) | [Prettier](https://prettier.io)                                                                        | npm      | pre-commit, CI  |
| Linting (JS/TS/Vue)    | [ESLint](https://eslint.org)                                                                           | npm      | pre-commit, CI  |
| Static analysis (PHP)  | [PHPStan](https://phpstan.org) via [Larastan](https://github.com/larastan/larastan)                    | Composer | pre-push, CI    |
| Type checking (Vue/TS) | [vue-tsc](https://github.com/vuejs/language-tools)                                                     | npm      | pre-commit, CI  |
| Testing                | [Pest](https://pestphp.com) (PHPUnit under the hood)                                                   | Composer | CI, on demand   |
| Commit format          | [Commitlint](https://commitlint.js.org) + [Commitizen](https://commitizen-tools.github.io/commitizen/) | npm      | commit-msg hook |
| Git hooks              | [Husky](https://typicode.github.io/husky) + [lint-staged](https://github.com/lint-staged/lint-staged)  | npm      | on git events   |

### Hook summary

| Hook         | Runs                                                                              |
| ------------ | --------------------------------------------------------------------------------- |
| `pre-commit` | lint-staged → Pint (`.php`), ESLint + Prettier (`.ts`, `.vue`) + vue-tsc          |
| `commit-msg` | Commitlint (enforces [Conventional Commits](https://www.conventionalcommits.org)) |
| `pre-push`   | PHPStan                                                                           |

Use `npm run commit` for an interactive conventional commit prompt via Commitizen.

---

## Local Setup

```bash
composer run setup   # install deps, generate app key, run migrations
composer run dev     # start server, queue, logs, and Vite concurrently
```

Required environment variables — see `.env.example` for the full list. Key ones:

```
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
MAIL_FROM_ADDRESS=
```

---

## Domain Overview

```
Shelter
├── has many Dogs
├── has many Campaigns
└── has many Staff (shelter_admin users)

Dog
├── belongs to Shelter
├── has many Campaigns (optional FK — campaigns can be shelter-wide)
├── has many VetRecords
├── has many Photos
├── has many AdoptionApplications
└── has many FosterApplications

Campaign
├── belongs to Shelter
├── belongs to Dog (optional — scope to a specific dog or leave null for shelter-wide)
├── has many Donations
├── type: recurring | one_off
└── status: active | closed | cancelled  (goal_reached is computed: SUM paid donations >= goal_amount)

Donation
├── belongs to Campaign
├── belongs to User (donor)
├── type: one_time | recurring
└── status: pending | paid | failed | refunded

User
├── role: External | ShelterManager | Admin
├── has many Donations
├── has many AdoptionApplications
└── has many FosterApplications

AdoptionApplication
├── belongs to Dog
├── belongs to User
└── status: pending | approved | rejected

FosterApplication
├── belongs to Dog
├── belongs to User
└── status: pending | approved | rejected | ended

VetRecord
└── belongs to Dog

Photo
├── belongs to Dog (S3 via Laravel Storage)
└── is_primary: boolean
```

---

## Dog Lifecycle

```mermaid
stateDiagram-v2
    state "rainbow bridge" as rainbow_bridge

    [*] --> rescued : intake
    rescued --> available : vet assessment<br>(VetRecord created, one-off Campaign opened if needed)
    available --> fostered : foster approved
    available --> adopted : adoption approved
    available --> rainbow_bridge : passed away
    fostered --> available : foster ended
    fostered --> adopted : adoption approved
    fostered --> rainbow_bridge : passed away
    adopted --> [*] : campaigns closed, recurring donors notified
    rainbow_bridge --> [*] : campaigns closed, soft deleted
```

---

## Campaign Types

### Recurring — Keep the Shelter Alive

- Monthly sponsorship to fund food, upkeep, operations
- Shelter-wide by default (`dog_id` null); can be scoped to a dog
- No hard goal (ongoing)
- Closes only when admin manually closes it or the shelter shuts down
- Donor charged monthly via Stripe subscription

### One-off — Emergency

- Hard goal amount (e.g. ₱15,000 for surgery)
- Can be dog-specific (surgery) or shelter-wide (tick epidemic, new van)
- `dog_id` optional
- Closes when goal reached or manually by admin
- Single charge
- Examples: hit by car, surgery, rescue transport, spay/neuter drive, parasite treatment

---

## Database Design

### shelters

| column       | type               | notes       |
| ------------ | ------------------ | ----------- |
| id           | bigint PK          |             |
| name         | string             |             |
| email        | string unique      |             |
| phone_number | string nullable    |             |
| location     | string             |             |
| description  | string nullable    |             |
| deleted_at   | timestamp nullable | soft delete |
| timestamps   |                    |             |

### users

| column        | type               | notes                             |
| ------------- | ------------------ | --------------------------------- |
| id            | bigint PK          |                                   |
| shelter_id    | FK nullable        | null if donor                     |
| name          | string             |                                   |
| email         | string unique      |                                   |
| password      | string             |                                   |
| role          | enum               | external, shelter_manager, admin  |
| stripe_id     | string nullable    | Cashier                           |
| pm_type       | string nullable    | Cashier                           |
| pm_last_four  | string nullable    | Cashier                           |
| trial_ends_at | timestamp nullable | Cashier                           |
| timestamps    |                    |                                   |

### dogs

| column      | type                  | notes                                                 |
| ----------- | --------------------- | ----------------------------------------------------- |
| id               | bigint PK             |                                                       |
| shelter_id       | FK                    |                                                       |
| name             | string                |                                                       |
| breed            | string nullable       |                                                       |
| age_months       | integer nullable      |                                                       |
| gender           | enum                  | male, female                                          |
| description      | text nullable         |                                                       |
| adoption_status  | enum                  | rescued, available, fostered, adopted, rainbow_bridge |
| is_urgent        | boolean default false | bumps to top of listing                               |
| rescued_at       | date nullable         |                                                       |
| deleted_at       | timestamp nullable    | soft delete                                           |
| timestamps       |                       |                                                       |

### campaigns

| column        | type               | notes                                   |
| ------------- | ------------------ | --------------------------------------- |
| id          | bigint PK          |                                                                       |
| shelter_id  | FK                 |                                                                       |
| dog_id      | FK nullable        | optional for both types; null = shelter-wide campaign                 |
| title       | string             |                                                                       |
| description | text nullable      |                                                                       |
| type        | enum               | recurring, one_off                                                    |
| status      | enum               | active, closed, cancelled                                             |
| goal_amount | integer nullable   | centavos, one_off only                                                |
| closed_at   | timestamp nullable |                                                                       |
| timestamps  |                    |                                                                       |

> `goal_reached` is not stored — computed via `SUM(donations.amount WHERE status='paid') >= goal_amount`.

### donations

| column                   | type               | notes                           |
| ------------------------ | ------------------ | ------------------------------- |
| id                       | bigint PK          |                                 |
| campaign_id              | FK                 |                                 |
| user_id                  | FK                 |                                 |
| type                     | enum               | one_time, recurring             |
| amount                   | integer            | centavos                        |
| status                   | enum               | pending, paid, failed, refunded |
| stripe_payment_intent_id | string nullable    |                                 |
| stripe_subscription_id   | string nullable    | recurring only                  |
| paid_at                  | timestamp nullable |                                 |
| timestamps               |                    |                                 |

### adoption_applications

| column      | type               | notes                       |
| ----------- | ------------------ | --------------------------- |
| id          | bigint PK          |                             |
| dog_id      | FK                 |                             |
| user_id     | FK                 |                             |
| status      | enum               | pending, approved, rejected |
| message     | text nullable      | why they want to adopt      |
| reviewed_at | timestamp nullable |                             |
| timestamps  |                    |                             |

### foster_applications

| column       | type               | notes                              |
| ------------ | ------------------ | ---------------------------------- |
| id           | bigint PK          |                                    |
| dog_id       | FK                 |                                    |
| user_id      | FK                 |                                    |
| status       | enum               | pending, approved, rejected, ended |
| message      | text nullable      |                                    |
| foster_start | date nullable      |                                    |
| foster_end   | date nullable      |                                    |
| reviewed_at  | timestamp nullable |                                    |
| timestamps   |                    |                                    |

### vet_records

| column      | type             | notes                                 |
| ----------- | ---------------- | ------------------------------------- |
| id          | bigint PK        |                                       |
| dog_id      | FK               |                                       |
| title       | string           | e.g. "Emergency Surgery - Hit by car" |
| notes       | text nullable    |                                       |
| cost        | integer nullable | centavos                              |
| record_date | date             |                                       |
| timestamps  |                  |                                       |

### photos

| column     | type                  | notes                               |
| ---------- | --------------------- | ----------------------------------- |
| id         | bigint PK             |                                     |
| dog_id     | FK                    |                                     |
| path       | string                | S3 path                             |
| disk       | string default 's3'   |                                     |
| is_primary | boolean default false | one primary per dog enforced in app |
| timestamps |                       |                                     |

---

## API Endpoints

These power the Inertia frontend and are available to external clients (e.g. mobile).

### Auth

```
POST   /api/register
POST   /api/login
POST   /api/logout
GET    /api/user
```

### Shelters

```
GET    /api/shelters                              public
POST   /api/shelters                              Admin
GET    /api/shelters/{shelter}                    public
PUT    /api/shelters/{shelter}                    Admin | ShelterManager (own shelter)
DELETE /api/shelters/{shelter}                    Admin
```

### Dogs

```
GET    /api/shelters/{shelter}/dogs               public, urgent first
POST   /api/shelters/{shelter}/dogs               ShelterManager (own shelter) | Admin
GET    /api/dogs/{dog}                            public
PUT    /api/dogs/{dog}                            ShelterManager (own shelter) | Admin
DELETE /api/dogs/{dog}                            ShelterManager (own shelter) | Admin, soft delete
```

## Permissions Matrix

Roles: `External` (donor/adopter/fosterer), `ShelterManager` (staff of a specific shelter), `Admin` (app-wide).

### Shelters

| Endpoint                    | External | ShelterManager | Admin |
| --------------------------- | :------: | :------------: | :---: |
| GET /shelters               | ✓        | ✓              | ✓     |
| POST /shelters              | ✗        | ✗              | ✓     |
| GET /shelters/{shelter}     | ✓        | ✓              | ✓     |
| PUT /shelters/{shelter}     | ✗        | own shelter    | ✓     |
| DELETE /shelters/{shelter}  | ✗        | ✗              | ✓     |

### Dogs

| Endpoint                          | External | ShelterManager | Admin |
| --------------------------------- | :------: | :------------: | :---: |
| GET /shelters/{shelter}/dogs      | ✓        | ✓              | ✓     |
| POST /shelters/{shelter}/dogs     | ✗        | own shelter    | ✓     |
| GET /dogs/{dog}                   | ✓        | ✓              | ✓     |
| PUT /dogs/{dog}                   | ✗        | own shelter    | ✓     |
| DELETE /dogs/{dog}                | ✗        | own shelter    | ✓     |

### Campaigns

| Endpoint                                | External | ShelterManager | Admin |
| --------------------------------------- | :------: | :------------: | :---: |
| GET /shelters/{shelter}/campaigns       | ✓        | ✓              | ✓     |
| POST /shelters/{shelter}/campaigns      | ✗        | own shelter    | ✓     |
| GET /campaigns/{campaign}               | ✓        | ✓              | ✓     |
| PATCH /campaigns/{campaign}/close       | ✗        | own shelter    | ✓     |

---

### Dog Photos

```
POST   /api/dogs/{dog}/photos                     shelter_admin, S3 upload
DELETE /api/dogs/{dog}/photos/{photo}             shelter_admin
PATCH  /api/dogs/{dog}/photos/{photo}/primary     shelter_admin, set as primary
```

### Campaigns

```
GET    /api/shelters/{shelter}/campaigns          public
POST   /api/shelters/{shelter}/campaigns          ShelterManager (own shelter) | Admin
GET    /api/campaigns/{campaign}                  public, with progress %
PATCH  /api/campaigns/{campaign}/close            ShelterManager (own shelter) | Admin
```

### Donations

```
POST   /api/campaigns/{campaign}/donate           authenticated donor
GET    /api/donations                             donor sees own only
GET    /api/donations/{donation}                  donor sees own only (Policy)
POST   /api/donations/{donation}/cancel-recurring donor cancels their own subscription
```

### Applications

```
POST   /api/dogs/{dog}/adopt                      authenticated donor
POST   /api/dogs/{dog}/foster                     authenticated donor
GET    /api/applications                          shelter_admin sees all, donor sees own
GET    /api/applications/{application}            Policy-gated
PATCH  /api/applications/{application}/approve    shelter_admin
PATCH  /api/applications/{application}/reject     shelter_admin
PATCH  /api/applications/{application}/end-foster shelter_admin
```

### Vet Records

```
GET    /api/dogs/{dog}/vet-records                shelter_admin only
POST   /api/dogs/{dog}/vet-records                shelter_admin only
GET    /api/dogs/{dog}/vet-records/{record}       shelter_admin only
PUT    /api/dogs/{dog}/vet-records/{record}       shelter_admin only
```

### Webhooks

```
POST   /api/webhooks/stripe                       Stripe signed webhook
```

---

## Stripe Webhook Events

```
payment_intent.succeeded          mark Donation paid, check goal_reached via SUM
payment_intent.payment_failed     mark Donation failed, notify donor
invoice.payment_succeeded         recurring donation paid, check goal_reached via SUM
invoice.payment_failed            notify donor of failed recurring charge
customer.subscription.deleted     mark recurring Donation cancelled
```

---

## Scheduled Jobs

```
ThankRecurringDonorJob            monthly — scheduled via Laravel Scheduler
```

All other jobs are dispatched on-demand from controllers or webhook handlers.

---

## Jobs (Queue)

```
SendDonationReceiptJob            email donor after successful payment
SendGoalReachedJob                notify shelter admin when one_off goal met
CloseCampaignJob                  close campaign after goal reached
ThankRecurringDonorJob            monthly thank you email
NotifyDonorsOnAdoptionJob         notify campaign donors dog was adopted
NotifyDonorsOnRainbowBridgeJob    notify campaign donors of passing
SendApplicationStatusJob          notify applicant of approval/rejection
FlagUrgentDogJob                  notify shelter admin when dog flagged urgent
ProcessPhotoUploadJob             resize/optimize photo after S3 upload
CancelDogSubscriptionsJob         cancel all recurring donations when dog adopted/passes
```

---

## Form Requests

```
RegisterRequest
CreateShelterRequest
UpdateShelterRequest
CreateDogRequest
UpdateDogRequest
CreateCampaignRequest
CreateDonationRequest
CreateApplicationRequest
CreateVetRecordRequest
UploadPhotoRequest
```

---

## Custom Validation Rules

```
CampaignIsActive         can't donate to closed/cancelled campaign
DogIsAvailable           can't apply to adopt/foster if already adopted
GoalNotExceeded          one_off donations can't exceed remaining goal amount
NoPendingApplication     can't submit duplicate application for same dog
```

---

## Policies

```
DogPolicy
  view:            public
  create:          shelter_admin of that shelter
  update:          shelter_admin of that shelter
  delete:          shelter_admin of that shelter

CampaignPolicy
  view:            public
  create:          ShelterManager of that shelter
  close:           ShelterManager of that shelter

DonationPolicy
  view:            donor sees own only
  create:          any authenticated user
  cancelRecurring: donor cancels own only

ApplicationPolicy
  view:            shelter_admin sees all, donor sees own
  approve/reject:  shelter_admin of that dog's shelter
  endFoster:       shelter_admin only
```

---

## Mail (AWS SES)

```
DonationReceiptMail
GoalReachedMail
AdoptionAnnouncementMail      to shelter + all campaign donors
RainbowBridgeMail             to shelter + all campaign donors
ApplicationApprovedMail
ApplicationRejectedMail
RecurringDonationFailedMail
MonthlyThankYouMail
```

---

## Storage (AWS S3)

```
Dog photos → s3://betterwag/dogs/{dog_id}/{uuid}.jpg
Primary photo surfaced via DogResource
ProcessPhotoUploadJob handles resize/optimize after upload
```

---

## Planned Frontend

Vue 3 + Quasar (as Vite plugin) — not yet implemented, backend is the priority.
Setup reference: https://quasar.dev/start/vite-plugin/
