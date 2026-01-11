# Vercel Deployment Guide - Supabase PostgreSQL

## Prerequisites

1. A Vercel account (sign up at https://vercel.com)
2. A Supabase account (already set up at https://supabase.com)

## Your Supabase Database

You already have a Supabase PostgreSQL database configured:
- **URL**: `https://bbronqbxbyfnhzisocbf.supabase.co`
- **Host**: `db.bbronqbxbyfnhzisocbf.supabase.co`
- **Database**: `postgres`

## Deployment Steps

### 1. Setup Database Tables in Supabase

1. Go to your Supabase dashboard: https://supabase.com/dashboard
2. Select your project: `bbronqbxbyfnhzisocbf`
3. Go to **SQL Editor**
4. Run this SQL to create the required tables:

```sql
-- Create winners table
CREATE TABLE IF NOT EXISTS winners (
    id SERIAL PRIMARY KEY,
    name_arabic VARCHAR(255) NOT NULL,
    name_english VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_created ON winners(created_at);

-- Create page visits tracking table
CREATE TABLE IF NOT EXISTS page_visits (
    id SERIAL PRIMARY KEY,
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
    platform VARCHAR(50),
    user_agent TEXT,
    referrer TEXT,
    touch_screen VARCHAR(10),
    orientation VARCHAR(50),
    gpu TEXT,
    ram VARCHAR(50),
    cpu_cores VARCHAR(20),
    device_type VARCHAR(20),
    device_model VARCHAR(100),
    pixel_ratio VARCHAR(20),
    connection_type VARCHAR(50),
    battery_level VARCHAR(20),
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_ip ON page_visits(ip_address);
CREATE INDEX IF NOT EXISTS idx_time ON page_visits(visit_time);
CREATE INDEX IF NOT EXISTS idx_country ON page_visits(country);
CREATE INDEX IF NOT EXISTS idx_tracked_emp ON page_visits(tracked_emp_id);

-- Create unique links table
CREATE TABLE IF NOT EXISTS unique_links (
    id SERIAL PRIMARY KEY,
    link_code VARCHAR(10) UNIQUE NOT NULL,
    employee_id VARCHAR(50) NOT NULL,
    name_arabic VARCHAR(255),
    name_english VARCHAR(255),
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

CREATE INDEX IF NOT EXISTS idx_link_code ON unique_links(link_code);
CREATE INDEX IF NOT EXISTS idx_employee_id ON unique_links(employee_id);
```

### 2. Push to GitHub

Your code is already on GitHub at: `https://github.com/Mustafa9o/prize-distribution-system`

### 3. Import to Vercel

1. Go to https://vercel.com/new
2. Import your GitHub repository: `Mustafa9o/prize-distribution-system`
3. Configure your project:
   - **Framework Preset**: Other
   - **Root Directory**: `./`
   - **Build Command**: Leave empty
   - **Output Directory**: Leave empty

### 4. Add Environment Variables

In Vercel project settings → Environment Variables, add:

```
POSTGRES_URL=postgres://postgres.bbronqbxbyfnhzisocbf:dGyx8HM5JjbZR2VM@aws-1-us-east-1.pooler.supabase.com:6543/postgres?sslmode=require&supa=base-pooler.x

POSTGRES_HOST=db.bbronqbxbyfnhzisocbf.supabase.co

POSTGRES_DATABASE=postgres

POSTGRES_USER=postgres

POSTGRES_PASSWORD=dGyx8HM5JjbZR2VM

NEXT_PUBLIC_SUPABASE_URL=https://bbronqbxbyfnhzisocbf.supabase.co

NEXT_PUBLIC_SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJicm9ucWJ4Ynlmbmh6aXNvY2JmIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NjgxMDkyNTcsImV4cCI6MjA4MzY4NTI1N30.04xk3gpX3h9FR43fBwpbG5xqDbjAvb3LXe_N1XLjLTc
```

**Important**: Make sure to select **Production**, **Preview**, and **Development** for each variable.

### 5. Deploy

Click **Deploy** in Vercel. Your site will be live at:
```
https://your-project-name.vercel.app
```

## Important Notes

1. **Database Connection**: The app automatically detects Supabase PostgreSQL in production
2. **SSL Connection**: Supabase requires SSL (already configured in the connection string)
3. **Local Development**: Still works with XAMPP/MySQL - no changes needed
4. **Auto-Detection**: Code automatically switches between MySQL (local) and PostgreSQL (production)

## Testing

After deployment:
1. Visit your Vercel URL
2. Test the form submission
3. Check Supabase dashboard → Table Editor to see entries

## Troubleshooting

### Database Connection Errors
- Verify all environment variables are set in Vercel
- Check that tables were created in Supabase SQL Editor
- Ensure SSL mode is included in `POSTGRES_URL`

### Function Timeout
- Vercel free tier: 10s timeout for serverless functions
- Supabase connection pooling helps with this

### Table Not Found
- Make sure you ran the SQL in Supabase SQL Editor
- Check the table name matches exactly (case-sensitive in PostgreSQL)

## Environment Variables Reference

| Variable | Value |
|----------|-------|
| POSTGRES_URL | `postgres://postgres.bbronqbxbyfnhzisocbf:dGyx8HM5JjbZR2VM@aws-1-us-east-1.pooler.supabase.com:6543/postgres?sslmode=require&supa=base-pooler.x` |
| POSTGRES_HOST | `db.bbronqbxbyfnhzisocbf.supabase.co` |
| POSTGRES_DATABASE | `postgres` |
| POSTGRES_USER | `postgres` |
| POSTGRES_PASSWORD | `dGyx8HM5JjbZR2VM` |
| NEXT_PUBLIC_SUPABASE_URL | `https://bbronqbxbyfnhzisocbf.supabase.co` |
| NEXT_PUBLIC_SUPABASE_ANON_KEY | Your anon key (see above) |

## Key Differences from MySQL

1. **SERIAL vs AUTO_INCREMENT**: PostgreSQL uses `SERIAL` for auto-increment
2. **Index Creation**: Separate statements in PostgreSQL
3. **Connection String**: Uses `pgsql:` instead of `mysql:`
4. **SSL Required**: Supabase requires SSL connections

## Local vs Production

- **Local (XAMPP)**: Uses MySQL automatically
- **Production (Vercel)**: Uses Supabase PostgreSQL automatically
- No code changes needed - the app detects the environment

## Support

For issues:
- Check Vercel deployment logs
- Check Supabase logs in dashboard
- Verify environment variables are set correctly

