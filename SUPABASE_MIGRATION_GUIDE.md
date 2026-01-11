# Supabase Migration Guide

## Step 1: Create Tables in Supabase

1. Go to your Supabase dashboard: https://supabase.com/dashboard/project/bbronqbxbyfnhzisocbf
2. Click on **SQL Editor** in the left sidebar
3. Click **New Query**
4. Copy and paste this SQL:

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

5. Click **Run** button
6. You should see "Success. No rows returned"

## Step 2: Import Your Data

1. In the same SQL Editor, click **New Query**
2. Open the file: `supabase_migration.sql` (located in your prize folder)
3. Copy ALL the contents
4. Paste into the SQL Editor
5. Click **Run**
6. Your data will be imported!

## Step 3: Verify Data Import

1. Go to **Table Editor** in Supabase dashboard
2. Click on `winners` table - you should see 15 records
3. Click on `page_visits` table - you should see 100 records (limited export)
4. Click on `unique_links` table - check if you have any links

## Step 4: Enable Row Level Security (Optional but Recommended)

In SQL Editor, run:

```sql
-- Enable RLS on all tables
ALTER TABLE winners ENABLE ROW LEVEL SECURITY;
ALTER TABLE page_visits ENABLE ROW LEVEL SECURITY;
ALTER TABLE unique_links ENABLE ROW LEVEL SECURITY;

-- Create policies to allow service role to do everything
CREATE POLICY "Allow service role full access to winners" ON winners
    FOR ALL USING (true);

CREATE POLICY "Allow service role full access to page_visits" ON page_visits
    FOR ALL USING (true);

CREATE POLICY "Allow service role full access to unique_links" ON unique_links
    FOR ALL USING (true);
```

## Step 5: Test Connection

Your app is already configured! Just test it:
- Local (XAMPP): Uses MySQL automatically
- Vercel (Production): Uses Supabase PostgreSQL automatically

## Files Generated

- **supabase_migration.sql** - Contains all your data ready to import
- **check_data.php** - Check what's in your local database
- **migrate_to_supabase.php** - Generate fresh migration SQL anytime

## Quick Links

- Supabase Dashboard: https://supabase.com/dashboard/project/bbronqbxbyfnhzisocbf
- SQL Editor: https://supabase.com/dashboard/project/bbronqbxbyfnhzisocbf/sql
- Table Editor: https://supabase.com/dashboard/project/bbronqbxbyfnhzisocbf/editor

## Summary

✅ Migration SQL file created: `supabase_migration.sql`
✅ Contains 15 winners records
✅ Contains 100 page visits (first 100 records)
✅ Ready to import into Supabase
✅ Your app is already configured for Supabase

Just follow the steps above to complete the migration!
