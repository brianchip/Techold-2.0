# ERP Project Management Module - Techold Engineering

## Project Overview
Integrated Project Management Module within the ERP system to manage all stages of engineering, procurement, and construction projects. The module seamlessly connects with other ERP modules (CRM, Finance, Procurement, HR, SHEQ, Field Operations) and stores all project-related data in a structured, query-optimized backend database.

## Architecture
- **Backend**: Laravel 10.x (PHP 8.1+)
- **Database**: MySQL 8.0+ / PostgreSQL 14+
- **Frontend**: React 18+ with TypeScript
- **Authentication**: JWT with Role-Based Access Control
- **File Storage**: AWS S3 / Azure Blob Storage
- **Real-time Updates**: WebSocket / API Polling

## Key Features
- Project creation & setup with automated folder structure
- Work Breakdown Structure (WBS) with dependency management
- Budget & cost control linked to Finance module
- Resource allocation linked to HR module
- Document management with version control
- Risk & issue tracking linked to SHEQ module
- Real-time reporting and analytics
- Mobile-responsive design for field engineers

## Database Schema
### Core Tables
- **Projects**: Main project information
- **Tasks**: WBS structure with dependencies
- **Resources**: Human and equipment allocation
- **BudgetLines**: Cost tracking and variance analysis
- **Documents**: File management and versioning
- **Risks**: Risk assessment and mitigation

## API Endpoints
- `/api/projects` - Project CRUD operations
- `/api/tasks` - Task management
- `/api/resources` - Resource allocation
- `/api/budgets` - Budget tracking
- `/api/documents` - File management
- `/api/risks` - Risk management

## Installation & Setup
1. Clone the repository
2. Install dependencies: `composer install` (backend), `npm install` (frontend)
3. Configure environment variables
4. Run database migrations
5. Start development servers

## Development Phases
1. **Phase 1**: Project structure, database design, basic APIs
2. **Phase 2**: Core functionality, authentication, file management
3. **Phase 3**: Integration with other ERP modules, testing, deployment

## Contributing
Follow the established coding standards and ensure all changes are tested before submission.
