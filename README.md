# 🕌 Madrassah Management System

A comprehensive, modern web-based management system designed specifically for Islamic educational institutions. Built with PHP, MySQL, HTML, CSS, and JavaScript.

## ✨ Features

### 🎯 Multi-Role Support
- **Super Admin**: System-wide management and analytics
- **Madrassah Admin**: Complete institution management
- **Teacher**: Class management and student assessment
- **Student**: Progress tracking and course materials
- **Parent**: Child progress monitoring
- **Donor**: Sponsorship and donation management

### 🏛️ Core Functionality

#### 📚 Academic Management
- **Student Registration & Management**
- **Teacher Assignment & Scheduling**
- **Class & Subject Organization**
- **Syllabus Content Management** (PDFs, videos, notes)
- **Exam Conduction & Results**
- **Certificate Generation**

#### 📖 Qur'an Memorization Tracking
- **Progress Monitoring** by Surah and verses
- **Tajweed Assessment** (1-5 rating scale)
- **Milestone Certificates**
- **Detailed Progress Reports**

#### 📊 Analytics & Reporting
- **Attendance Tracking**
- **Performance Analytics**
- **System-wide Statistics**
- **Monthly Trends & Charts**
- **Printable Reports**

#### 💝 Sponsorship System
- **Student & Teacher Sponsorship**
- **Donation Management**
- **Impact Reporting**
- **Multi-Madrassah Support**

#### 💬 Communication
- **Internal Messaging System**
- **Parent-Teacher Communication**
- **Announcements & Notifications**

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL 5.7+ / MariaDB 10.3+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **UI Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6.0
- **Charts**: Chart.js
- **AJAX**: jQuery 3.6

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.3+
- Web server (Apache/Nginx)
- Modern web browser

### Installation

1. **Clone or Download**
   ```bash
   git clone https://github.com/your-repo/madrassah-management.git
   cd madrassah-management
   ```

2. **Setup Web Server**
   - Point your web server document root to the project folder
   - Ensure PHP has write permissions to `config/` and `uploads/` directories

3. **Run Installation**
   - Open your browser and navigate to `http://your-domain/install.php`
   - Follow the installation wizard
   - Configure your database connection
   - The installer will create the database and demo data automatically

4. **Access the System**
   - Visit `http://your-domain/` after installation
   - Use the demo accounts provided during installation

### Demo Accounts

After installation, you can login with these demo accounts:

| Role | Username | Password | Description |
|------|----------|----------|-------------|
| Super Admin | `superadmin` | `admin123` | Full system access |
| Madrassah Admin | `admin` | `admin123` | Madrassah management |
| Teacher | `teacher` | `teacher123` | Teaching interface |
| Student | `student` | `student123` | Student portal |
| Parent | `parent` | `parent123` | Parent dashboard |
| Donor | `donor` | `donor123` | Donation interface |

## 📁 Project Structure

```
madrassah-management/
├── 📁 assets/                 # Static assets
│   ├── 📁 css/               # Custom stylesheets
│   ├── 📁 js/                # JavaScript files
│   └── 📁 images/            # Images and media
├── 📁 config/                # Configuration files
│   └── database.php          # Database configuration
├── 📁 controllers/           # PHP controllers
│   └── logout.php            # Authentication controllers
├── 📁 database/              # Database files
│   └── madrassah_db.sql      # Database schema
├── 📁 includes/              # Common PHP includes
│   ├── auth.php              # Authentication system
│   ├── header.php            # Common header
│   └── footer.php            # Common footer
├── 📁 uploads/               # File uploads
│   ├── 📁 syllabus/         # Course materials
│   ├── 📁 certificates/     # Generated certificates
│   └── 📁 logos/            # Institution logos
├── 📁 views/                 # User interface views
│   ├── 📁 auth/             # Login/Registration
│   ├── 📁 super_admin/      # Super admin interface
│   ├── 📁 madrassah_admin/  # Madrassah admin interface
│   ├── 📁 teacher/          # Teacher interface
│   ├── 📁 student/          # Student interface
│   ├── 📁 parent/           # Parent interface
│   ├── 📁 donor/            # Donor interface
│   └── 📁 shared/           # Shared components
├── index.php                # Main landing page
├── install.php              # Installation script
└── README.md                # This file
```

## 🎨 Key Features Walkthrough

### 🔐 Authentication System
- Secure password hashing (PHP `password_hash()`)
- Session management with timeout
- CSRF protection
- Role-based access control
- Remember me functionality

