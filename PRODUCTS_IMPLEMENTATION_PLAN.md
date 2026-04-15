# Products System Implementation Plan

## Overview

This document outlines the implementation plan for adding a comprehensive products system to the Wave Laravel SaaS framework. The system will integrate with existing categories, subscriptions, and change tracking while maintaining full compatibility with the current billing infrastructure.

## Database Analysis Summary

### Current Structure Analysis

#### Categories Table
- **Location:** `wave/database/migrations/2024_03_29_225435_create_categories_table.php:14-23`
- **Features:** Hierarchical structure (parent_id), name, slug, order
- **Current Usage:** Blog posts system
- **Reusability:** Perfect for product categorization

#### Plans Table
- **Location:** `wave/database/migrations/2024_03_29_230312_create_plans_table.php:14-32`
- **Features:** Monthly/yearly/onetime pricing, role-based permissions
- **Integration Point:** Will work alongside products for subscription management

#### Subscriptions Table
- **Location:** `wave/database/migrations/2024_03_29_230313_create_subscriptions_table.php:14-34`
- **Features:** Polymorphic billable relationship, cycle support (month/year/onetime)
- **Extension Required:** Add product_id for direct product association

#### Changelogs Table
- **Location:** `wave/database/migrations/2024_03_29_225656_create_changelogs_table.php:14-20`
- **Current Structure:** Simple title, description, body
- **Extension Required:** Make polymorphic to support product change tracking

## Implementation Plan

### 1. Products Table Structure

```sql
CREATE TABLE products (
    id UNSIGNED INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category_id UNSIGNED INT,
    short_description TEXT,
    description LONGTEXT,
    low_price DECIMAL(8,2),
    high_price DECIMAL(8,2),
    features JSON,
    icon VARCHAR(255),
    images JSON,
    website_url VARCHAR(255),
    is_active BOOLEAN DEFAULT 1,
    sort_order INT DEFAULT 0,
    seo_title VARCHAR(255),
    seo_description TEXT,
    seo_keywords TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_category_active (category_id, is_active),
    INDEX idx_slug (slug),
    INDEX idx_active_sort (is_active, sort_order)
);
```

**Key Features:**
- **Category Integration:** Links to existing categories table
- **Flexible Pricing:** Low and high price range for display
- **Rich Content:** Short and full descriptions
- **Media Support:** Icon and images (JSON array)
- **SEO Ready:** Full SEO meta fields
- **Performance:** Proper indexing for common queries
- **JSON Features:** Flexible feature list storage

### 2. User Products Pivot Table

```sql
CREATE TABLE user_products (
    id UNSIGNED BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id UNSIGNED BIGINT NOT NULL,
    product_id UNSIGNED INT NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    purchase_date TIMESTAMP NOT NULL,
    next_renewal_date TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'cancelled', 'expired') DEFAULT 'active',
    subscription_id UNSIGNED BIGINT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE SET NULL,
    INDEX idx_user_status (user_id, status),
    INDEX idx_product_status (product_id, status),
    UNIQUE KEY unique_user_product (user_id, product_id)
);
```

**Key Features:**
- **Purchase Tracking:** Records amount paid and purchase date
- **Renewal Management:** Tracks next renewal dates
- **Status Management:** Active, inactive, cancelled, expired states
- **Subscription Link:** Optional link to subscription for recurring payments
- **Unique Constraint:** Prevents duplicate product purchases per user
- **Performance:** Indexed for user and product lookups

### 3. Subscription Table Extension

```sql
ALTER TABLE subscriptions 
ADD COLUMN product_id UNSIGNED INT NULL AFTER plan_id,
ADD FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
ADD INDEX idx_product (product_id);
```

**Purpose:**
- Links subscriptions directly to products
- Maintains backward compatibility (nullable field)
- Enables product-specific subscription management
- Supports both plan-based and product-based subscriptions

### 4. Product Change Logs Extension

