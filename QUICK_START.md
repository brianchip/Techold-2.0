# Quick Start Guide - ERP Project Management Module

## Prerequisites

- PHP 8.1+
- Composer 2.0+
- Node.js 18+
- npm or yarn
- MySQL 8.0+ or PostgreSQL 14+
- Git

## Backend Setup (Laravel)

### 1. Clone and Install Dependencies
```bash
# Navigate to project directory
cd TECHOLD-2.0

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env
```

### 2. Environment Configuration
Edit `.env` file with your database and service configurations:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=techold_erp
DB_USERNAME=your_username
DB_PASSWORD=your_password

# File Storage (AWS S3)
AWS_ACCESS_KEY_ID=your_access_key
AWS_SECRET_ACCESS_KEY=your_secret_key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name

# JWT Secret
JWT_SECRET=your_jwt_secret_key

# App Settings
APP_NAME="Techold ERP Project Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

### 3. Database Setup
```bash
# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed database with sample data (optional)
php artisan db:seed

# Start development server
php artisan serve
```

The backend will be available at `http://localhost:8000`

## Frontend Setup (React)

### 1. Install Dependencies
```bash
# Navigate to frontend directory (if separate)
cd frontend

# Install Node.js dependencies
npm install
```

### 2. Environment Configuration
Create `.env` file in the frontend directory:

```env
REACT_APP_API_URL=http://localhost:8000/api
REACT_APP_APP_NAME=Techold ERP Project Management
```

### 3. Start Development Server
```bash
npm start
```

The frontend will be available at `http://localhost:3000`

## Database Structure

### Core Tables
- **projects**: Main project information
- **tasks**: Work breakdown structure
- **resources**: Human and equipment allocation
- **budget_lines**: Cost tracking and variance
- **documents**: File management and versioning
- **risks**: Risk assessment and mitigation

### Key Relationships
- Projects → Tasks (one-to-many)
- Tasks → Resources (one-to-many)
- Projects → Budget Lines (one-to-many)
- Projects → Documents (one-to-many)
- Projects → Risks (one-to-many)

## API Endpoints

### Authentication
- `POST /api/login` - User login
- `POST /api/register` - User registration
- `POST /api/logout` - User logout

### Projects
- `GET /api/projects` - List projects with filtering
- `POST /api/projects` - Create new project
- `GET /api/projects/{id}` - Get project details
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project
- `GET /api/projects/statistics` - Get project statistics

### Tasks
- `GET /api/projects/{id}/tasks` - Get project tasks
- `POST /api/projects/{id}/tasks` - Create project task

### Resources
- `GET /api/projects/{id}/resources` - Get project resources
- `POST /api/projects/{id}/resources` - Allocate resource

## Development Workflow

### 1. Feature Development
```bash
# Create feature branch
git checkout -b feature/project-management

# Make changes and commit
git add .
git commit -m "Add project management feature"

# Push and create pull request
git push origin feature/project-management
```

### 2. Testing
```bash
# Backend tests
php artisan test

# Frontend tests
npm test

# Run all tests
npm run test:all
```

### 3. Code Quality
```bash
# Backend code style
./vendor/bin/pint

# Frontend code style
npm run lint
npm run format
```

## Key Features to Implement

### Phase 1 (Weeks 1-3)
- [x] Project structure and database design
- [x] Basic API endpoints
- [x] Frontend foundation
- [ ] Authentication system
- [ ] User management

### Phase 2 (Weeks 4-7)
- [ ] Project CRUD operations
- [ ] Task management with WBS
- [ ] Resource allocation
- [ ] Budget management
- [ ] Document management
- [ ] Risk management

### Phase 3 (Weeks 8-10)
- [ ] Reporting and analytics
- [ ] ERP module integration
- [ ] Real-time updates
- [ ] Mobile responsiveness

## Common Issues & Solutions

### Backend Issues

#### Database Connection
```bash
# Check database connection
php artisan tinker
DB::connection()->getPdo();

# Clear cache
php artisan config:clear
php artisan cache:clear
```

#### Migration Issues
```bash
# Reset database
php artisan migrate:fresh

# Rollback specific migration
php artisan migrate:rollback --step=1
```

#### Permission Issues
```bash
# Set storage permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Frontend Issues

#### Build Errors
```bash
# Clear build cache
rm -rf build
rm -rf node_modules/.cache

# Reinstall dependencies
rm -rf node_modules
npm install
```

#### API Connection Issues
- Check CORS configuration in Laravel
- Verify API URL in environment variables
- Check network tab for error details

## Performance Optimization

### Backend
- Implement database query caching
- Use eager loading for relationships
- Implement API response caching
- Optimize database indexes

### Frontend
- Implement React.memo for components
- Use React Query for data caching
- Implement lazy loading for routes
- Optimize bundle size with code splitting

## Security Considerations

### Authentication
- JWT token expiration
- Refresh token rotation
- Rate limiting on API endpoints
- Input validation and sanitization

### Data Protection
- Encrypt sensitive data at rest
- Use HTTPS for all communications
- Implement role-based access control
- Regular security audits

## Deployment Checklist

### Production Environment
- [ ] Environment variables configured
- [ ] Database optimized and indexed
- [ ] SSL certificates installed
- [ ] File storage configured
- [ ] Monitoring and logging setup

### Application
- [ ] Debug mode disabled
- [ ] Error reporting configured
- [ ] Performance monitoring enabled
- [ ] Backup procedures in place
- [ ] Rollback procedures tested

## Support & Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://reactjs.org/docs)
- [Material-UI Documentation](https://mui.com/material-ui/)

### Community
- Laravel Discord/Slack channels
- React community forums
- Stack Overflow for specific issues

### Internal Resources
- Project BRD document
- Implementation plan
- API documentation
- Database schema documentation

## Next Steps

1. **Complete Phase 1**: Finish authentication and basic setup
2. **Begin Phase 2**: Implement core project management features
3. **Set up CI/CD**: Automated testing and deployment pipeline
4. **Performance Testing**: Load testing with realistic data
5. **User Acceptance Testing**: Gather feedback from stakeholders

## Contact

For technical questions or support:
- **Development Team**: [team@techold.com]
- **Project Manager**: [pm@techold.com]
- **Documentation**: [docs.techold.com]

---

**Happy Coding! 🚀**