### 📊 Super Admin Dashboard
- System-wide statistics
- User distribution charts
- Registration trends
- Top-performing madrassahs
- Quick management actions

### 🏫 Madrassah Management
- Complete student lifecycle management
- Teacher assignment and scheduling
- Class and subject organization
- Syllabus content upload (PDF, video, audio)
- Exam creation and result management

### 📖 Qur'an Progress Tracking
- Surah-by-surah progress monitoring
- Verse-level memorization tracking
- Tajweed quality assessment
- Progress visualization
- Achievement certificates

### 💰 Donation & Sponsorship
- Student and teacher sponsorship programs
- Donation tracking and management
- Impact reporting for donors
- Multi-currency support
- Recurring donation setup

## 🎯 User Roles & Permissions

### Super Admin
- ✅ Manage multiple madrassahs
- ✅ Assign madrassah administrators
- ✅ System-wide analytics and reporting
- ✅ Global settings management
- ✅ User management across all institutions

### Madrassah Admin
- ✅ Student registration and management
- ✅ Teacher recruitment and assignment
- ✅ Class and schedule creation
- ✅ Syllabus content management
- ✅ Exam conduction and certificates
- ✅ Progress tracking and reporting

### Teacher
- ✅ View personal teaching schedule
- ✅ Mark student attendance
- ✅ Input Qur'an progress assessments
- ✅ Grade assignments and exams
- ✅ Communicate with students and parents

### Student
- ✅ View personal progress reports
- ✅ Access class schedules
- ✅ Download syllabus materials
- ✅ View certificates and achievements
- ✅ Track Qur'an memorization progress

### Parent
- ✅ Monitor child's academic progress
- ✅ View attendance records
- ✅ Receive notifications and updates
- ✅ Communicate with teachers
- ✅ Access progress reports

### Donor
- ✅ Browse available madrassahs
- ✅ Sponsor students or teachers
- ✅ View impact reports
- ✅ Track donation history
- ✅ Receive updates from sponsored institutions

## 🔧 Configuration

### Database Configuration
Edit `config/database.php` after installation:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'madrassah_management');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Upload Settings
Configure file upload limits in `includes/auth.php`:
- Maximum file size: 10MB (configurable)
- Allowed file types: PDF, DOC, DOCX, MP4, MP3, JPG, JPEG, PNG

## 🎨 Customization

### Themes & Styling
- Edit `assets/css/style.css` for custom styling
- Modify CSS variables in `:root` for color scheme changes
- Islamic-themed color palette included by default

### Adding New Features
1. Create new database tables in `database/`
2. Add corresponding PHP controllers in `controllers/`
3. Create views in appropriate `views/` subdirectory
4. Update navigation in `includes/header.php`

## 🚀 Performance Optimization

### Database Optimization
- Indexed columns for faster queries
- Optimized JOIN operations
- Pagination for large datasets

### Frontend Optimization
- Minified CSS and JavaScript
- Image optimization recommendations
- Lazy loading for better performance

### Caching
- Browser caching headers
- Database query optimization
- Static asset caching

## 🔒 Security Features

- **SQL Injection Protection**: PDO prepared statements
- **XSS Prevention**: Input sanitization and output escaping
- **CSRF Protection**: Token-based form security
- **Session Security**: Secure session configuration
- **Password Security**: Strong hashing algorithms
- **File Upload Security**: Type and size validation

## 🌍 Internationalization

The system is designed with internationalization in mind:
- UTF-8 support for Arabic text
- RTL layout considerations
- Multi-language support structure
- Islamic calendar integration ready

## 📱 Mobile Responsiveness

- Fully responsive design using Bootstrap 5
- Mobile-first approach
- Touch-friendly interface
- Optimized for tablets and smartphones

## 🧪 Testing

### Manual Testing
- Test all user roles and permissions
- Verify database operations
- Check file upload functionality
- Validate form submissions

### Browser Compatibility
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 💝 Support

If you find this project helpful, please consider:
- ⭐ Starring the repository
- 🐛 Reporting bugs and issues
- 💡 Suggesting new features
- 🤝 Contributing to the codebase

## 📞 Contact

For questions, suggestions, or support:
- Email: support@madrassah-management.com
- GitHub Issues: [Create an issue](https://github.com/your-repo/madrassah-management/issues)

---

**"وَقُل رَّبِّ زِدْنِي عِلْمًا"**  
*"And say: My Lord, increase me in knowledge." - Qur'an 20:114*

Built with ❤️ for the Islamic education community.