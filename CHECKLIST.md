# ValexHub — Full System Checklist

> Updated as work progresses. ✅ = done, 🔄 = in progress, ⬜ = not started.

---

## 🐛 Bugs

- [x] **`deploy_for` / client details not saving** — made `deployFor` `.live` in `@entangle` so it syncs to Livewire immediately when toggled.
- [x] **`next_renewal_date` never set** — `checkout()` now calculates and writes `next_renewal_date` based on pricing type (monthly +1 month, quarterly +3, yearly +1 year, onetime/license = null).
- [x] **Duplicate "Custom Hospital Management System"** — bad seed row (id=2, no pricing) deleted from products table.

---

## 💳 Checkout & Payment Flow

- [x] **Paystack success webhook** — `charge.success` with type=`product_purchase` activates `UserProduct`, activates `Deployment`, dispatches `FulfillProductDeployment` job. Already implemented.
- [x] **Paystack failure webhook** — `charge.failed` handler added: marks inactive `UserProduct` / deployment as failed and logs it.
- [x] **Paystack renewal webhook** — `invoice.payment_failed` handler added: cancels Wave subscription or suspends `UserProduct` + linked `Deployment` for product plans.
- [x] **Post-checkout callback page** — callback now verifies the Paystack transaction, extracts `deployment_id` from metadata, and redirects to `/dashboard/deployments/{id}` with flash message. Falls back to deployments list if reference missing.
- [ ] **Server-side breakdown matches client-side** — ensure `buildBreakdown()` in `checkout()` includes the selected recurring plan amount (currently excluded from the `onetimeArr` path).

---

## ⚙️ Service Lifecycle

- [x] **1. Suspend** — Filament action on Deployment: sets `status = suspended` on deployment + userProduct. Coolify API call deferred until Coolify is configured.
- [x] **2. Reactivate** — Filament action: sets `status = active` on both. Coolify API deferred.
- [ ] **3. Schedule termination date** — add `terminate_at` column to `user_products`; Filament date-picker action; daily job auto-terminates when date passes.
- [x] **4. Terminate** — Filament action (with confirmation modal): sets `status = terminated` on deployment + `cancelled` on userProduct. Coolify backup/delete deferred.
- [ ] **5. Restore** — Admin re-triggers deploy from GitHub backup via Coolify; reactivates `user_product` record.
- [ ] **Automated daily job** — process scheduled terminations (`terminate_at <= now()`).
- [ ] **Automated renewal check job** — runs daily; flags any `next_renewal_date` past due as overdue.

---

## 📋 Client Dashboard

- [x] **Deployment detail view** — `/dashboard/deployments/{id}` Folio page shows: plan, each addon with price, setup fee, total paid, renewal date, credentials reveal, one-click login, domain config, status-specific messaging.
- [x] **Renewal date & next billing** — shown on deployment list cards; overdue flag on cards and detail.
- [x] **Deployment status badge** — active / suspended / terminated / failed / provisioning / pending with plain-language explanations on both list and detail. `suspended`+`terminated` added to enum via migration.
- [x] **Credentials view** — "Show credentials" toggle on deployment detail page reveals encrypted credentials in-place.
- [x] **Domain request** — domain configuration section on deployment detail; choose self-managed or request team setup.
- [ ] **Cancel / terminate request** — client requests cancellation; admin approves before actual termination.
- [x] **Support ticket per deployment** — "Get Help" panel on deployment detail opens inline form; links to `/dashboard/support`; `SupportThread` Livewire component for live conversation.
- [x] **Invoice history** — `/dashboard/invoices` page with table, status, and PDF download link.

---

## 🛠 Admin Panel — Deployments & Orders

- [x] **Show what was ordered** — DeploymentResource form now shows order summary section: setup fee, plan (recurring), each addon with price, total paid, and renewal date via `Placeholder` with HTML table.
- [x] **Fix `deploy_for` display** — `client_name` / `client_email` on list and detail; deploy type (cloud/on-prem) as description on Product column.
- [x] **Renewal dates** — `userProduct.next_renewal_date` column on list (red if overdue); renewal row in form order summary.
- [x] **Suspend / Reactivate / Terminate actions** on DeploymentResource — visible conditionally by status; confirmation modal on each.
- [ ] **Set termination date action** — date-picker action on DeploymentResource.
- [ ] **View decrypted credentials** — admin action to reveal deployment credentials.
- [ ] **Order history per user** — on UserResource, show all their `user_products` with plan and addons.

