# Security Policy

## Overview
This document outlines the security policies and procedures implemented in the PC Parts Inventory Management System.

---

## 1. Password Policy

### Requirements
- **Minimum Length**: 8 characters
- **Complexity Requirements**:
  - At least one uppercase letter (A-Z)
  - At least one lowercase letter (a-z)
  - At least one number (0-9)
- **Password Hashing**: bcrypt with configurable rounds (default: 12)

### Password Management
- Users can reset passwords via email verification
- Administrators can force password changes for users
- Failed login attempts are tracked and logged

---

## 2. Access Policy

### Role-Based Access Control (RBAC)
The system implements two primary roles:

| Role | Access Level |
|------|--------------|
| **Admin** | Full system access including user management, database management, security settings |
| **Employee** | Standard access to inventory, products, suppliers, reports |

### Protected Routes
- `/users/*` - Admin only
- `/database/*` - Admin only
- `/security/*` - Admin only
- `/audit-logs/*` - Authenticated users

### Session Management
- Sessions stored in database
- Session encryption enabled
- Session timeout: 120 minutes (configurable)
- Ability to terminate active sessions

---

## 3. Logging Policy

### What is Logged
- **Login Events**: Successful logins, failed attempts, blocked attempts
- **User Actions**: CRUD operations on all models
- **System Events**: Database backups, restores, optimizations
- **Security Events**: Account lockouts, password changes, role changes

### Log Retention
- Audit logs are retained indefinitely
- Login history is maintained for security analysis
- Logs can be exported for compliance reporting

### Log Format
Each audit log entry contains:
- Timestamp
- User ID
- Action performed
- Model type affected
- Old values (for updates)
- New values (for creates/updates)
- IP address

---

## 4. Backup Strategy

### Automated Backups
- Database backups can be created manually through the admin interface
- Backup files are stored in `storage/app/backups/`

### Backup Contents
- Full database schema
- All table data
- Backup metadata (timestamp, size, user who created)

### Restoration
- Backups can be restored through the admin interface
- Restoration requires admin privileges
- All restoration events are logged

### Recommendations
- Create daily backups
- Store backups in multiple locations
- Test restoration procedures periodically
- Retain backups for at least 30 days

---

## 5. Incident Response Plan

### Detection
1. Monitor audit logs for suspicious activity
2. Review failed login attempts daily
3. Check for unusual patterns in user behavior

### Response Levels

#### Level 1: Minor Incident
- Failed login attempts from unknown sources
- **Action**: Monitor and document

#### Level 2: Moderate Incident
- Multiple failed login attempts from same source
- Unauthorized access attempts
- **Action**: 
  - Block IP if necessary
  - Review affected accounts
  - Notify administrator

#### Level 3: Critical Incident
- Successful unauthorized access
- Data breach suspected
- System compromise
- **Action**:
  - Immediately disable affected accounts
  - Isolate affected systems
  - Preserve logs and evidence
  - Notify stakeholders
  - Initiate full investigation

### Recovery Steps
1. Identify scope of incident
2. Contain the threat
3. Eradicate the threat
4. Recover systems from clean backups
5. Document lessons learned
6. Update security measures

### Contact Information
- System Administrator: [Contact info to be updated]
- Security Team: [Contact info to be updated]

---

## 6. Data Protection

### Sensitive Data Handling
- Passwords are never stored in plain text
- All passwords hashed using bcrypt
- Session data encrypted
- CSRF protection on all forms

### Database Security
- Database credentials stored in environment variables
- Parameterized queries via Eloquent ORM
- No raw SQL queries with user input

### Transport Security
- HTTPS recommended for production
- Secure cookies enabled
- HTTP-only cookies for session

---

## 7. Authentication Security

### Login Security Features
- Math CAPTCHA on login form
- Account lockout after failed attempts (configurable)
- Login history tracking
- IP address logging

### Account Lockout Policy
- Maximum failed attempts: Configurable (default: 5)
- Lockout duration: Configurable (default: 15 minutes)
- Administrators can manually unlock accounts

---

## 8. Compliance

### Implemented Controls
- [x] Secure credential storage
- [x] Password complexity requirements
- [x] Role-based access control
- [x] Audit logging
- [x] Session management
- [x] CSRF protection
- [x] Input validation

### Regular Review
- Security policies should be reviewed quarterly
- Access rights should be audited monthly
- Logs should be reviewed weekly

---

## Document Information
- **Version**: 1.0
- **Last Updated**: March 2026
- **Next Review**: June 2026
