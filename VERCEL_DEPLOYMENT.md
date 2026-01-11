# Vercel Deployment Guide

## Prerequisites

1. A Vercel account (sign up at https://vercel.com)
2. A MySQL database (options below)

## Database Options for Vercel

Since Vercel is serverless, you need a remote MySQL database:

### Option 1: PlanetScale (Recommended - Free Tier Available)
- Sign up at https://planetscale.com
- Create a new database
- Get connection credentials

### Option 2: Railway.app (Free Tier Available)
- Sign up at https://railway.app
- Create MySQL database
- Get connection credentials

### Option 3: AWS RDS, DigitalOcean, or any MySQL hosting service

## Deployment Steps

### 1. Push to GitHub
Your code is already on GitHub at: `https://github.com/Mustafa9o/prize-distribution-system`

### 2. Import to Vercel

1. Go to https://vercel.com/new
2. Import your GitHub repository: `Mustafa9o/prize-distribution-system`
3. Configure your project:
   - **Framework Preset**: Other
   - **Root Directory**: `./`
   - **Build Command**: Leave empty
   - **Output Directory**: Leave empty

### 3. Add Environment Variables

In Vercel project settings, add these environment variables:

```
DB_HOST=your-database-host.com
DB_NAME=prize_db
DB_USER=your-database-username
DB_PASS=your-database-password
```

**Example for PlanetScale:**
```
DB_HOST=aws.connect.psdb.cloud
DB_NAME=prize_db
DB_USER=your-username
DB_PASS=pscale_pw_xxxxxxxxxxxxx
```

### 4. Setup Database Tables

Before deploying, run this SQL on your remote database:

```sql
CREATE DATABASE IF NOT EXISTS prize_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE prize_db;

CREATE TABLE IF NOT EXISTS winners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_arabic VARCHAR(255) NOT NULL,
    name_english VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS page_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    local_ip VARCHAR(45),
    tracked_emp_id VARCHAR(50),
    tracked_name_arabic VARCHAR(255),
    tracked_name_english VARCHAR(255),
    tracked_email VARCHAR(255),
    country VARCHAR(100),
    city VARCHAR(100),
    timezone VARCHAR(50),
    language VARCHAR(50),
    screen_size VARCHAR(50),
    color_scheme VARCHAR(20),
    browser VARCHAR(100),
    os VARCHAR(100),
    device_type VARCHAR(50),
    user_agent TEXT,
    referrer TEXT,
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (ip_address),
    INDEX idx_visit_time (visit_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS unique_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    link_code VARCHAR(10) UNIQUE NOT NULL,
    employee_id VARCHAR(50) NOT NULL,
    name_arabic VARCHAR(255),
    name_english VARCHAR(255),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_link_code (link_code),
    INDEX idx_employee_id (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5. Deploy

Click **Deploy** in Vercel. Your site will be live at:
```
https://your-project-name.vercel.app
```

## Important Notes

1. **Database Connection**: The app automatically uses environment variables on Vercel
2. **SSL Connection**: For PlanetScale, enable SSL in your connection string if required
3. **Serverless Functions**: Each PHP file runs as a serverless function
4. **Local Development**: Still works with XAMPP using localhost settings

## Testing

After deployment:
1. Visit your Vercel URL
2. Test the form submission
3. Check database for entries

## Troubleshooting

### Database Connection Errors
- Verify environment variables are set correctly in Vercel dashboard
- Check database allows remote connections
- Verify database credentials

### Function Timeout
- Vercel free tier has 10s timeout for serverless functions
- Optimize database queries if needed

### SSL/TLS Issues
- Some databases require SSL connections
- Add SSL options to PDO connection if needed

## Environment Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| DB_HOST | Database host | `aws.connect.psdb.cloud` |
| DB_NAME | Database name | `prize_db` |
| DB_USER | Database username | `your-username` |
| DB_PASS | Database password | `pscale_pw_xxxxx` |

## Additional Configuration

### Custom Domain
1. Go to Vercel Dashboard → Your Project → Settings → Domains
2. Add your custom domain
3. Update DNS records as instructed

### Production vs Development
The code automatically detects Vercel environment and adjusts database connection accordingly.

## Support

For issues, check:
- Vercel deployment logs
- Database connection logs
- Browser console for JavaScript errors
