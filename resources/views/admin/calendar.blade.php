<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Kalender Konsultasi Dokter — Klinik Maristella</title>
<link rel="icon" type="image/png" href="{{ asset('images/maristella-icon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-page: #ffffff;
    --bg-muted: #f8fafc;
    --bg-subtle: #f1f5f9;
    --border: #e2e8f0;
    --border-light: #f1f5f9;
    
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-tertiary: #94a3b8;
    --icon-default: #64748b;
    
    /* Klinik Maristella Navy Brand Colors */
    --brand-primary: #012D68;
    --brand-primary-hover: #083E88;
    --brand-primary-tint: #EFF6FF;
    --brand-dark: #001F4D;
    --action-bg: #012D68;
    --action-text: #ffffff;
    
    /* Status Colors (Green, Red, Yellow, Gray) */
    --status-available-bg: #ECFDF5;
    --status-available-border: #6EE7B7;
    --status-available-text: #065F46;
    --status-available-badge: #D1FAE5;
    --status-available-dot: #10B981;

    --status-full-bg: #FEF2F2;
    --status-full-border: #FCA5A5;
    --status-full-text: #991B1B;
    --status-full-badge: #FEE2E2;
    --status-full-dot: #EF4444;

    --status-pending-bg: #FFFBEB;
    --status-pending-border: #FCD34D;
    --status-pending-text: #92400E;
    --status-pending-badge: #FEF3C7;
    --status-pending-dot: #F59E0B;

    --status-cancelled-bg: #F1F5F9;
    --status-cancelled-border: #CBD5E1;
    --status-cancelled-text: #64748B;
    --status-cancelled-badge: #E2E8F0;
    --status-cancelled-dot: #94A3B8;

    --font-sans: "Plus Jakarta Sans", "Inter", system-ui, -apple-system, sans-serif;
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 14px;
    --radius-full: 999px;
    --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.05);
    --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
    --shadow-lg: 0 12px 28px rgba(15, 23, 42, 0.12);
    
    --sidebar-w: 240px;
    --panel-w: 280px;
    --hour-h: 68px;
  }
  
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: var(--font-sans);
    font-size: 13px;
    line-height: 20px;
    color: var(--text-secondary);
    background: var(--bg-muted);
    -webkit-font-smoothing: antialiased;
  }
  button { font: inherit; color: inherit; background: none; border: 0; cursor: pointer; }
  input, select, textarea { font: inherit; color: inherit; }
  svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.6; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
  :focus-visible { outline: 2px solid var(--brand-primary); outline-offset: 2px; }

  html { scrollbar-gutter: stable; }

  .app {
    display: flex;
    min-height: 100vh;
    background: var(--bg-page);
    max-width: 1440px;
    width: 100%;
    margin: 0 auto;
  }

  /* ===== Sidebar ===== */
  .sidebar {
    width: var(--sidebar-w);
    flex-shrink: 0;
    border-right: 1px solid var(--border);
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: var(--bg-page);
    position: sticky;
    top: 0;
    height: 100vh;
    box-sizing: border-box;
    z-index: 20;
  }
  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 24px;
    padding: 0;
  }
  .brand img {
    height: 36px;
    width: auto;
    object-fit: contain;
  }
  .role-indicator-badge {
    font-size: 10px;
    font-weight: 700;
    background: var(--brand-primary-tint);
    border: 1px solid #BFDBFE;
    color: var(--brand-primary);
    border-radius: 4px;
    padding: 2px 6px;
    margin-left: auto;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .sidebar-nav {
    display: flex;
    flex-direction: column;
    gap: 4px;
    width: 100%;
  }
  .sidebar-nav .nav-item,
  .sidebar-nav button.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 12px;
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    text-align: left;
    width: 100%;
    background: transparent;
    border: none;
    cursor: pointer;
    box-sizing: border-box;
    transition: background-color 0.15s ease, color 0.15s ease;
  }
  .sidebar-nav .nav-item:hover {
    background-color: var(--brand-primary-tint);
    color: var(--brand-primary);
  }
  .sidebar-nav .nav-item.active {
    background-color: var(--brand-primary-tint);
    color: var(--brand-primary);
    font-weight: 600;
  }
  .sidebar-nav .nav-item.active svg {
    color: var(--brand-primary);
  }

  .nav {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .nav a {
    display: flex;
    align-items: center;
    gap: 10px;
    height: 34px;
    padding: 0 10px;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    font-size: 12.5px;
    transition: background .15s, color .15s;
  }
  .nav a:hover {
    background: var(--bg-subtle);
    color: var(--text-primary);
  }
  .nav a.active {
    background: var(--brand-primary-tint);
    color: var(--brand-primary);
    font-weight: 600;
  }
  .nav a.active svg { color: var(--brand-primary); }

  .divider {
    border: 0;
    border-top: 1px solid var(--border);
    margin: 12px 0;
  }

  /* User Card */
  .user-card-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: #fff;
    margin-top: auto;
    text-decoration: none;
    color: inherit;
    transition: background .15s;
  }
  .user-card-link:hover {
    background: var(--bg-subtle);
  }
  .avatar {
    width: 34px;
    height: 34px;
    border-radius: var(--radius-full);
    background: var(--brand-primary);
    color: #fff;
    font-weight: 700;
    font-size: 12px;
    display: grid;
    place-items: center;
    position: relative;
    overflow: hidden;
    flex-shrink: 0;
  }
  .avatar img { width: 100%; height: 100%; object-fit: cover; }
  .status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10B981;
    border: 1.5px solid #fff;
  }
  .logout-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 7px 10px;
    margin-top: 6px;
    border-radius: var(--radius-md);
    color: #b91c1c;
    font-size: 12px;
    font-weight: 600;
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: background .15s;
  }
  .logout-btn:hover { background: #fef2f2; border-color: #fecaca; }

  /* ===== Main Content Area ===== */
  .main {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    padding: 20px 24px;
    background: var(--bg-muted);
  }

  /* Topbar */
  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 16px;
    flex-wrap: wrap;
  }
  .crumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-tertiary);
  }
  .crumbs strong {
    color: var(--text-primary);
    font-weight: 600;
  }
  .top-actions {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  /* Doctor Filter Tabs */
  .tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    padding: 3px;
    border-radius: var(--radius-md);
    margin-bottom: 16px;
    width: fit-content;
    max-width: 100%;
  }
  .tab {
    height: 32px;
    padding: 0 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    border: 1px solid transparent;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    cursor: pointer;
  }
  .tab .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--text-tertiary);
  }
  .tab:hover { color: var(--text-primary); background: rgba(255,255,255,0.7); }
  .tab.active {
    background: #ffffff;
    color: var(--brand-primary);
    border-color: var(--border);
    box-shadow: var(--shadow-xs);
  }
  .tab.active .dot { background: var(--brand-primary); }

  /* ===== Calendar Card Container ===== */
  .card {
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    background: #fff;
    overflow: hidden;
    box-shadow: var(--shadow-xs);
  }
  
  /* Toolbar */
  .toolbar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    background: #ffffff;
  }
  .date-badge {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-md);
    background: var(--brand-primary-tint);
    border: 1px solid #BFDBFE;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--brand-primary);
    flex-shrink: 0;
  }
  .date-badge small { font-size: 9px; font-weight: 700; text-transform: uppercase; line-height: 10px; }
  .date-badge b { font-size: 15px; font-weight: 800; line-height: 16px; }
  .date-title h2 { font-size: 15px; font-weight: 700; color: var(--text-primary); }
  .date-title span { font-size: 12px; color: var(--text-tertiary); }

  .seg {
    display: flex;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-xs);
    background: #fff;
  }
  .seg button { height: 34px; min-width: 34px; display: grid; place-items: center; color: var(--icon-default); transition: background .15s; }
  .seg button:hover { background: var(--bg-subtle); color: var(--text-primary); }
  .seg button.today { padding: 0 16px; border-inline: 1px solid var(--border); font-weight: 600; font-size: 12px; color: var(--text-primary); }

  /* View Mode Switcher (Harian, Mingguan, Bulanan) */
  .view-switcher {
    display: inline-flex;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    padding: 2px;
    border-radius: var(--radius-md);
    margin-left: 8px;
  }
  .view-btn {
    height: 30px;
    padding: 0 12px;
    border-radius: 6px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text-secondary);
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .view-btn.active {
    background: #ffffff;
    color: var(--brand-primary);
    box-shadow: var(--shadow-xs);
  }

  .btn {
    height: 34px;
    padding: 0 14px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: #fff;
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 12.5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: var(--shadow-xs);
    cursor: pointer;
    transition: all 0.15s;
  }
  .btn.primary {
    background: var(--brand-primary);
    color: #fff;
    border-color: var(--brand-primary);
  }
  .btn.primary:hover { background: var(--brand-primary-hover); }

  /* ===== View Body: Daily Time Grid & Right Detail Panel ===== */
  .daily-body {
    display: grid;
    grid-template-columns: 1fr var(--panel-w);
    height: 600px;
  }

  /* Time Grid */
  .grid {
    overflow-y: auto;
    position: relative;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
  }
  .grid-inner {
    position: relative;
    height: calc(var(--hour-h) * 10 + 20px);
    padding-top: 12px;
  }
  .hour { display: flex; height: var(--hour-h); }
  .hour .lbl {
    width: 68px;
    flex-shrink: 0;
    text-align: right;
    padding-right: 12px;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-tertiary);
    transform: translateY(-9px);
  }
  .hour .line {
    flex: 1;
    border-top: 1px solid var(--border-light);
    position: relative;
    cursor: pointer;
    transition: background .15s;
  }
  .hour .line:hover {
    background: rgba(16, 185, 129, 0.04);
  }
  .hour .line::after { content: ""; position: absolute; left: 0; right: 0; top: 50%; border-top: 1px dashed #f1f5f9; }

  /* Events Container */
  .events { position: absolute; left: 68px; right: 0; top: 12px; bottom: 0; pointer-events: none; }
  
  /* Event Card Style */
  .event {
    position: absolute;
    left: 8px;
    right: 12px;
    border-radius: var(--radius-md);
    padding: 8px 12px;
    border: 1px solid;
    font-size: 11.5px;
    line-height: 16px;
    overflow: hidden;
    cursor: pointer;
    box-shadow: var(--shadow-xs);
    pointer-events: auto;
    transition: transform 0.15s, box-shadow 0.15s;
  }
  .event:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }
  .event.selected {
    border-width: 2px !important;
    box-shadow: 0 0 0 3px rgba(1, 45, 104, 0.2) !important;
  }

  /* Consistent Status Colors (Green, Red, Yellow, Gray) */
  .event.status-available {
    background: var(--status-available-bg) !important;
    border-color: var(--status-available-border) !important;
    color: var(--status-available-text) !important;
  }
  .event.status-pending {
    background: var(--status-pending-bg) !important;
    border-color: var(--status-pending-border) !important;
    color: var(--status-pending-text) !important;
  }
  .event.status-booked,
  .event.status-confirmed {
    background: var(--status-full-bg) !important;
    border-color: var(--status-full-border) !important;
    color: var(--status-full-text) !important;
  }
  .event.status-cancelled {
    background: var(--status-cancelled-bg) !important;
    border-color: var(--status-cancelled-border) !important;
    color: var(--status-cancelled-text) !important;
    opacity: 0.85;
  }
  .event.status-completed {
    background: #F8FAFC !important;
    border-color: #E2E8F0 !important;
    color: #334155 !important;
  }
  
  .event-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 2px;
  }
  .event-header b {
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  /* Status Badges */
  .badge-status {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 9.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    flex-shrink: 0;
  }
  .badge-status-available { background: var(--status-available-badge); color: var(--status-available-text); border: 1px solid var(--status-available-border); }
  .badge-status-pending { background: var(--status-pending-badge); color: var(--status-pending-text); border: 1px solid var(--status-pending-border); }
  .badge-status-full { background: var(--status-full-badge); color: var(--status-full-text); border: 1px solid var(--status-full-border); }
  .badge-status-cancelled { background: var(--status-cancelled-badge); color: var(--status-cancelled-text); border: 1px solid var(--status-cancelled-border); }
  .badge-status-completed { background: #DBEAFE; color: #1E40AF; border: 1px solid #BFDBFE; }

  .badge-status::before { content: ""; width: 6px; height: 6px; border-radius: 50%; }
  .badge-status-available::before { background: var(--status-available-dot); }
  .badge-status-pending::before { background: var(--status-pending-dot); }
  .badge-status-full::before { background: var(--status-full-dot); }
  .badge-status-cancelled::before { background: var(--status-cancelled-dot); }
  .badge-status-completed::before { background: #3B82F6; }

  .event-meta {
    font-size: 11px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    overflow: hidden;
    opacity: 0.9;
  }

  /* Current Time Red/Navy Indicator */
  .now {
    position: absolute;
    left: 56px;
    right: 0;
    top: 220px;
    border-top: 2px solid var(--now-indicator);
    display: flex;
    align-items: center;
    pointer-events: none;
    z-index: 5;
  }
  .now::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--now-indicator);
    margin-left: -5px;
  }
  .now em {
    font-style: normal;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    background: var(--now-indicator);
    padding: 0 4px;
    border-radius: 3px;
    margin-left: 6px;
  }

  /* Right Side Detail Panel */
  .panel {
    border-left: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    background: #fff;
    overflow-y: auto;
  }
  
  /* Mini Month Grid */
  .mini {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
  }
  .mini-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
  }
  .mini-head strong { font-size: 12.5px; font-weight: 700; color: var(--text-primary); }
  .mini-head button { width: 22px; height: 22px; display: grid; place-items: center; border-radius: var(--radius-sm); color: var(--icon-default); }
  .mini-head button:hover { background: var(--bg-subtle); color: var(--text-primary); }
  .mini-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    text-align: center;
    font-size: 11px;
  }
  .mini-grid .dow {
    font-size: 9.5px;
    font-weight: 700;
    color: var(--text-tertiary);
    padding: 3px 0;
  }
  .mini-grid .d {
    height: 24px;
    display: grid;
    place-items: center;
    border-radius: 4px;
    color: var(--text-secondary);
    position: relative;
    cursor: pointer;
  }
  .mini-grid .d:hover { background: var(--bg-subtle); color: var(--text-primary); }
  .mini-grid .d.out { color: #cbd5e1; }
  .mini-grid .d.sel { background: var(--brand-primary); color: #fff; font-weight: 700; }
  .mini-grid .d.has::after {
    content: "";
    position: absolute;
    bottom: 2px;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--brand-primary);
  }
  .mini-grid .d.sel.has::after { background: #fff; }

  /* Selected Slot Detail Section */
  .detail {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .detail-head h3 {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
  }
  .detail-meta-list {
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 10px;
    font-size: 11.5px;
  }
  .meta-row { display: flex; align-items: center; gap: 8px; color: var(--text-secondary); }
  .meta-row svg { width: 14px; height: 14px; color: var(--icon-default); }

  .medical-info-card {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 10px;
    font-size: 11.5px;
  }
  .medical-info-card h4 {
    font-size: 11px;
    font-weight: 700;
    color: var(--brand-primary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .medical-info-card p {
    color: var(--text-secondary);
    line-height: 16px;
  }

  .patient-privacy-notice {
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: var(--radius-md);
    padding: 12px;
    font-size: 11.5px;
    color: var(--text-secondary);
    line-height: 17px;
  }

  .detail-actions {
    margin-top: auto;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .btn-danger {
    height: 36px;
    width: 100%;
    border-radius: var(--radius-md);
    background: #fee2e2;
    border: 1px solid #fca5a5;
    color: #991b1b;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: background 0.15s;
  }
  .btn-danger:hover { background: #fecaca; }

  /* ===== Weekly View Container ===== */
  .week-view-wrapper {
    display: none;
    padding: 16px;
    background: #f8fafc;
    min-height: 580px;
    overflow-y: auto;
  }
  .week-view-wrapper.active { display: block; }
  .week-grid-cols {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 12px;
  }
  .week-col-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    box-shadow: var(--shadow-xs);
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .week-col-card:hover {
    border-color: var(--brand-primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }
  .week-col-card.selected-col {
    border-color: var(--brand-primary);
    background: #eff6ff;
  }
  .week-col-header {
    border-bottom: 1px solid var(--border-light);
    padding-bottom: 6px;
  }
  .week-col-header b { font-size: 13px; color: var(--text-primary); display: block; }
  .week-col-header small { font-size: 11px; color: var(--text-tertiary); }
  .week-col-body {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-size: 11.5px;
    flex: 1;
  }
  .week-slot-count {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-secondary);
  }

  /* ===== Monthly Full View Container ===== */
  .month-view-wrapper {
    display: none;
    padding: 16px;
    background: #ffffff;
    min-height: 580px;
    overflow-y: auto;
  }
  .month-view-wrapper.active { display: block; }
  .month-full-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
  }
  .month-full-dow {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--text-tertiary);
    padding: 6px 0;
    text-transform: uppercase;
  }
  .month-full-cell {
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    min-height: 90px;
    padding: 8px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .month-full-cell:hover {
    border-color: var(--brand-primary);
    background: #f8fafc;
  }
  .month-full-cell.selected-day {
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 2px rgba(1, 45, 104, 0.2);
  }
  .month-full-cell.other-month {
    opacity: 0.45;
    background: var(--bg-muted);
  }

  /* ===== Interactive Booking Modal ===== */
  .modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 100;
  }
  .modal-backdrop.open { display: block; }

  .modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 95%;
    max-width: 520px;
    background: #ffffff;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border);
    padding: 24px;
    z-index: 101;
    max-height: 90vh;
    overflow-y: auto;
  }
  .modal.open { display: block; }

  .modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
  }
  .modal-head h3 { font-size: 16px; font-weight: 700; color: var(--brand-primary); }
  .modal-close { width: 28px; height: 28px; border-radius: 6px; display: grid; place-items: center; color: var(--icon-default); }
  .modal-close:hover { background: var(--bg-subtle); color: var(--text-primary); }

  .form-group { margin-bottom: 14px; }
  .form-group label { display: block; font-size: 12px; font-weight: 600; color: var(--text-primary); margin-bottom: 5px; }
  .form-control {
    width: 100%;
    height: 38px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 0 12px;
    font-size: 12.5px;
    color: var(--text-primary);
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
  }
  .form-control:focus {
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 3px rgba(1, 45, 104, 0.15);
  }
  textarea.form-control { height: 72px; padding: 8px 12px; resize: none; }

  .alert-box {
    display: none;
    padding: 10px 12px;
    border-radius: var(--radius-md);
    font-size: 11.5px;
    line-height: 16px;
    margin-bottom: 14px;
  }
  .alert-warning-box {
    background: #fffbeb;
    border: 1px solid #fcd34d;
    color: #92400e;
  }
  .alert-danger-box {
    background: #fef2f2;
    border: 1px solid #fca5a5;
    color: #991b1b;
  }

  /* Toast Notification */
  .toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #0f172a;
    color: #fff;
    padding: 12px 18px;
    border-radius: var(--radius-md);
    font-size: 12.5px;
    font-weight: 600;
    box-shadow: var(--shadow-lg);
    z-index: 200;
    display: flex;
    align-items: center;
    gap: 8px;
    opacity: 0;
    transform: translateY(10px);
    transition: opacity .2s, transform .2s;
    pointer-events: none;
  }
  .toast.show { opacity: 1; transform: translateY(0); pointer-events: auto; }

  @media (max-width: 900px) {
    .daily-body { grid-template-columns: 1fr; height: auto; }
    .grid { height: 480px; }
    .panel { border-left: none; border-top: 1px solid var(--border); }
    .week-grid-cols { grid-template-columns: repeat(2, 1fr); }
  }
</style>
</head>
<body>

<div class="app">
  <!-- ===== Sidebar (Klinik Maristella) ===== -->
  <aside class="sidebar">
    <div class="brand">
      <img src="{{ asset('images/maristella-icon.png') }}" alt="Logo Klinik Maristella">
      <span class="role-indicator-badge" id="sidebarRoleBadge">{{ !empty($user['is_admin']) && ($role ?? 'admin') === 'admin' ? 'ADMIN' : 'PASIEN' }}</span>
    </div>

    <!-- Patient Navigation -->
    <nav class="sidebar-nav" id="patientSidebarNav" style="{{ !empty($user['is_admin']) && ($role ?? 'admin') === 'admin' ? 'display: none;' : 'display: flex;' }}">
      <a href="{{ route('home') }}" class="nav-item">
        <svg><use href="#i-home"/></svg>
        <span>Beranda</span>
      </a>
      <a href="{{ route('calendar') }}" class="nav-item active">
        <svg><use href="#i-cal"/></svg>
        <span>Jadwal Dokter</span>
      </a>
      <a href="{{ route('rekam-medis') }}" class="nav-item">
        <svg><use href="#i-doc"/></svg>
        <span>Rekam Medis</span>
      </a>
      <a href="{{ route('profile.edit') }}" class="nav-item">
        <svg><use href="#i-set"/></svg>
        <span>Profil Saya</span>
      </a>
      @auth
        <form action="{{ route('logout') }}" method="POST" style="width: 100%; margin: 0;">
          @csrf
          <button type="submit" class="nav-item" style="color: #dc2626;">
            <svg><use href="#i-logout"/></svg>
            <span>Keluar</span>
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="nav-item" style="color: var(--brand-primary); font-weight: 700;">
          <svg><use href="#i-logout"/></svg>
          <span>Masuk / Login</span>
        </a>
      @endauth
    </nav>

    @if(!empty($user['is_admin']))
    <!-- Admin Navigation (Khusus Administrator) -->
    <div id="adminSidebarNav" style="{{ ($role ?? 'admin') === 'admin' ? 'display: block;' : 'display: none;' }}">
      <nav class="nav" aria-label="Navigasi admin">
        <a href="{{ route('admin.calendar') }}" class="active" title="Jadwal Dokter">
          <svg><use href="#i-cal"/></svg>
          <span>Jadwal Dokter</span>
        </a>
        <a href="{{ route('admin.dashboard') }}" title="Riwayat Pembayaran">
          <svg><use href="#i-bill"/></svg>
          <span>Riwayat Pembayaran</span>
        </a>
        <a href="{{ route('admin.doctors.index') }}" title="Manajemen Dokter">
          <svg><use href="#i-user"/></svg>
          <span>Manajemen Dokter</span>
        </a>
        <a href="{{ route('profile.edit') }}" title="Profil Saya">
          <svg><use href="#i-set"/></svg>
          <span>Profil Saya</span>
        </a>
      </nav>
      <hr class="divider">
    </div>
    @endif

    <!-- Active User Card Profile Link -->
    <a href="{{ route('profile.edit') }}" class="user-card-link" id="sidebarUserCard" title="Kelola Profil Saya">
      <div class="avatar" id="sidebarAvatar">
        @if(!empty($user['avatar_url']))
          <img src="{{ $user['avatar_url'] }}" alt="{{ $user['name'] }}">
        @else
          {{ $user['initials'] ?? 'US' }}
        @endif
        <i class="status"></i>
      </div>
      <div style="min-width:0; flex:1;">
        <strong id="sidebarUserName" style="display:block; font-size:12.5px; font-weight:700; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $user['name'] ?? 'Pengguna' }}</strong>
        <small id="sidebarUserRole" style="display:block; font-size:11px; color:var(--text-tertiary);">{{ $user['role'] ?? 'Pasien' }}</small>
      </div>
    </a>
  </aside>

  <!-- ===== Main Content Area ===== -->
  <main class="main">
    <!-- Topbar -->
    <div class="topbar">
      <div class="crumbs">
        <svg><use href="#i-home"/></svg>
        <svg style="width:12px"><use href="#i-cr"/></svg>
        <span>Klinik Maristella</span>
        <svg style="width:12px"><use href="#i-cr"/></svg>
        <strong id="crumbModeTitle">{{ !empty($user['is_admin']) && ($role ?? 'admin') === 'admin' ? 'Kalender Dokter (Admin)' : 'Jadwal Konsultasi Dokter' }}</strong>
      </div>

      <div class="top-actions">
        <!-- Quick search -->
        <label class="search" style="display:flex; align-items:center; gap:8px; width:210px; height:34px; border:1px solid var(--border); border-radius:var(--radius-md); padding:0 10px; background:#fff;">
          <svg><use href="#i-search"/></svg>
          <input id="q" placeholder="Cari nama pasien/dokter..." style="border:0; outline:0; width:100%; font-size:12px;">
        </label>

        @if(!empty($user['is_admin']))
        <!-- Role Toggle (Switcher hanya untuk Administrator) -->
        <div style="display:inline-flex; border:1px solid var(--border); border-radius:var(--radius-md); overflow:hidden;">
          <button type="button" class="view-btn {{ ($role ?? 'admin') === 'patient' ? 'active' : '' }}" onclick="switchRole('patient')">Pasien</button>
          <button type="button" class="view-btn {{ ($role ?? 'admin') === 'admin' ? 'active' : '' }}" onclick="switchRole('admin')">Admin</button>
        </div>
        @endif
      </div>
    </div>

    <!-- Heading -->
    <div style="margin-bottom:12px;">
      <h1 style="font-size:21px; font-weight:800; color:var(--brand-primary); letter-spacing:-0.01em;">Kalender & Reservasi Jadwal Praktik Dokter</h1>
      <p style="font-size:12px; color:var(--text-tertiary); margin-top:2px;">Sistem manajemen waktu terintegrasi database untuk mencegah jadwal bentrok dan kelebihan kuota pasien.</p>
    </div>

    <!-- Doctor Filter Tabs (Connected to database) -->
    <div class="tabs" role="tablist" aria-label="Filter Dokter">
      <button class="tab active" data-doctor="all" onclick="filterDoctor('all')">
        <span class="dot"></span>Semua Dokter
      </button>
      @foreach($doctors as $index => $doc)
        @php
          $colors = ['#E11D48', '#4F46E5', '#012D68', '#059669', '#D97706'];
          $docColor = $colors[$index % count($colors)];
        @endphp
        <button class="tab" data-doctor="{{ $doc->id }}" onclick="filterDoctor('{{ $doc->id }}')">
          <span class="dot" style="background: {{ $docColor }};"></span>{{ $doc->nama_dokter }} ({{ $doc->spesialisasi }})
        </button>
      @endforeach
    </div>

    <!-- Main Calendar Card -->
    <section class="card">
      <!-- Toolbar -->
      <div class="toolbar">
        <div class="date-badge">
          <small id="badgeMonth">NOV</small>
          <b id="badgeDay">17</b>
        </div>
        <div class="date-title">
          <h2 id="dateTitle">November 17, 2026</h2>
          <span id="dateDow">Tuesday</span>
        </div>

        <!-- Date navigation (Previous, Today, Next) -->
        <div class="seg">
          <button id="prevDay" aria-label="Hari sebelumnya" onclick="shiftDay(-1)"><svg><use href="#i-l"/></svg></button>
          <button class="today" id="todayBtn" onclick="goToToday()">Hari Ini</button>
          <button id="nextDay" aria-label="Hari berikutnya" onclick="shiftDay(1)"><svg><use href="#i-r"/></svg></button>
        </div>

        <!-- View Mode Switcher (Harian, Mingguan, Bulanan) -->
        <div class="view-switcher" role="group" aria-label="Mode Tampilan Kalender">
          <button type="button" class="view-btn active" id="btnViewDay" onclick="switchViewMode('day')">Harian</button>
          <button type="button" class="view-btn" id="btnViewWeek" onclick="switchViewMode('week')">Mingguan</button>
          <button type="button" class="view-btn" id="btnViewMonth" onclick="switchViewMode('month')">Bulanan</button>
        </div>

        <!-- Action Button -->
        <div style="margin-left:auto; display:flex; gap:8px; align-items:center;">
          <button id="btnOpenBookingModal" class="btn primary" onclick="openBookingModal()">
            <svg><use href="#i-plus"/></svg>
            <span id="btnActionText">{{ !empty($user['is_admin']) && ($role ?? 'admin') === 'admin' ? 'Tambah Jadwal Pasien' : 'Booking Jadwal Dokter' }}</span>
          </button>
        </div>
      </div>

      <!-- VIEW 1: Daily Time Grid & Detail Panel -->
      <div id="dayViewContainer" class="daily-body">
        <!-- Time Grid (8 AM - 5 PM) -->
        <div class="grid" id="grid">
          <div class="grid-inner" id="gridInner">
            <div id="hoursContainer"></div>
            <div class="events" id="eventsContainer"></div>
            <div class="now" id="nowIndicator">
              <em id="nowTimeLbl">Live</em>
            </div>
          </div>
        </div>

        <!-- Right Side Panel: Mini Calendar & Details -->
        <aside class="panel">
          <!-- Mini Month Picker -->
          <div class="mini">
            <div class="mini-head">
              <button aria-label="Bulan sebelumnya" onclick="shiftMonth(-1)"><svg><use href="#i-cl"/></svg></button>
              <strong id="miniMonthTitle">November 2026</strong>
              <button aria-label="Bulan berikutnya" onclick="shiftMonth(1)"><svg><use href="#i-cr"/></svg></button>
            </div>
            <div class="mini-grid" id="miniGrid"></div>
          </div>

          <!-- Selected Slot / Booking Details -->
          <div class="detail" id="detailPanel">
            <div class="detail-head">
              <h3 id="dTitle">Detail Konsultasi</h3>
            </div>
            
            <div class="detail-meta-list">
              <div class="meta-row">
                <svg><use href="#i-cal"/></svg>
                <span id="dDate">Tuesday, Nov 17, 2026</span>
              </div>
              <div class="meta-row">
                <svg><use href="#i-clock"/></svg>
                <span id="dTime">09:00 - 10:00</span>
              </div>
              <div class="meta-row">
                <svg><use href="#i-user"/></svg>
                <span id="dDoctor">Semua Dokter Praktik</span>
              </div>
            </div>

            <!-- Dynamic Medical Information (Admin View) or Privacy Notice (Patient View) -->
            <div id="dMedicalInfoSection"></div>

            <!-- Actions in Detail Panel -->
            <div class="detail-actions" id="dActionsSection"></div>
          </div>
        </aside>
      </div>

      <!-- VIEW 2: Weekly View Container (7 columns) -->
      <div id="weekViewContainer" class="week-view-wrapper">
        <div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
          <h3 style="font-size:14px; font-weight:700; color:var(--brand-primary);" id="weekRangeTitle">Ringkasan Jadwal Mingguan</h3>
          <span style="font-size:11.5px; color:var(--text-tertiary);">Klik salah satu hari untuk beralih ke jadwal harian terperinci</span>
        </div>
        <div class="week-grid-cols" id="weekGridCols"></div>
      </div>

      <!-- VIEW 3: Monthly Full View Container -->
      <div id="monthViewContainer" class="month-view-wrapper">
        <div style="margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
          <h3 style="font-size:14px; font-weight:700; color:var(--brand-primary);" id="monthRangeTitle">Ringkasan Kalender Bulanan</h3>
          <span style="font-size:11.5px; color:var(--text-tertiary);">Pilih tanggal untuk melihat ketersediaan slot jam dokter</span>
        </div>
        <div class="month-full-grid" id="monthFullGrid"></div>
      </div>
    </section>
  </main>
</div>

<!-- ===== Interactive Booking Modal ===== -->
<div id="bookingModalBackdrop" class="modal-backdrop" onclick="closeBookingModal()"></div>
<div id="bookingModal" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-head">
    <h3 id="modalTitle">Booking Jadwal Konsultasi Dokter</h3>
    <button class="modal-close" onclick="closeBookingModal()" aria-label="Tutup Modal">
      <svg><use href="#i-close"/></svg>
    </button>
  </div>

  <div id="modalAlertBox" class="alert-box alert-warning-box"></div>

  <form id="bookingForm" onsubmit="handleBookingSubmit(event)">
    <div class="form-group">
      <label for="bmDoctor">Pilih Dokter Spesialis:</label>
      <select id="bmDoctor" class="form-control" required onchange="checkSlotAvailability()">
        @foreach($doctors as $doc)
          <option value="{{ $doc->id }}" data-price="{{ $doc->price_num }}" data-capacity="{{ $doc->schedules->first()?->kuota ?? 20 }}">
            {{ $doc->nama_dokter }} — {{ $doc->spesialisasi }} ({{ $doc->availability }})
          </option>
        @endforeach
      </select>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
      <div class="form-group">
        <label for="bmDate">Tanggal Konsultasi:</label>
        <input type="date" id="bmDate" class="form-control" value="{{ $initialDate ?? now()->toDateString() }}" required onchange="checkSlotAvailability()">
      </div>

      <div class="form-group">
        <label for="bmHour">Jam Praktik (1 Jam Sesi):</label>
        <select id="bmHour" class="form-control" required onchange="checkSlotAvailability()">
          <option value="8">08:00 - 09:00</option>
          <option value="9" selected>09:00 - 10:00</option>
          <option value="10">10:00 - 11:00</option>
          <option value="11">11:00 - 12:00</option>
          <option value="12">12:00 - 13:00</option>
          <option value="13">13:00 - 14:00</option>
          <option value="14">14:00 - 15:00</option>
          <option value="15">15:00 - 16:00</option>
          <option value="16">16:00 - 17:00</option>
        </select>
      </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
      <div class="form-group">
        <label for="bmPatientName">Nama Lengkap Pasien:</label>
        <input type="text" id="bmPatientName" class="form-control" placeholder="Nama pasien..." required value="{{ $user['name'] ?? 'Rina Anggraini' }}">
      </div>

      <div class="form-group">
        <label for="bmPhone">No. WhatsApp / HP:</label>
        <input type="tel" id="bmPhone" class="form-control" placeholder="08123456789" required value="{{ $user['phone'] ?? '0812-5550-1920' }}">
      </div>
    </div>

    <div class="form-group">
      <label for="bmKeluhan">Keluhan Utama (Gejala yang Dirasakan):</label>
      <textarea id="bmKeluhan" class="form-control" placeholder="Contoh: Nyeri dada kiri, migrain berdenyut..." required></textarea>
    </div>

    <div class="form-group">
      <label for="bmRiwayat">Riwayat Penyakit Sebelumnya / Alergi Obat (Opsional):</label>
      <textarea id="bmRiwayat" class="form-control" placeholder="Contoh: Riwayat hipertensi, alergi antibiotik penisilin..."></textarea>
    </div>

    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
      <button type="button" class="btn" onclick="closeBookingModal()">Batal</button>
      <button type="submit" id="bmSubmitBtn" class="btn primary">
        <span>Konfirmasi Booking</span>
      </button>
    </div>
  </form>
</div>

<!-- Global Toast Notification -->
<div id="appToast" class="toast"></div>

<!-- SVG Sprite Icons -->
<svg style="display:none;">
  <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></symbol>
  <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></symbol>
  <symbol id="i-doc" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></symbol>
  <symbol id="i-bill" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></symbol>
  <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
  <symbol id="i-set" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></symbol>
  <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
  <symbol id="i-plus" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></symbol>
  <symbol id="i-l" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></symbol>
  <symbol id="i-r" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></symbol>
  <symbol id="i-cl" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></symbol>
  <symbol id="i-cr" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></symbol>
  <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
  <symbol id="i-trash" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></symbol>
  <symbol id="i-close" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
  <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
</svg>

<!-- ========================================== -->
<!-- Live Database Calendar Logic Script        -->
<!-- ========================================== -->
<script>
/* Master Doctors from Database */
const RAW_DOCTORS = @json($doctors);
const DOCTORS = {};
RAW_DOCTORS.forEach(d => {
  DOCTORS[d.id] = d;
});

/* Live Database Bookings Data */
let BOOKINGS = @json($initialBookings);

/* State Management */
const IS_ADMIN = {{ !empty($user['is_admin']) ? 'true' : 'false' }};
let currentRole = IS_ADMIN ? "{{ $role ?? 'admin' }}" : "patient";
let activeDoctorFilter = 'all';
let selectedDateStr = "{{ $initialDate ?? now()->toDateString() }}";
let activeBookingId = null;
let currentViewMode = 'day'; // 'day', 'week', 'month'

let miniCalYear = parseInt(selectedDateStr.split('-')[0]);
let miniCalMonth = parseInt(selectedDateStr.split('-')[1]) - 1;

const DOW_NAMES = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
const MONTH_NAMES = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

/* CSRF Header Helper */
function getCsrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";
}

/* Fetch dynamic data from database */
async function fetchCalendarData(date = selectedDateStr, doctorId = activeDoctorFilter) {
  try {
    const res = await fetch(`{{ route('calendar.data') }}?date=${date}&doctor_id=${doctorId}`);
    if (res.ok) {
      const data = await res.json();
      BOOKINGS = data.bookings || [];
      renderEvents();
      renderMiniCalendar();
      if (currentViewMode === 'week') renderWeeklyGrid();
      if (currentViewMode === 'month') renderMonthlyGrid();
    }
  } catch (err) {
    console.error("Error refreshing calendar data:", err);
  }
}

/* Render Hour Grid (8 AM to 5 PM) */
function initHourGrid() {
  const container = document.getElementById("hoursContainer");
  container.innerHTML = "";
  for (let h = 8; h <= 17; h++) {
    const lbl = (h % 12 || 12) + " " + (h < 12 ? "AM" : "PM");
    container.insertAdjacentHTML("beforeend", `
      <div class="hour" data-hour="${h}">
        <span class="lbl">${lbl}</span>
        <div class="line" onclick="handleEmptySlotClick(${h})" title="Klik untuk reservasi di jam ${lbl}"></div>
      </div>
    `);
  }
}

/* Format Hour Decimal to Text */
function formatHourDecimal(dec) {
  const h = Math.floor(dec);
  const m = Math.round((dec - h) * 60);
  const period = h < 12 ? "AM" : "PM";
  const displayH = (h % 12 || 12);
  const displayM = m < 10 ? "0" + m : m;
  return `${displayH}:${displayM} ${period}`;
}

/* Format date to display */
function formatDateDisplay(dateStr) {
  const [y, m, d] = dateStr.split('-').map(Number);
  const dt = new Date(y, m - 1, d);
  return `${DOW_NAMES[dt.getDay()]}, ${d} ${MONTH_NAMES[dt.getMonth()]} ${y}`;
}

/* Render Events to Daily Grid with Consistent Colors */
function renderEvents() {
  const container = document.getElementById("eventsContainer");
  container.innerHTML = "";

  const filterDocId = activeDoctorFilter !== 'all' ? parseInt(activeDoctorFilter) : null;
  const filtered = BOOKINGS.filter(b => {
    const matchDoc = !filterDocId || parseInt(b.doctor_id) === filterDocId;
    const matchDate = (b.booking_date === selectedDateStr || b.date === selectedDateStr);
    return matchDoc && matchDate;
  });

  const HOUR_HEIGHT = 68; // var(--hour-h)
  const START_HOUR = 8; // 8:00 AM

  filtered.forEach(b => {
    let startH = 9;
    let durH = 1;

    if (b.jam_mulai && b.jam_selesai) {
      const p1 = b.jam_mulai.split(':');
      const p2 = b.jam_selesai.split(':');
      startH = parseInt(p1[0]) + (parseInt(p1[1]) / 60);
      const endH = parseInt(p2[0]) + (parseInt(p2[1]) / 60);
      durH = Math.max(0.5, endH - startH);
    } else if (b.startHour !== undefined) {
      startH = b.startHour;
      durH = b.durationHours || 1;
    }

    const topPx = (startH - START_HOUR) * HOUR_HEIGHT;
    const heightPx = Math.max(34, durH * HOUR_HEIGHT - 4);
    const isSelected = (b.id === activeBookingId);

    const docName = b.doctor?.nama_dokter || b.doctorName || 'Dokter';
    const patName = b.patient?.full_name || b.patientName || 'Pasien';

    // Status mapping & Consistent Colors
    const status = (b.status || 'confirmed').toLowerCase();
    let statusClass = 'status-booked';
    let badgeClass = 'badge-status-full';
    let badgeText = 'Terisi / Penuh';

    if (status === 'cancelled' || status === 'batal') {
      statusClass = 'status-cancelled';
      badgeClass = 'badge-status-cancelled';
      badgeText = 'Dibatalkan';
    } else if (status === 'pending') {
      statusClass = 'status-pending';
      badgeClass = 'badge-status-pending';
      badgeText = 'Menunggu';
    } else if (status === 'completed') {
      statusClass = 'status-completed';
      badgeClass = 'badge-status-completed';
      badgeText = 'Selesai';
    }

    const titleText = currentRole === 'admin' ? patName : (status === 'cancelled' ? 'Slot Dibatalkan' : 'Sudah Terbook');
    const timeText = `${formatHourDecimal(startH)} - ${formatHourDecimal(startH + durH)}`;

    const btn = document.createElement("button");
    btn.className = `event ${statusClass} ${isSelected ? 'selected' : ''}`;
    btn.dataset.id = b.id;
    btn.style.top = topPx + "px";
    btn.style.height = heightPx + "px";

    btn.innerHTML = `
      <div class="event-header">
        <b>${escapeHtml(titleText)}</b>
        <span class="badge-status ${badgeClass}">${badgeText}</span>
      </div>
      <div class="event-meta">
        <svg style="width:12px;height:12px;"><use href="#i-clock"/></svg>
        <span>${timeText} · ${escapeHtml(docName)}</span>
      </div>
    `;

    btn.addEventListener("click", () => selectBooking(b.id));
    container.appendChild(btn);
  });

  renderDetailPanel();
}

/* Detail Panel Rendering */
function renderDetailPanel() {
  const booking = BOOKINGS.find(b => b.id == activeBookingId);
  const dTitle = document.getElementById("dTitle");
  const dDate = document.getElementById("dDate");
  const dTime = document.getElementById("dTime");
  const dDoctor = document.getElementById("dDoctor");
  const infoSec = document.getElementById("dMedicalInfoSection");
  const actSec = document.getElementById("dActionsSection");

  if (!booking) {
    dTitle.innerHTML = `
      <span>Slot Jadwal Tersedia</span>
      <span class="badge-status badge-status-available" style="margin-left:6px;">Tersedia</span>
    `;
    dDate.textContent = formatDateDisplay(selectedDateStr);
    dTime.textContent = "Pilih slot untuk reservasi";
    dDoctor.textContent = activeDoctorFilter !== 'all' ? (DOCTORS[activeDoctorFilter]?.nama_dokter || "Dokter") : "Semua Dokter Praktik";
    
    infoSec.innerHTML = `
      <div class="patient-privacy-notice">
        <b style="color:#065f46;">🟢 Slot Jam Kosong:</b><br>
        Dokter siap menerima konsultasi pasien pada jam ini. Anda dapat membuat booking baru dengan menekan tombol di bawah.
      </div>
    `;
    actSec.innerHTML = `
      <button class="btn primary" style="width:100%;height:38px;justify-content:center;" onclick="openBookingModal()">
        + Booking di Slot Ini
      </button>
    `;
    return;
  }

  let startH = 9, durH = 1;
  if (booking.jam_mulai && booking.jam_selesai) {
    const p1 = booking.jam_mulai.split(':');
    const p2 = booking.jam_selesai.split(':');
    startH = parseInt(p1[0]) + (parseInt(p1[1]) / 60);
    const endH = parseInt(p2[0]) + (parseInt(p2[1]) / 60);
    durH = Math.max(0.5, endH - startH);
  }

  const startStr = formatHourDecimal(startH);
  const endStr = formatHourDecimal(startH + durH);
  const docName = booking.doctor?.nama_dokter || booking.doctorName || 'Dokter';
  const specialty = booking.doctor?.spesialisasi || booking.specialty || '';
  const patName = booking.patient?.full_name || booking.patientName || 'Pasien';
  const status = (booking.status || 'confirmed').toLowerCase();

  dDate.textContent = formatDateDisplay(booking.booking_date || selectedDateStr);
  dTime.textContent = `${startStr} - ${endStr}`;
  dDoctor.textContent = `${docName} (${specialty})`;

  let statusBadgeHtml = '<span class="badge-status badge-status-full">Terisi</span>';
  if (status === 'cancelled' || status === 'batal') {
    statusBadgeHtml = '<span class="badge-status badge-status-cancelled">Dibatalkan</span>';
  } else if (status === 'pending') {
    statusBadgeHtml = '<span class="badge-status badge-status-pending">Menunggu</span>';
  } else if (status === 'completed') {
    statusBadgeHtml = '<span class="badge-status badge-status-completed">Selesai</span>';
  }

  if (currentRole === 'admin') {
    dTitle.innerHTML = `
      <span>${escapeHtml(patName)}</span>
      <span style="display:inline-block; margin-top:4px;">${statusBadgeHtml}</span>
    `;

    infoSec.innerHTML = `
      <div class="medical-info-card">
        <h4><svg style="width:13px;height:13px;"><use href="#i-doc"/></svg> Keluhan Pasien:</h4>
        <p>${escapeHtml(booking.symptoms || booking.keluhan || 'Pemeriksaan konsultasi.')}</p>
      </div>

      <div class="medical-info-card" style="margin-top:6px;">
        <h4><svg style="width:13px;height:13px;"><use href="#i-user"/></svg> Data Pasien & Kontak:</h4>
        <p>Telepon: <b>${escapeHtml(booking.patient?.phone || booking.phone || '-')}</b><br>
           No. Ref: <b style="font-family:monospace;">${escapeHtml(booking.booking_code || '-')}</b></p>
      </div>
    `;

    if (status !== 'cancelled' && status !== 'batal') {
      actSec.innerHTML = `
        <button class="btn-danger" onclick="cancelBooking('${booking.id}')">
          <svg style="width:14px;height:14px"><use href="#i-trash"/></svg>
          Batalkan Booking Ini
        </button>
      `;
    } else {
      actSec.innerHTML = `
        <div style="font-size:11.5px;color:#64748b;text-align:center;padding:6px;background:#f1f5f9;border-radius:6px;">
          Jadwal ini telah dibatalkan (Slot terbuka untuk booking baru).
        </div>
      `;
    }
  } else {
    // Patient view: Hide confidential patient details for privacy
    if (status === 'cancelled' || status === 'batal') {
      dTitle.innerHTML = `<span>Slot Telah Dibatalkan</span> ${statusBadgeHtml}`;
      infoSec.innerHTML = `
        <div class="patient-privacy-notice">
          Jadwal konsultasi ini sebelumnya dibatalkan, sehingga slot jam kini terbuka kembali untuk direservasi oleh pasien lain.
        </div>
      `;
      actSec.innerHTML = `
        <button class="btn primary" style="width:100%;height:38px;justify-content:center;" onclick="openBookingModal()">
          Booking di Slot Ini
        </button>
      `;
    } else {
      dTitle.innerHTML = `<span>Slot Sudah Direservasi</span> ${statusBadgeHtml}`;
      infoSec.innerHTML = `
        <div class="patient-privacy-notice">
          <b>Informasi Ketersediaan:</b><br>
          Dokter ${escapeHtml(docName)} telah memiliki jadwal konsultasi dengan pasien lain pada jam ini. Demi menjaga privasi medis, data pasien dirahasiakan.<br><br>
          <span style="color:#b91c1c;font-weight:600;">Pemberitahuan:</span> Anda tidak dapat membooking dokter ini pada jam yang sama. Silakan pilih jam atau tanggal lain.
        </div>
      `;
      actSec.innerHTML = `
        <button class="btn primary" style="width:100%;height:38px;justify-content:center;" onclick="openBookingModal()">
          Cari Jam Lain yang Tersedia
        </button>
      `;
    }
  }
}

function selectBooking(id) {
  activeBookingId = id;
  document.querySelectorAll(".event").forEach(b => b.classList.toggle("selected", b.dataset.id == id));
  renderDetailPanel();
}

function handleEmptySlotClick(hour) {
  activeBookingId = null;
  document.querySelectorAll(".event").forEach(b => b.classList.remove("selected"));
  renderDetailPanel();
  openBookingModal(hour);
}

/* Cancel Booking with Live Backend Integration */
async function cancelBooking(id) {
  if (!confirm("Apakah Anda yakin ingin membatalkan jadwal konsultasi ini? Slot jam akan kembali kosong dan tersedia.")) {
    return;
  }

  try {
    const res = await fetch(`/booking/${id}/cancel`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": getCsrfToken()
      }
    });

    const data = await res.json();
    if (res.ok) {
      showToast("✅ Jadwal booking berhasil dibatalkan. Slot jam kini kosong.");
      activeBookingId = null;
      await fetchCalendarData();
    } else {
      showToast("❌ Gagal membatalkan booking: " + (data.message || "Terjadi kesalahan"));
    }
  } catch (err) {
    showToast("❌ Gagal memproses pembatalan ke server.");
  }
}

