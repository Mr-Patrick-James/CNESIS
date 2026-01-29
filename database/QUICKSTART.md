# 🚀 QUICK START GUIDE - Database Setup

## ⚡ 3-Step Setup (5 Minutes)

### **STEP 1: Start WAMP** ✅
1. Click WAMP icon in system tray
2. Wait until icon turns **GREEN**
3. If not green, click → Start All Services

### **STEP 2: Import Database** ✅
1. Open browser → `http://localhost/phpmyadmin`
2. Click **SQL** tab at the top
3. Open file: `c:\wamp64\www\CNESIS\database\setup.sql`
4. **Select ALL** (Ctrl+A) → **Copy** (Ctrl+C)
5. **Paste** into phpMyAdmin SQL box (Ctrl+V)
6. Click **Go** button (bottom right)
7. Wait for green success messages ✅

### **STEP 3: Test Everything** ✅

**Test 1: Check Database**
- In phpMyAdmin left sidebar, you should see `cnesis_db`
- Click on it → should show 4 tables
- Click `programs` table → Browse → should show 4 programs

**Test 2: Test API**
- Open browser → `http://localhost/CNESIS/api/programs/get-all.php`
- Should see JSON with 4 programs
- If you see data = **Working!** ✅

**Test 3: Test Website**
- Go to: `http://localhost/CNESIS/views/user/program.html`
- Programs should load automatically
- Should see 4 program cards
- Click "Download Prospectus" on BSIS or BPA
- Click "View Details" to see modal

**Test 4: Test Admin Login**
- Go to: `http://localhost/CNESIS/index.html`
- Click **LOGIN** button
- Click **Demo Login** or enter:
  - Username: `admin_demo@colegio.edu`
  - Password: `demo123`
- Should redirect to admin dashboard

---

## ✅ What You Now Have

### **Database: `cnesis_db`**
- ✅ 4 Tables created
- ✅ 4 Programs loaded (BSIS, BPA, BTVTED-CHS, BTVTED-WFT)
- ✅ 1 Admin user created
- ✅ Ready for production

### **Backend: PHP API**
- ✅ `api/config/database.php` - Database connection
- ✅ `api/programs/get-all.php` - Fetch all programs
- ✅ `api/programs/get-one.php` - Fetch single program
- ✅ `api/programs/create.php` - Add new program
- ✅ `api/programs/update.php` - Update program
- ✅ `api/programs/delete.php` - Delete program
- ✅ `api/programs/upload-prospectus.php` - Upload files
- ✅ `api/programs/upload-image.php` - Upload images

### **Frontend: Dynamic Loading**
- ✅ `assets/js/programs-loader.js` - Loads from database
- ✅ `views/user/program.html` - Shows programs dynamically
- ✅ Prospectus download buttons working
- ✅ Program details modal working

---

## 🎯 What's Next

### **Immediate Next Steps:**
1. ✅ Database is set up
2. ✅ API is working
3. ✅ Frontend loads from database
4. 🔄 **Build admin dashboard UI** (next task)
5. 🔄 **Add program management forms**
6. 🔄 **Test file uploads**

---

## 🔧 Troubleshooting

### ❌ Error: "Database connection failed"
**Solution:**
- Check WAMP icon is green
- Open `api/config/database.php`
- Verify: `$username = "root"` and `$password = ""`

### ❌ Error: "Table doesn't exist"
**Solution:**
- You didn't import the SQL file yet
- Go back to STEP 2 above
- Make sure you copied **ALL** content from setup.sql

### ❌ Programs not loading on website
**Solution:**
1. Press F12 in browser → Check Console tab
2. Look for red errors
3. Test API directly: `http://localhost/CNESIS/api/programs/get-all.php`
4. If API shows error, database wasn't imported correctly

### ❌ Can't login to admin
**Solution:**
- Username must be: `admin_demo@colegio.edu`
- Password must be: `demo123`
- Check database has user: `SELECT * FROM users;` in phpMyAdmin

---

## 📊 Database Structure

```
cnesis_db
├── programs (4 records)
│   ├── BSIS - BS Information Systems
│   ├── BPA - Bachelor of Public Administration
│   ├── BTVTED-CHS - Computer Hardware Servicing
│   └── BTVTED-WFT - Welding & Fabrication Technology
│
├── admissions (0 records - ready for student applications)
├── users (1 record - admin account)
└── inquiries (0 records - ready for contact form)
```

---

## 🎓 Admin Credentials

**Default Admin Account:**
- Username: `admin_demo@colegio.edu`
- Password: `demo123`
- Role: Admin

⚠️ **IMPORTANT:** Change this password in production!

---

## 📝 File Locations

```
CNESIS/
├── database/
│   ├── setup.sql ← Import this file
│   └── QUICKSTART.md ← You are here
│
├── api/
│   ├── config/
│   │   └── database.php ← Database connection
│   └── programs/
│       ├── get-all.php ← Fetch programs
│       ├── create.php ← Add program
│       ├── update.php ← Edit program
│       ├── delete.php ← Delete program
│       ├── upload-prospectus.php ← Upload files
│       └── upload-image.php ← Upload images
│
└── assets/
    └── js/
        └── programs-loader.js ← Dynamic loading
```

---

## ✨ Success Checklist

- [ ] WAMP icon is green
- [ ] Database `cnesis_db` exists in phpMyAdmin
- [ ] 4 tables created (programs, admissions, users, inquiries)
- [ ] 4 programs visible in programs table
- [ ] API returns JSON: `http://localhost/CNESIS/api/programs/get-all.php`
- [ ] Programs page loads: `http://localhost/CNESIS/views/user/program.html`
- [ ] Can login with admin credentials
- [ ] Prospectus download buttons work

**If all checked ✅ = You're ready to go!**

---

## 🆘 Need Help?

1. Check WAMP is running (green icon)
2. Check browser console (F12) for errors
3. Test API endpoint directly
4. Verify database exists in phpMyAdmin
5. Check PHP error logs in WAMP folder

---

**Setup Time: ~5 minutes**  
**Last Updated: January 2026**