```sql
ALTER TABLE changelogs 
ADD COLUMN loggable_type VARCHAR(255) NULL AFTER id,
ADD COLUMN loggable_id UNSIGNED INT NULL AFTER loggable_type,
ADD INDEX idx_loggable (loggable_type, loggable_id);
```

**Purpose:**
- Makes changelogs polymorphic
- Supports both general changelogs and product-specific changes
- Maintains backward compatibility
- Enables tracking of product updates and changes

### 5. Category System
**No changes required** - existing categories table is perfect for product categorization.

## Migration Files to Create

### Required Migration Files (in order):

1. **`2025_04_15_100000_create_products_table.php`**
   - Creates the main products table with all required fields
   - Sets up foreign keys and indexes

2. **`2025_04_15_100001_create_user_products_table.php`**
   - Creates the user-product relationship table
   - Handles purchase tracking and status management

3. **`2025_04_15_100002_add_product_id_to_subscriptions_table.php`**
   - Extends existing subscriptions table
   - Adds product association capability

4. **`2025_04_15_100003_extend_changelogs_for_products.php`**
   - Makes changelogs polymorphic
   - Enables product change tracking

## Key Features of This Design

### Billing System Integration
- **Seamless Integration:** Works with existing Stripe/Paddle billing
- **Dual Support:** Handles both one-time purchases and recurring subscriptions
- **Purchase History:** Complete tracking in `user_products` table
- **Flexible Pricing:** Supports first-year pricing + ongoing subscriptions

### Category System Enhancement
- **Reuses Infrastructure:** Leverages existing hierarchical categories
- **No Conflicts:** Products and blog posts can share categories safely
- **Performance:** Maintains existing caching mechanisms from `wave/src/Category.php:33-47`

### Change Tracking Integration
- **Polymorphic Design:** Supports both general and product-specific changelogs
- **Version Control:** Track all product updates and changes
- **Backward Compatible:** Existing changelogs continue working unchanged

### SEO & Performance Optimization
- **Complete SEO:** Title, description, keywords fields
- **Smart Indexing:** Optimized for common query patterns
- **JSON Flexibility:** Features and images stored as JSON
- **Caching Ready:** Follows Wave's caching patterns from existing models

### User Experience Features
- **Purchase Management:** Full purchase history and status tracking
- **Renewal Tracking:** Automatic renewal date management
- **Status Control:** Granular status management (active, cancelled, expired)
- **Duplicate Prevention:** Unique constraints prevent accidental double purchases

### Extensibility Features
- **Modular Design:** Each table serves a specific purpose
- **Future-Proof:** JSON fields allow feature expansion
- **API Ready:** Structure supports REST API development
- **Admin Panel Ready:** Compatible with existing Filament admin structure

## Business Model Support

### First Year + Subscription Model
- **Initial Purchase:** Recorded in `user_products` with first-year amount
- **Ongoing Subscription:** Linked via `subscription_id` for monthly/yearly billing
- **Flexible Pricing:** `low_price`/`high_price` can represent different pricing tiers
- **Status Tracking:** Handles transitions from first-year to subscription

### Software Product Management
- **Rich Metadata:** Name, description, website URL, features
- **Media Support:** Icon and image gallery
- **Category Organization:** Hierarchical categorization
- **Active Status:** Enable/disable products easily

## Implementation Considerations

### Database Performance
- All tables properly indexed for common queries
- JSON fields for flexible data without schema changes
- Foreign key constraints maintain data integrity
- Unique constraints prevent data duplication

### Caching Strategy
- Follows Wave's existing caching patterns
- Ready for implementation of product-specific caching
- Compatible with existing cache clearing mechanisms

### Migration Safety
- All new fields are nullable for backward compatibility
- Foreign keys use appropriate cascade rules
- Indexes added for performance optimization
- Migration order ensures dependency resolution

This implementation plan provides a complete foundation for a robust products system that integrates seamlessly with Wave's existing architecture while supporting your specific business requirements.