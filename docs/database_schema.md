# 🎓📰 Database Schema Design for Faculty News Module (Updated)

## 10. Menu Builder (Frontend Navigation)
**Table: `menus`**
- `id` (PK)
- `name` (String, e.g., "Main Menu", "Footer Menu")
- `handle` (String, Unique slug for code reference)
- `is_active` (Boolean)
- `created_at` / `updated_at`

**Table: `menu_items`**
- `id` (PK)
- `menu_id` (FK -> `menus.id`)
- `parent_id` (FK -> `menu_items.id`, Nullable, for recursive nested items)
- `title` (String)
- `url` (String, Nullable)
- `route` (String, Nullable, for internal named routes)
- `parameters` (JSON, Nullable, e.g., `{"id": 1}`)
- `target` (Enum: '_self', '_blank')
- `type` (Enum: 'link', 'route', 'category', 'page')
- `related_id` (Integer, Nullable, Polymorphic ID equivalent if needed)
- `related_type` (String, Nullable)
- `order` (Integer, Default: 0)
- `icon` (String, Nullable)

---

## 1. Users & RBAC (Role Based Access Control)
**Table: `users`** (Standard Laravel + Custom Fields)
... (Same as before)

## 2. Organization Structure
**Table: `program_studies`**
... (Same as before)

## 3. Content Management (Articles & Categories)
**Table: `articles`**
... (Same as before)

## 4. Events & Agenda
**Table: `events`**
... (Same as before)
