# 🎓📰 Modul Website Berita Fakultas - Breakdown & Task List

> **Prioritas Utama (Core Modules)**: 1, 2, 3, 4, 12 (Menu Builder)
> **Tech Stack**: Laravel 11, Vue.js (Inertia), TailwindCSS, Shadcn UI.
> **Security Focus**: Implementasi ketat pada Input Validation, Output Escaping, CSRF Protection, dan Role-Based Access Control.

---

## 0. Security & System Hardening (System Core)
**Status:** [x] Done
- [x] Install `spatie/laravel-permission` & `spatie/laravel-activitylog`.
- [x] Setup Laravel Breeze (Vue/Inertia).
- [x] Setup Shadcn UI components.

---

## 1. Modul Role & Struktur Organisasi (Core)
**Status:** [x] Done
- [x] Create Migration & Model `User`, `Role`, `Permission`.
- [x] Backend: Controllers for User CRUD.
- [x] Frontend (Admin): Vue Pages for User Management.
- [x] Seeding roles and initial users.

---

## 2. Modul Struktur Fakultas & Prodi (Core)
**Status:** [x] Done
- [x] Create Migration & Model `ProgramStudy`.
- [x] Backend: Controller `ProgramStudyController`.
- [x] Frontend (Admin): Form Input Data Prodi.
- [x] Frontend (Public): Prodi Profile Page & Listing Page (`/prodi`).

---

## 5. Modul Halaman Statis (Page)
**Status:** [x] Done
- [x] Create Migration & Model `Page`.
- [x] Backend: Controller `PageController`.
- [x] Frontend (Admin): Page Management.
- [x] Frontend (Public): Static Page View (`/{slug}`).

---

## 6. Modul Menu Builder ⭐
**Status:** [x] Done
- [x] Dynamic Menu Rendering.
- [x] Backend: Controllers for Menu CRUD.
- [x] Frontend (Admin): Menu Builder with Hierarchical Support.
- [x] Frontend (Public): Recursive Navbar.
- [ ] Drag & Drop Menu Management (Optional enhancement).

---

## 7. Modul Agenda & Fasilitas
**Status:** [x] Done
- [x] Migration & Model `Event`, `Facility`.
- [x] Admin CRUD for both.
- [x] Public Index & Detail views for both.

---

## 8. Modul Documents (Download)
**Status:** [x] Done
- [x] Migration & Model `Document`.
- [x] Public listing grouped by category.
- [x] Secure download functionality.

---

## 9. Hero Slider
**Status:** [x] Done
- [x] Dynamic homepage slider management.

---

## 10. Search & UI/UX Polish
**Status:** [x] Done
- [x] Site-wide search implementation.
- [x] Responsive layout refinements.
- [ ] Dark mode optimizations (Optional).

---

## 11. Final Review & Cleanup
**Status:** [/] In Progress
- [x] Verification of all public routes.
- [x] Seeding verification.
- [ ] Final project documentation.