/* Switch Calendar Role */
function switchRole(role) {
  if (!IS_ADMIN && role === 'admin') {
    showToast("Akses ditolak: Hanya administrator yang dapat beralih ke mode Admin.");
    return;
  }
  currentRole = role;
  const badge = document.getElementById("sidebarRoleBadge");
  if (badge) badge.textContent = role.toUpperCase();
  const patNav = document.getElementById("patientSidebarNav");
  if (patNav) patNav.style.display = role === 'admin' ? 'none' : 'flex';
  const admNav = document.getElementById("adminSidebarNav");
  if (admNav) admNav.style.display = role === 'admin' ? 'block' : 'none';
  const crumb = document.getElementById("crumbModeTitle");
  if (crumb) crumb.textContent = role === 'admin' ? 'Kalender Dokter (Admin)' : 'Jadwal Konsultasi Dokter';
  const btnTxt = document.getElementById("btnActionText");
  if (btnTxt) btnTxt.textContent = role === 'admin' ? 'Tambah Jadwal Pasien' : 'Booking Jadwal Dokter';

  renderEvents();
  showToast(`Beralih ke mode tampilan ${role === 'admin' ? 'Administrator' : 'Pasien'}.`);
}

/* Filter Doctor */
function filterDoctor(docId) {
  activeDoctorFilter = docId;
  document.querySelectorAll(".tab").forEach(t => {
    t.classList.toggle("active", t.dataset.doctor === String(docId));
  });
  renderEvents();
  if (currentViewMode === 'week') renderWeeklyGrid();
  if (currentViewMode === 'month') renderMonthlyGrid();
}

