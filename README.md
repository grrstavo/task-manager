# Task Manager Application

A task management application built with Laravel and Vue.js, featuring real-time validation, efficient data handling, and a responsive user interface.

## 🚀 Features

- ✅ Task management with categories
- 📱 Responsive design
- 🔍 Filtering and search
- 📊 Pagination
- ⚡ Real-time validation
- 🎨 Status-based styling
- 🗄️ Category organization

## 📋 Requirements

- PHP 8.2+
- Node.js 20+
- Docker
- Composer

## 🛠️ Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd task-manager
```

2. **Start Docker containers**
```bash
docker compose up -d
```

3. **Run migrations**
```bash
docker compose exec app php artisan migrate:fresh --seed
```

The application will be available at `http://localhost:8000`

## 📁 Project Structure

```
task-manager/
├── app/
|   ├── Enum/
│   |   └── TaskStatus.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
|   |   |       ├── CategoryController.php
│   │   │       └── TaskController.php
│   │   └── Requests/
│   │       └── TaskRequest.php
│   ├── Models/
│   │   ├── Task.php
│   │   └── Category.php
│   ├── Repositories/
│   │   ├── Interfaces/
|   |   |   └── TaskRepositoryInterface.php
│   │   └── TaskRepository.php
│   └── Services/
│       └── TaskService.php
├── resources/
│   └── js/
│       ├── Components/
│       │   ├── Tasks/
│       │   |   ├── TaskForm.vue
│       │   |   └── TaskList.vue
|       |   ├── ApplicationLogo.vue
|       |   ├── Modal.vue
|       |   ├── Navigation.vue
|       |   └── NavigationLink.vue
│       ├── composables/
|       |   ├── useTaskFilters.vue
|       |   ├── useTaskForm.vue
|       |   └── useTaskList.vue
│       ├── Pages/
│       │   └── Tasks/
│       │       └── Index.vue
│       └── stores/
│           └── taskStore.js
└── docker/
    ├── nginx/
    ├── postgres/
    └── redis/
```

## 🔧 Technologies Used

### Backend
- **Laravel 10** - PHP Framework
- **PostgreSQL** - Database
- **Redis** - Caching
- **Docker** - Containerization
- **Nginx** - Web Server

### Frontend
- **Vue.js 3** - JavaScript Framework
- **Inertia.js** - Server-Side Rendering
- **Pinia** - State Management
- **Tailwind CSS** - Styling
- **Vite** - Build Tool

## 💼 Business Rules

### Task Management
1. Tasks must have:
   - Title (3-255 characters)
   - Description (optional)
   - Status (pending, in_progress, completed)
   - Due Date (optional, must be today or future date)
   - Category (optional)

2. Task Statuses:
   - **Pending**: Default status for new tasks
   - **In Progress**: Tasks currently being worked on
   - **Completed**: Tasks that have been finished

3. Task Filtering:
   - Filter by status
   - Filter by category
   - Filter by due date (today, overdue, upcoming)
   - Search by title/description

### Data Display
1. Tasks are displayed in a paginated list
2. Each page shows 10 tasks by default
3. Tasks can be sorted by:
   - Creation date (default)
   - Due date
   - Categories
   - Status

### Validation Rules
1. Task Creation:
   - Title is required and must be 3-255 characters
   - Due date must be today or in the future
   - Status must be a valid enum value
   - Category must exist in the database

2. Real-time Validation:
   - Form validation occurs on both client and server side
   - Immediate feedback for validation errors
   - Submit button disabled until form is valid

### Additional Rules
1. Overdue tasks are visually highlighted
2. Tasks can be deleted by users who have access to them

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.