---

## 🧾 Invoice System

- [x] **`invoices` table** — `id, user_id, user_product_id, deployment_id, amount, status, paystack_reference, line_items (json), pdf_path, paid_at`.
- [x] **Auto-generate invoice on checkout success** — `InvoiceService::generateForPurchase()` called in webhook `processProductPurchase()`; errors caught and logged without breaking fulfillment.
- [x] **Invoice PDF** — `resources/views/pdf/invoice.blade.php` rendered via DomPDF; stored at `storage/invoices/INV-XXXXXX.pdf`.
- [x] **Auto-email invoice** — `InvoiceMail` queued with PDF attachment after successful payment.
- [x] **Renewal invoice** — `GenerateRenewalInvoices` command finds active recurring UserProducts due in 7 days, generates PDF invoice, emails client. Scheduled daily at 08:00 in `console.php`.
- [x] **Invoice list on client dashboard** — `/dashboard/invoices` Folio page; table with status badge, paid date, PDF download.
- [x] **Invoice management on admin** — `InvoiceResource` with: client name/email search filter, overdue filter, Download PDF, Resend, Mark Paid, **Split** (move selected line items to new invoice), **Merge** bulk action (combine 2+ invoices into one), **Create Manual Invoice** header action (pick client, add custom line items, set due date).

---

## 🎟 Support System

- [x] **`support_tickets` table** — `id, user_id, deployment_id, subject, status (open/in_progress/resolved/closed), priority`.
- [x] **`support_messages` table** — `id, ticket_id, user_id, body, is_admin`.
- [x] **Client: create ticket** — "Get Help" inline form on deployment detail page; posts to `SupportTicketController@store`.
- [x] **Client: view ticket thread** — `/dashboard/support/{ticket}` Folio page with `SupportThread` Livewire component (polls every 10s).
- [x] **Admin: ticket management** — `SupportTicketResource` with Reply action (sends email + updates status), Edit Status, priority filter.
- [x] **Email notifications** — `SupportTicketCreatedMail` to admin on new ticket; `SupportReplyMail` to client on admin reply.

---

## 📧 Notifications & Emails

- [ ] **Deployment credentials email** — verify it fires correctly after Paystack success webhook (currently `DeploymentCredentialsMail` exists).
- [ ] **Renewal reminder email** — 7 days before `next_renewal_date`; queued by daily scheduler.
- [ ] **Overdue payment email** — on the day of and 3 days after missed renewal.
- [ ] **Suspension notice email** — when service is suspended.
- [ ] **Termination warning email** — 7 days before scheduled termination date.
- [ ] **Invoice email** — with PDF attached.
- [ ] **Support reply notification** — both directions (client ↔ admin).

---

## 🗂 Admin Sidebar — Grouping

- [x] **Products & Catalog** — Products (1), Categories (2), Changelogs (3).
- [x] **Deployments & Services** — Deployments (1), Domain Requests (2).
- [x] **Users & Affiliates** — Users (1), Roles (2), Permissions (3), Affiliate Commissions (4).
- [x] **Finance & Billing** — Plans (1), Demo Requests (2).
- [x] **Content** — Posts (1), Pages (2). (Forms already hidden via `$shouldRegisterNavigation = false`.)
- [x] **Settings & System** — Settings (1).
- [x] `navigationSort` set within each group.
- [ ] Hide unused Wave built-in resources (if any appear after testing).

---

## 🚀 Coolify Setup & Deployment Infrastructure

### What's Built ✅
- [x] **Per-server credentials in DB** — `coolify_servers` table stores `api_url`, `api_token` (encrypted), `coolify_server_uuid`, `coolify_project_uuid`, `coolify_environment_name` per VPS.
- [x] **`RealCoolifyDeploymentService`** — reads credentials from the `CoolifyServer` model, not `.env`. Supports `docker_image` and `git_repo` deploy types.
- [x] **Product deploy config** — each product has `coolify_deploy_type`, `coolify_docker_image`, `coolify_git_repo`, `coolify_git_branch`, `coolify_env_template` fields editable in admin.
- [x] **Test image on all products** — all 5 products set to `traefik/whoami` (3 MB, zero config, port 80) for end-to-end testing.
- [x] **Deployment status auto-update** — `DeploymentDetail` Livewire component polls every 5s while pending/provisioning; stops when done.
- [x] **Coolify FQDN update** — code path exists to call `PATCH /api/v1/applications/{uuid}` to assign a customer domain to a deployment.