/* Real-time Conflict & Capacity Checking */
function checkSlotAvailability() {
  const docId = parseInt(document.getElementById("bmDoctor").value);
  const dateVal = document.getElementById("bmDate").value;
  const hourVal = parseFloat(document.getElementById("bmHour").value);
  const alertBox = document.getElementById("modalAlertBox");
  const submitBtn = document.getElementById("bmSubmitBtn");

  const newStart = hourVal;
  const newEnd = hourVal + 1;
  const doc = DOCTORS[docId];

  // 1. Check time collision with active bookings
  const conflict = BOOKINGS.find(b => {
    if (parseInt(b.doctor_id) === docId && (b.booking_date === dateVal || b.date === dateVal)) {
      if (b.status === 'cancelled' || b.status === 'batal') return false;

      let bStart = 9, bEnd = 10;
      if (b.jam_mulai && b.jam_selesai) {
        const p1 = b.jam_mulai.split(':');
        const p2 = b.jam_selesai.split(':');
        bStart = parseInt(p1[0]) + (parseInt(p1[1]) / 60);
        bEnd = parseInt(p2[0]) + (parseInt(p2[1]) / 60);
      } else if (b.startHour !== undefined) {
        bStart = b.startHour;
        bEnd = b.startHour + (b.durationHours || 1);
      }

      // Overlap condition: start < existing.end AND end > existing.start
      return (newStart < bEnd && newEnd > bStart);
    }
    return false;
  });

  if (conflict) {
    const docName = doc?.nama_dokter || 'Dokter';
    alertBox.innerHTML = `⚠️ <b>Jadwal Bentrok:</b> Dokter ${escapeHtml(docName)} sudah memiliki janji pada jam ${formatHourDecimal(newStart)} - ${formatHourDecimal(newEnd)}. Silakan pilih jam atau dokter lain.`;
    alertBox.className = "alert-box alert-danger-box";
    alertBox.style.display = "block";
    submitBtn.disabled = true;
    submitBtn.style.opacity = "0.5";
    return false;
  }

  // 2. Check doctor's capacity for that day
  const activeBookingsCount = BOOKINGS.filter(b => {
    return parseInt(b.doctor_id) === docId && (b.booking_date === dateVal || b.date === dateVal) && b.status !== 'cancelled' && b.status !== 'batal';
  }).length;

  const capacity = (doc?.schedules && doc.schedules[0] && doc.schedules[0].kuota) ? doc.schedules[0].kuota : 20;

  if (activeBookingsCount >= capacity) {
    const docName = doc?.nama_dokter || 'Dokter';
    alertBox.innerHTML = `⚠️ <b>Kapasitas Penuh:</b> Dokter ${escapeHtml(docName)} telah mencapai kuota maksimal (${capacity} pasien) pada tanggal ${dateVal}.`;
    alertBox.className = "alert-box alert-danger-box";
    alertBox.style.display = "block";
    submitBtn.disabled = true;
    submitBtn.style.opacity = "0.5";
    return false;
  }

  alertBox.style.display = "none";
  submitBtn.disabled = false;
  submitBtn.style.opacity = "1";
  return true;
}

