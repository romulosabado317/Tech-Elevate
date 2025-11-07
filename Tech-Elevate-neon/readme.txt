Tech-Elevate Purple-Neon — Final Build
-------------------------------------
1. Start XAMPP (Apache + MySQL).
2. Import your tech_elevate.sql into phpMyAdmin (database name: tech_elevate).
   - The package will ALTER the projects table to add a status column if missing.
3. Copy the folder 'Tech-Elevate-Neon' to C:\xampp\htdocs\
4. Ensure PHP can write to uploads/ and uploads/profile_photos/ (the app will create them).
5. Visit: http://localhost/Tech-Elevate-Neon/
Seeded accounts (from your SQL):
- Admin: tech@gmail.com / admin123
- Student: group1@gmail.com / demo123
Admin can mark projects inactive and deactivate/reactivate users.
Explore page is available to logged-in users only.


