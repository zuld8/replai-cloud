# Quick Test Guide - Public Ticket Tracking

## 🚀 Quick Start

### 1. Access the Page
```
http://127.0.0.1:8000/track-ticket
```

### 2. Test with Sample Ticket ID
Gunakan ticket ID yang sudah ada di database Anda, contoh:
- `TCK-2024-001`
- `TCK-2024-002`
- Atau ticket ID manapun dari database

### 3. Test API Directly
```bash
# Test valid ticket
curl http://127.0.0.1:8000/api/public/tickets/TCK-2024-001

# Test invalid ticket (should return 404)
curl http://127.0.0.1:8000/api/public/tickets/INVALID-123
```

## 🎨 Visual Features to Check

### ✅ On Load
- [ ] Modern gradient background
- [ ] Centered search box
- [ ] Clean header with logo
- [ ] Footer with links
- [ ] "How to Track" guide visible

### ✅ After Search
- [ ] Ticket details card dengan gradient header
- [ ] Status badge dengan warna sesuai
- [ ] Priority badge dengan warna sesuai  
- [ ] Category, Created date displayed
- [ ] Progress timeline dengan checkmarks
- [ ] Assigned agents dengan avatars
- [ ] Notes timeline (jika ada)
- [ ] Contact support CTA

### ✅ Error Handling
- [ ] Shows error untuk invalid ticket ID
- [ ] Shows error untuk empty search
- [ ] Loading spinner saat searching

### ✅ Responsive
- [ ] Mobile view (< 768px)
- [ ] Tablet view (768-1024px)
- [ ] Desktop view (> 1024px)

## 🔍 Testing Scenarios

### Scenario 1: Valid Ticket
1. Enter existing ticket ID
2. Click "Track"
3. **Expected**: Full ticket details display

### Scenario 2: Invalid Ticket
1. Enter "INVALID-123"
2. Click "Track"
3. **Expected**: Error message "Ticket not found"

### Scenario 3: Empty Search
1. Click "Track" without entering ID
2. **Expected**: Error message "Please enter a ticket ID"

### Scenario 4: URL Parameter
1. Visit: `http://127.0.0.1:8000/track-ticket?ticket_id=TCK-2024-001`
2. **Expected**: Automatically loads ticket details

### Scenario 5: Multiple Status
Test dengan tickets yang memiliki different status:
- Open
- In Progress
- Waiting
- Resolved
- Closed

## 🛠️ If Something Doesn't Work

### Vue Component Not Loading
```bash
cd c:\xampp\htdocs\whatsmail-azira
npm run dev
```

### Routes Not Working
```bash
php artisan route:clear
php artisan cache:clear
```

### API Returns Empty
```bash
# Check database
php artisan tinker
>>> \App\Models\Ticket\Ticket::first()
```

### Styling Issues
- Make sure Tailwind CSS is compiled
- Check browser console for errors
- Clear browser cache (Ctrl+Shift+R)

## 📸 Screenshot Checklist

Before/After untuk documentation:
- [ ] Landing page (empty state)
- [ ] Search in progress (loading)
- [ ] Ticket details (success)
- [ ] Error state
- [ ] Mobile view
- [ ] Timeline detail
- [ ] Agents section
- [ ] Notes section

## 🎯 Demo Flow

1. **Introduction**
   - Show landing page
   - Explain purpose

2. **Search Demo**
   - Enter ticket ID
   - Show loading state
   - Display results

3. **Feature Tour**
   - Point out status badge
   - Explain timeline
   - Show assigned team
   - Review notes

4. **Error Handling**
   - Demonstrate invalid search
   - Show error recovery

5. **Mobile Demo**
   - Resize browser
   - Show responsive layout

## ✨ Customization Tips

### Change Colors
Edit `TrackTicket.vue`:
```javascript
// Status colors in getStatusBadgeClass()
// Priority colors in getPriorityClass()
```

### Modify Timeline
Edit `statusTimeline` computed property to add/remove statuses

### Add More Info
Extend API response in `PublicTicketController.php`

### Branding
- Update logo in header
- Change gradient colors
- Modify footer links

## 🎉 Success Indicators

You'll know it's working when:
- ✅ Page loads without errors
- ✅ Search returns ticket data
- ✅ Timeline shows correct progression
- ✅ All sections display properly
- ✅ Mobile view is responsive
- ✅ API endpoints return JSON
- ✅ Error handling works correctly

---

**Ready to Test?**
1. Start your server: `php artisan serve`
2. Visit: `http://127.0.0.1:8000/track-ticket`
3. Enter a valid ticket ID
4. Click "Track"
5. Enjoy! 🎊