/* Submit Booking via Backend API */
async function handleBookingSubmit(e) {
  e.preventDefault();

  if (!checkSlotAvailability()) {
    return;
  }

  const docId = parseInt(document.getElementById("bmDoctor").value);
  const dateVal = document.getElementById("bmDate").value;
  const hourVal = parseInt(document.getElementById("bmHour").value);
  const patientNameVal = document.getElementById("bmPatientName").value.trim();
  const phoneVal = document.getElementById("bmPhone").value.trim();
  const keluhanVal = document.getElementById("bmKeluhan").value.trim();
  const riwayatVal = document.getElementById("bmRiwayat").value.trim();

  const startHStr = String(hourVal).padStart(2, '0') + ':00:00';
  const endHStr = String(hourVal + 1).padStart(2, '0') + ':00:00';

  const payload = {
    doctor_id: docId,
    booking_date: dateVal,
    jam_mulai: startHStr,
    jam_selesai: endHStr,
    booking_time: `${String(hourVal).padStart(2, '0')}:00 - ${String(hourVal + 1).padStart(2, '0')}:00`,
    patient_name: patientNameVal,
    phone: phoneVal,
    symptoms: keluhanVal,
    notes: riwayatVal
  };

  const alertBox = document.getElementById("modalAlertBox");
  const submitBtn = document.getElementById("bmSubmitBtn");
  submitBtn.disabled = true;
  submitBtn.textContent = "Menyimpan...";

  try {
    const res = await fetch("{{ route('booking.store') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": getCsrfToken()
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();
    if (!res.ok) {
      const errMsg = data.message || Object.values(data.errors || {})[0]?.[0] || "Gagal menyimpan booking.";
      alertBox.innerHTML = `⚠️ <b>Gagal Menyimpan:</b> ${escapeHtml(errMsg)}`;
      alertBox.className = "alert-box alert-danger-box";
      alertBox.style.display = "block";
      submitBtn.disabled = false;
      submitBtn.textContent = "Konfirmasi Booking";
      return;
    }

    closeBookingModal();
    selectedDateStr = dateVal;
    activeBookingId = data.booking?.id || null;
    updateDateHeader();
    await fetchCalendarData();
    showToast("✅ Janji konsultasi berhasil disimpan ke database!");
  } catch (err) {
    alertBox.innerHTML = `⚠️ <b>Koneksi Terganggu:</b> Gagal menghubungi server.`;
    alertBox.className = "alert-box alert-danger-box";
    alertBox.style.display = "block";
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = "Konfirmasi Booking";
  }
}

/* Modal Handling */
function openBookingModal(hourDefault = null) {
  document.getElementById("bookingModalBackdrop").classList.add("open");
  document.getElementById("bookingModal").classList.add("open");

  document.getElementById("bmDate").value = selectedDateStr;
  if (hourDefault !== null && hourDefault >= 8 && hourDefault <= 16) {
    document.getElementById("bmHour").value = hourDefault;
  }
  if (activeDoctorFilter !== 'all') {
    document.getElementById("bmDoctor").value = activeDoctorFilter;
  }

  // Pre-fill patient name & phone if logged-in as patient
  const patientInput = document.getElementById("bmPatientName");
  const phoneInput = document.getElementById("bmPhone");
  if (!IS_ADMIN && patientInput && !patientInput.value) {
    patientInput.value = "{{ $user['name'] ?? '' }}";
  }
  if (!IS_ADMIN && phoneInput && !phoneInput.value) {
    phoneInput.value = "{{ $user['phone'] ?? '' }}";
  }

  checkSlotAvailability();
}

function closeBookingModal() {
  document.getElementById("bookingModalBackdrop").classList.remove("open");
  document.getElementById("bookingModal").classList.remove("open");
  document.getElementById("modalAlertBox").style.display = "none";
}

/* Mini Calendar */
function renderMiniCalendar() {
  const container = document.getElementById("miniGrid");
  container.innerHTML = "";

  document.getElementById("miniMonthTitle").textContent = `${MONTH_NAMES[miniCalMonth]} ${miniCalYear}`;

  ["Min","Sen","Sel","Rab","Kam","Jum","Sab"].forEach(d => {
    container.insertAdjacentHTML("beforeend", `<div class="dow">${d}</div>`);
  });

  const firstDayIndex = new Date(miniCalYear, miniCalMonth, 1).getDay();
  const daysInMonth = new Date(miniCalYear, miniCalMonth + 1, 0).getDate();
  const prevMonthDays = new Date(miniCalYear, miniCalMonth, 0).getDate();

  for (let i = firstDayIndex - 1; i >= 0; i--) {
    const dNum = prevMonthDays - i;
    container.insertAdjacentHTML("beforeend", `<div class="d out">${dNum}</div>`);
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const mm = String(miniCalMonth + 1).padStart(2, '0');
    const dd = String(d).padStart(2, '0');
    const dateKey = `${miniCalYear}-${mm}-${dd}`;

    const isSel = (dateKey === selectedDateStr);
    const hasBooking = BOOKINGS.some(b => (b.booking_date === dateKey || b.date === dateKey) && b.status !== 'cancelled' && b.status !== 'batal');

    const b = document.createElement("button");
    b.className = `d ${isSel ? 'sel' : ''} ${hasBooking ? 'has' : ''}`;
    b.textContent = d;
    b.addEventListener("click", () => {
      selectedDateStr = dateKey;
      updateDateHeader();
      renderMiniCalendar();
      fetchCalendarData();
    });
    container.appendChild(b);
  }
}

function shiftMonth(delta) {
  miniCalMonth += delta;
  if (miniCalMonth < 0) {
    miniCalMonth = 11;
    miniCalYear--;
  } else if (miniCalMonth > 11) {
    miniCalMonth = 0;
    miniCalYear++;
  }
  renderMiniCalendar();
}

/* Update Header Toolbar Date */
function updateDateHeader() {
  const [y, m, d] = selectedDateStr.split('-').map(Number);
  const dt = new Date(y, m - 1, d);
  const mName = MONTH_NAMES[dt.getMonth()];
  const dowName = DOW_NAMES[dt.getDay()];

  document.getElementById("dateTitle").textContent = `${d} ${mName} ${y}`;
  document.getElementById("dateDow").textContent = dowName;
  document.getElementById("badgeDay").textContent = d;
  document.getElementById("badgeMonth").textContent = mName.substring(0, 3).toUpperCase();
}

function shiftDay(delta) {
  const [y, m, d] = selectedDateStr.split('-').map(Number);
  const dt = new Date(y, m - 1, d + delta);
  const yyyy = dt.getFullYear();
  const mm = String(dt.getMonth() + 1).padStart(2, '0');
  const dd = String(dt.getDate()).padStart(2, '0');
  selectedDateStr = `${yyyy}-${mm}-${dd}`;

  miniCalYear = yyyy;
  miniCalMonth = dt.getMonth();

  updateDateHeader();
  renderMiniCalendar();
  fetchCalendarData();
}

function goToToday() {
  const dt = new Date();
  const yyyy = dt.getFullYear();
  const mm = String(dt.getMonth() + 1).padStart(2, '0');
  const dd = String(dt.getDate()).padStart(2, '0');
  selectedDateStr = `${yyyy}-${mm}-${dd}`;
  miniCalYear = yyyy;
  miniCalMonth = dt.getMonth();

  updateDateHeader();
  renderMiniCalendar();
  fetchCalendarData();
}

/* Switch View Modes: 'day', 'week', 'month' */
function switchViewMode(mode) {
  currentViewMode = mode;
  document.getElementById("btnViewDay").classList.toggle("active", mode === 'day');
  document.getElementById("btnViewWeek").classList.toggle("active", mode === 'week');
  document.getElementById("btnViewMonth").classList.toggle("active", mode === 'month');

  document.getElementById("dayViewContainer").style.display = mode === 'day' ? 'grid' : 'none';
  document.getElementById("weekViewContainer").classList.toggle("active", mode === 'week');
  document.getElementById("monthViewContainer").classList.toggle("active", mode === 'month');

  if (mode === 'week') {
    renderWeeklyGrid();
  } else if (mode === 'month') {
    renderMonthlyGrid();
  } else {
    renderEvents();
  }
}

/* Render Weekly 7-Day Overview */
function renderWeeklyGrid() {
  const container = document.getElementById("weekGridCols");
  container.innerHTML = "";

  const [y, m, d] = selectedDateStr.split('-').map(Number);
  const curDt = new Date(y, m - 1, d);
  const curDayIndex = (curDt.getDay() + 6) % 7; // Monday = 0

  const mondayDt = new Date(curDt);
  mondayDt.setDate(curDt.getDate() - curDayIndex);

  const startStr = `${mondayDt.getDate()} ${MONTH_NAMES[mondayDt.getMonth()]}`;
  const sundayDt = new Date(mondayDt);
  sundayDt.setDate(mondayDt.getDate() + 6);
  const endStr = `${sundayDt.getDate()} ${MONTH_NAMES[sundayDt.getMonth()]} ${sundayDt.getFullYear()}`;
  document.getElementById("weekRangeTitle").textContent = `Minggu: ${startStr} - ${endStr}`;

  for (let i = 0; i < 7; i++) {
    const colDt = new Date(mondayDt);
    colDt.setDate(mondayDt.getDate() + i);
    const yyyy = colDt.getFullYear();
    const mm = String(colDt.getMonth() + 1).padStart(2, '0');
    const dd = String(colDt.getDate()).padStart(2, '0');
    const dateKey = `${yyyy}-${mm}-${dd}`;

    const isToday = (dateKey === selectedDateStr);
    const dayName = DOW_NAMES[colDt.getDay()];

    const activeBookingsOnDate = BOOKINGS.filter(b => {
      const matchDoc = activeDoctorFilter === 'all' || parseInt(b.doctor_id) === parseInt(activeDoctorFilter);
      return matchDoc && (b.booking_date === dateKey || b.date === dateKey) && b.status !== 'cancelled' && b.status !== 'batal';
    });

    const docCapacity = 20;
    const bookedCount = activeBookingsOnDate.length;
    const availCount = Math.max(0, docCapacity - bookedCount);

    let statusPill = `<span class="badge-status badge-status-available">Tersedia</span>`;
    if (bookedCount >= docCapacity) {
      statusPill = `<span class="badge-status badge-status-full">Penuh</span>`;
    } else if (bookedCount > 0) {
      statusPill = `<span class="badge-status badge-status-pending">${availCount} Sisa</span>`;
    }

    const card = document.createElement("div");
    card.className = `week-col-card ${isToday ? 'selected-col' : ''}`;
    card.innerHTML = `
      <div class="week-col-header">
        <b>${dayName}</b>
        <small>${dd} ${MONTH_NAMES[colDt.getMonth()]}</small>
      </div>
      <div class="week-col-body">
        <div style="margin-bottom:4px;">${statusPill}</div>
        <div class="week-slot-count">Terisi: <b>${bookedCount}</b> Pasien</div>
        <div class="week-slot-count" style="color:#059669;">Slot Sisa: <b>${availCount}</b></div>
        <div style="font-size:10.5px; color:#64748b; margin-top:auto;">Jam: 08:00 - 17:00</div>
      </div>
    `;

    card.addEventListener("click", () => {
      selectedDateStr = dateKey;
      updateDateHeader();
      switchViewMode('day');
    });
    container.appendChild(card);
  }
}

/* Render Full Monthly View Grid */
function renderMonthlyGrid() {
  const container = document.getElementById("monthFullGrid");
  container.innerHTML = "";

  document.getElementById("monthRangeTitle").textContent = `Bulan: ${MONTH_NAMES[miniCalMonth]} ${miniCalYear}`;

  ["Min","Sen","Sel","Rab","Kam","Jum","Sab"].forEach(d => {
    container.insertAdjacentHTML("beforeend", `<div class="month-full-dow">${d}</div>`);
  });

  const firstDay = new Date(miniCalYear, miniCalMonth, 1).getDay();
  const daysInMonth = new Date(miniCalYear, miniCalMonth + 1, 0).getDate();
  const prevDays = new Date(miniCalYear, miniCalMonth, 0).getDate();

  for (let i = firstDay - 1; i >= 0; i--) {
    const dNum = prevDays - i;
    container.insertAdjacentHTML("beforeend", `<div class="month-full-cell other-month"><span style="font-size:11px;font-weight:700;">${dNum}</span></div>`);
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const mm = String(miniCalMonth + 1).padStart(2, '0');
    const dd = String(d).padStart(2, '0');
    const dateKey = `${miniCalYear}-${mm}-${dd}`;

    const isSelected = (dateKey === selectedDateStr);
    const activeOnDate = BOOKINGS.filter(b => {
      const matchDoc = activeDoctorFilter === 'all' || parseInt(b.doctor_id) === parseInt(activeDoctorFilter);
      return matchDoc && (b.booking_date === dateKey || b.date === dateKey) && b.status !== 'cancelled' && b.status !== 'batal';
    });

    const bookedCount = activeOnDate.length;
    let badgeHtml = '';
    if (bookedCount > 0) {
      badgeHtml = `<span class="badge-status badge-status-pending" style="font-size:8.5px;padding:1px 4px;">${bookedCount} Booked</span>`;
    }

    const cell = document.createElement("div");
    cell.className = `month-full-cell ${isSelected ? 'selected-day' : ''}`;
    cell.innerHTML = `
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="font-size:12px; font-weight:700; color:var(--text-primary);">${d}</span>
        ${badgeHtml}
      </div>
      <div style="font-size:10px; color:#64748b;">${bookedCount > 0 ? (20 - bookedCount) + ' Sisa' : 'Tersedia'}</div>
    `;

    cell.addEventListener("click", () => {
      selectedDateStr = dateKey;
      updateDateHeader();
      switchViewMode('day');
    });

    container.appendChild(cell);
  }
}

/* Helper escape HTML */
function escapeHtml(str) {
  if (!str) return '';
  return String(str).replace(/[&<>"']/g, m => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  })[m]);
}

/* Toast Notification */
function showToast(msg) {
  const toast = document.getElementById("appToast");
  toast.textContent = msg;
  toast.classList.add("show");
  setTimeout(() => toast.classList.remove("show"), 3400);
}

/* Initialization on DOM Ready */
window.addEventListener("DOMContentLoaded", () => {
  initHourGrid();
  updateDateHeader();
  renderMiniCalendar();
  renderEvents();
});
</script>

</body>
</html>