### Remaining ⬜
- [ ] **End-to-end test with real VPS** — checkout → payment → webhook → Coolify deploys → app accessible at auto-domain → credentials email.
- [ ] **Coolify FQDN API test** — call the FQDN update endpoint with a placeholder domain; confirm Coolify stores it. (DNS propagation tested separately with a real domain.)
- [ ] **GitHub backup config** — configure backup destination in Coolify per-app; test backup-to-GitHub flow.
- [ ] **Mac Docker Desktop fix** — add `/root` to Docker Desktop file sharing OR run `docker run --rm --privileged -v /:/hostroot alpine mkdir -p /hostroot/root/.docker/buildx` to create the path inside the Docker VM. Needed for Coolify's helper container on Mac only.

---

## 🖥 New VPS Launch Guide

Follow this every time you provision a new VPS server.

### 1. Install Coolify on the VPS
```bash
curl -fsSL https://cdn.coollabs.io/coolify/install.sh | bash
```
Coolify dashboard will be at `http://YOUR_VPS_IP:8000` (or port 3000 depending on version).

### 2. Get Coolify credentials
In the Coolify dashboard:
- **API Token** → Settings → API Tokens → create one
- **Server UUID** → Servers → click your server → UUID shown in the URL or server detail
- **Project UUID** → create a new Project (e.g. "ValexHub Clients") → UUID in URL

### 3. Set the wildcard domain in Coolify
In Coolify: **Settings → Instance → Wildcard Domain**

Set it to (no asterisk — Coolify adds that itself):
```
apps1.valexhub.com        ← for VPS 1
apps2.valexhub.com        ← for VPS 2
```
Each VPS gets a different subdomain so auto-generated URLs are unique per server.

### 4. DNS records (in your domain registrar / Cloudflare)
Add one wildcard A record per VPS:
```
*.apps1.valexhub.com   →   VPS_1_IP
*.apps2.valexhub.com   →   VPS_2_IP
```
This makes every auto-generated deployment URL (e.g. `myapp-abc123.apps1.valexhub.com`) resolve to the right server automatically.

### 5. Open firewall ports on the VPS
```
Port 80   (HTTP — Traefik)
Port 443  (HTTPS — Traefik + Let's Encrypt)
Port 8000 (Coolify dashboard — restrict to your IP only)
```

### 6. Add the server in ValexHub admin
Go to `/admin/coolify-servers` → New Server:
- **Name**: e.g. `VPS 1 (Lagos)`
- **API URL**: `http://YOUR_VPS_IP:8000`
- **API Token**: from step 2
- **Server UUID**: from step 2
- **Project UUID**: from step 2
- **Environment Name**: `production`

### 7. For customer custom domains (per deployment)
When a customer's domain DNS is pointed to the VPS IP, call Coolify API to assign it:
```
PATCH /api/v1/applications/{app_uuid}
{ "fqdn": "thetownschool.com" }
```
Traefik on that VPS picks it up automatically and requests an SSL cert from Let's Encrypt.

> **Customer domain DNS record:** `thetownschool.com → IP of whichever VPS holds that deployment`
> Your code already knows the VPS from the `Deployment → CoolifyServer` relationship.

---

## ✅ Production Readiness

- [ ] **Queue worker** — all jobs (invoice, email, deployment) run via queue; configure Redis for production.
- [ ] **Scheduler** — `routes/console.php` has renewal checks, termination processing, invoice reminders all scheduled.
- [ ] **Webhook security** — verify Paystack webhook signature on every incoming webhook.
- [ ] **Rate limiting** — on checkout endpoint to prevent double-submission.
- [ ] **Authorization checks** — clients can only see/act on their own deployments (policy guards).
- [ ] **Encryption** — credentials stored encrypted; verify key set and rotation plan exists.
- [ ] **Error monitoring** — Sentry or similar wired up for production exceptions.
- [ ] **`.env.example` updated** — all new keys documented (Coolify API, Paystack webhook secret, etc.).

---

## Order of Work

1. Bugs
2. Paystack webhooks (nothing activates without these)
3. Client dashboard detail view
4. Admin deployment detail
5. Invoice system
6. Service lifecycle actions (Filament)
7. Support system
8. Sidebar grouping
9. Coolify local setup + end-to-end test
10. Production hardening
