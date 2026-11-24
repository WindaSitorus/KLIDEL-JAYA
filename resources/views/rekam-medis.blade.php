<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>Rekam Medis Pasien — Klinik Maristella</title>
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
    
    --danger: #DC2626;
    --danger-soft: #FEF2F2;
    --danger-border: #FECACA;
    --danger-text: #991B1B;

    --success: #16A34A;
    --success-soft: #F0FDF4;
    --success-border: #BBF7D0;
    
    --warning: #D97706;
    --warning-soft: #FFFBEB;
    --warning-border: #FDE68A;

    --font-sans: "Plus Jakarta Sans", "Inter", system-ui, -apple-system, sans-serif;
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 14px;
    --radius-xl: 18px;
    --radius-full: 999px;
    --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.04);
    --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.06);
    --shadow-lg: 0 12px 28px rgba(15, 23, 42, 0.1);
    
    --sidebar-w: 240px;
  }
  
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: var(--font-sans);
    font-size: 13px;
    line-height: 20px;
    color: var(--text-secondary);
    background: var(--bg-muted);
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
  }
  button { font: inherit; color: inherit; background: none; border: 0; cursor: pointer; }
  input, select, textarea { font: inherit; color: inherit; }
  svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; flex-shrink: 0; }
  :focus-visible { outline: 2px solid var(--brand-primary); outline-offset: 2px; }

  html { scrollbar-gutter: stable; }

  /* Layout Container */
  .app {
    display: flex;
    min-height: 100vh;
    background: var(--bg-muted);
    max-width: 1440px;
    width: 100%;
    margin: 0 auto;
  }

  /* ===== Sidebar (Identik Gambar 1 & Kalender) ===== */
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
  .brand a {
    display: flex;
    align-items: center;
    text-decoration: none;
  }
  .brand img {
    height: 36px;
    width: auto;
    object-fit: contain;
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
    position: relative;
    box-sizing: border-box;
    transition: background-color 0.15s ease, color 0.15s ease;
    font-family: inherit;
  }
  .sidebar-nav .nav-item svg,
  .sidebar-nav button.nav-item svg {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    fill: none;
    stroke-width: 1.75;
    stroke-linecap: round;
    stroke-linejoin: round;
    flex-shrink: 0;
    color: var(--text-secondary);
    transition: color 0.15s ease;
  }
  .sidebar-nav .nav-item:hover,
  .sidebar-nav button.nav-item:hover {
    background-color: rgba(239, 246, 255, 0.6);
    color: var(--brand-primary);
  }
  .sidebar-nav .nav-item:hover svg,
  .sidebar-nav button.nav-item:hover svg {
    color: var(--brand-primary);
  }
  .sidebar-nav .nav-item.active,
  .sidebar-nav button.nav-item.active {
    background-color: var(--brand-primary-tint);
    color: var(--brand-primary);
    font-weight: 600;
  }
  .sidebar-nav .nav-item.active svg,
  .sidebar-nav button.nav-item.active svg {
    color: var(--brand-primary);
  }
  .sidebar-nav .nav-item .active-indicator {
    display: block;
    position: absolute;
    left: -20px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    border-radius: 0 4px 4px 0;
    background-color: var(--brand-primary);
  }
  .sidebar-nav .nav-item.nav-item-logout {
    color: #DC2626 !important;
    font-weight: 500;
  }
  .sidebar-nav .nav-item.nav-item-logout svg {
    color: #DC2626 !important;
  }
  .sidebar-nav .nav-item.nav-item-logout:hover {
    background-color: #FEF2F2 !important;
    color: #B91C1C !important;
  }
  .sidebar-nav .nav-item.nav-item-admin {
    color: #7E22CE !important;
    background-color: #FAF5FF;
  }
  .sidebar-nav .nav-item.nav-item-admin svg {
    color: #7E22CE !important;
  }
  .sidebar-nav .nav-item.nav-item-admin:hover {
    background-color: #F3E8FF !important;
  }
  .sidebar-nav .nav-item.nav-item-login {
    color: var(--brand-primary);
    background-color: var(--brand-primary-tint);
    font-weight: 600;
  }

  /* ===== Main Content ===== */
  .main {
    flex: 1;
    min-width: 0;
    padding: 24px 32px 48px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
  }

  /* Topbar */
  .topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 22px;
  }
  .crumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-tertiary);
    font-size: 13px;
  }
  .crumbs a {
    color: var(--text-tertiary);
    text-decoration: none;
    transition: color 0.15s;
  }
  .crumbs a:hover {
    color: var(--brand-primary);
  }
  .crumbs .cur {
    background: var(--bg-subtle);
    border-radius: var(--radius-sm);
    padding: 3px 8px;
    color: var(--text-primary);
    font-weight: 600;
    font-size: 12px;
  }

  .topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .icon-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #ffffff;
    border: 1px solid var(--border);
    display: grid;
    place-items: center;
    color: var(--text-secondary);
    transition: all 0.15s ease;
    box-shadow: var(--shadow-xs);
  }
  .icon-btn:hover {
    background: var(--bg-subtle);
    border-color: var(--brand-primary);
    color: var(--brand-primary);
  }

  .user-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-full);
    padding: 4px 14px 4px 4px;
    box-shadow: var(--shadow-xs);
  }
  .user-badge .avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--brand-primary);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 700;
    font-size: 12px;
  }
  .user-badge .u-name {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-primary);
    line-height: 1.2;
  }
  .user-badge .u-role {
    font-size: 10.5px;
    color: var(--text-tertiary);
  }

  /* ===== Patient Header Card ===== */
  .patient-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px 24px;
    box-shadow: var(--shadow-xs);
    display: flex;
    align-items: center;
    gap: 20px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }
  .patient-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #012D68 0%, #0F52C7 100%);
    color: #ffffff;
    display: grid;
    place-items: center;
    font-weight: 700;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(1, 45, 104, 0.25);
  }
  .patient-info {
    flex: 1;
    min-width: 220px;
  }
  .patient-info h1 {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.01em;
    margin-bottom: 4px;
  }
  .patient-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12.5px;
    color: var(--text-tertiary);
    flex-wrap: wrap;
  }
  .patient-meta .dot-sep {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: #cbd5e1;
  }
  .patient-meta .mrn-tag {
    font-weight: 600;
    color: var(--brand-primary);
    background: var(--brand-primary-tint);
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 11.5px;
  }
  .patient-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 600;
    padding: 9px 16px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: #ffffff;
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
  }
  .btn:hover {
    background: var(--bg-subtle);
    border-color: var(--brand-primary);
    color: var(--brand-primary);
  }
  .btn.btn-primary {
    background: var(--brand-primary);
    border-color: var(--brand-primary);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(1, 45, 104, 0.25);
  }
  .btn.btn-primary:hover {
    background: var(--brand-primary-hover);
    border-color: var(--brand-primary-hover);
  }
  .btn.btn-primary svg {
    color: #ffffff;
  }

  /* ===== Tabs Navigation ===== */
  .tabs-nav {
    display: flex;
    gap: 28px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 22px;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .tabs-nav::-webkit-scrollbar { display: none; }
  .tab-btn {
    background: none;
    border: none;
    border-bottom: 2.5px solid transparent;
    padding: 10px 4px 12px;
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-tertiary);
    cursor: pointer;
    white-space: nowrap;
    margin-bottom: -1px;
    transition: all 0.15s ease;
  }
  .tab-btn:hover {
    color: var(--brand-primary);
  }
  .tab-btn.active {
    color: var(--brand-primary);
    border-bottom-color: var(--brand-primary);
  }

  /* Tab Panes */
  .tab-pane {
    display: none;
  }
  .tab-pane.active {
    display: block;
    animation: fadeIn 0.18s ease;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ===== Grid Layout for Rekam Medis (Prototype Structure) ===== */
  .rm-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.15fr);
    gap: 20px;
    align-items: start;
  }

  .stack {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px 22px;
    box-shadow: var(--shadow-xs);
  }
  .card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }
  .card-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 9px;
  }
  .card-title svg {
    color: var(--brand-primary);
    width: 17px;
    height: 17px;
  }
  .card-title.danger {
    color: var(--danger);
  }
  .card-title.danger svg {
    color: var(--danger);
  }
  .link-btn {
    font-size: 12px;
    font-weight: 600;
    color: var(--brand-primary);
    text-decoration: none;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
  }
  .link-btn:hover {
    text-decoration: underline;
  }

  /* Fields DL / DT / DD */
  .fields-dl {
    display: grid;
    grid-template-columns: 130px 1fr;
    gap: 10px 16px;
    font-size: 13px;
  }
  .fields-dl dt {
    color: var(--text-tertiary);
    font-weight: 500;
  }
  .fields-dl dd {
    color: var(--text-primary);
    font-weight: 500;
    word-break: break-word;
  }

  /* Clinical Summary Blocks */
  .clinical-block {
    margin-bottom: 14px;
  }
  .clinical-block:last-child {
    margin-bottom: 0;
  }
  .lbl {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: var(--text-tertiary);
    margin-bottom: 4px;
  }
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 3px 10px;
    border-radius: var(--radius-full);
    font-size: 11.5px;
    font-weight: 600;
    margin-right: 6px;
    margin-top: 4px;
  }
  .badge.red {
    background: var(--danger-soft);
    color: var(--danger-text);
    border: 1px solid var(--danger-border);
  }
  .badge.blue {
    background: var(--brand-primary-tint);
    color: var(--brand-primary);
    border: 1px solid #bfdbfe;
  }
  .badge.green {
    background: var(--success-soft);
    color: var(--success);
    border: 1px solid var(--success-border);
  }

  /* Previous Encounters Rows */
  .encounter-row {
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    text-align: left;
    background: var(--bg-muted);
    border: 1px solid transparent;
    border-radius: var(--radius-md);
    padding: 12px 14px;
    cursor: pointer;
    color: var(--text-primary);
    font-family: inherit;
    transition: all 0.15s ease;
    margin-bottom: 8px;
  }
  .encounter-row:last-child { margin-bottom: 0; }
  .encounter-row:hover {
    background: #f1f5f9;
    border-color: var(--border);
    transform: translateX(2px);
  }
  .encounter-ico {
    width: 36px;
    height: 36px;
    border-radius: 9px;
    background: var(--brand-primary-tint);
    color: var(--brand-primary);
    display: grid;
    place-items: center;
    flex-shrink: 0;
  }
  .encounter-ico svg {
    width: 18px;
    height: 18px;
  }
  .encounter-t {
    flex: 1;
  }
  .encounter-t b {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
  }
  .encounter-t small {
    color: var(--text-tertiary);
    font-size: 11.5px;
  }
  .encounter-arrow {
    color: var(--text-tertiary);
    transition: transform 0.15s;
  }
  .encounter-row:hover .encounter-arrow {
    transform: translateX(3px);
    color: var(--brand-primary);
  }

  /* Active Alerts */
  .alert-box {
    display: flex;
    gap: 12px;
    background: var(--danger-soft);
    border: 1px solid var(--danger-border);
    border-radius: var(--radius-md);
    padding: 12px 14px;
    color: var(--danger-text);
    margin-bottom: 10px;
  }
  .alert-box:last-child { margin-bottom: 0; }
  .alert-box svg {
    color: var(--danger);
    margin-top: 2px;
  }
  .alert-box strong {
    display: block;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 2px;
  }
  .alert-box p {
    margin: 0;
    font-size: 11.5px;
    line-height: 1.45;
  }

  /* Next Appointment */
  .appt-box {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
  }
  .appt-box b {
    font-size: 14px;
    font-weight: 700;
    color: var(--text-primary);
  }
  .appt-box small {
    display: block;
    color: var(--text-tertiary);
    font-size: 12px;
    margin-top: 2px;
    line-height: 1.4;
  }
  .btn-sm {
    padding: 7px 12px;
    font-size: 12px;
  }

  /* Vital Signs Grid */
  .vitals-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
  }
  .vital-item {
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 10px 12px;
  }
  .vital-item .v-lbl {
    font-size: 10.5px;
    text-transform: uppercase;
    font-weight: 600;
    color: var(--text-tertiary);
    margin-bottom: 2px;
  }
  .vital-item .v-val {
    font-size: 15px;
    font-weight: 700;
    color: var(--brand-primary);
  }
  .vital-item .v-sub {
    font-size: 10.5px;
    color: var(--text-tertiary);
  }

  /* Quick Actions */
  .qa-btn {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    background: var(--bg-muted);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 11px 14px;
    color: var(--text-primary);
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    margin-bottom: 8px;
  }
  .qa-btn:last-child { margin-bottom: 0; }
  .qa-btn:hover {
    background: var(--brand-primary-tint);
    border-color: #bfdbfe;
    color: var(--brand-primary);
  }
  .qa-btn svg {
    color: var(--text-tertiary);
    transition: color 0.15s;
  }
  .qa-btn:hover svg {
    color: var(--brand-primary);
  }

  /* Table for History & Medications */
  .data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12.5px;
    text-align: left;
  }
  .data-table th {
    background: var(--bg-muted);
    color: var(--text-tertiary);
    font-weight: 600;
    padding: 10px 12px;
    border-bottom: 1px solid var(--border);
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 0.04em;
  }
  .data-table td {
    padding: 12px;
    border-bottom: 1px solid var(--border);
    color: var(--text-primary);
  }
  .data-table tr:last-child td {
    border-bottom: none;
  }
  .data-table tr:hover td {
    background: #f8fafc;
  }

  /* Modals */
  .modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    z-index: 100;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
  }
  .modal-backdrop.open {
    opacity: 1;
    pointer-events: auto;
  }
  .modal {
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -48%) scale(0.96);
    width: 90%;
    max-width: 500px;
    background: #ffffff;
    border-radius: var(--radius-xl);
    padding: 24px;
    z-index: 101;
    box-shadow: var(--shadow-lg);
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .modal.open {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
    pointer-events: auto;
  }
  .modal-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
  }
  .modal-head h3 {
    font-size: 17px;
    font-weight: 700;
    color: var(--brand-primary);
  }
  .modal-close {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    color: var(--text-tertiary);
    cursor: pointer;
  }
  .modal-close:hover {
    background: var(--bg-subtle);
    color: var(--text-primary);
  }
  .form-group {
    margin-bottom: 14px;
  }
  .form-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 6px;
  }
  .form-control {
    width: 100%;
    padding: 9px 12px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    font-family: inherit;
    font-size: 13px;
    color: var(--text-primary);
    background: #ffffff;
    box-sizing: border-box;
    transition: border-color 0.15s;
  }
  .form-control:focus {
    outline: none;
    border-color: var(--brand-primary);
    box-shadow: 0 0 0 3px rgba(1, 45, 104, 0.15);
  }

  /* Global Toast */
  .toast {
    position: fixed;
    left: 50%;
    bottom: 24px;
    transform: translate(-50%, 20px);
    background: #0f172a;
    color: #ffffff;
    padding: 10px 18px;
    border-radius: var(--radius-full);
    font-size: 12.5px;
    font-weight: 500;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s ease;
    z-index: 200;
    box-shadow: var(--shadow-lg);
  }
  .toast.show {
    opacity: 1;
    transform: translate(-50%, 0);
  }

  /* Floating Bottom Nav (Hanya muncul jika di mobile / layar sempit) */
  .floating-bottom-nav {
    display: none;
  }

  /* Responsive Breakpoints */
  @media (max-width: 992px) {
    .rm-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 860px) {
    .sidebar {
      display: none;
    }
    .main {
      padding: 16px 16px 80px;
    }
    .floating-bottom-nav {
      position: fixed;
      left: 50%;
      transform: translateX(-50%);
      bottom: 20px;
      display: flex;
      gap: 8px;
      background: #ffffff;
      padding: 6px 12px;
      border-radius: 999px;
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.14);
      border: 1px solid var(--border);
      z-index: 40;
    }
    .floating-bottom-nav a,
    .floating-bottom-nav button {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: transparent;
      color: var(--text-secondary);
      display: grid;
      place-items: center;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .floating-bottom-nav a svg,
    .floating-bottom-nav button svg {
      width: 20px;
      height: 20px;
      stroke: currentColor;
      fill: none;
      stroke-width: 1.75;
      stroke-linecap: round;
      stroke-linejoin: round;
    }
    .floating-bottom-nav a:hover,
    .floating-bottom-nav button:hover {
      color: var(--brand-primary);
      background: var(--brand-primary-tint);
    }
    .floating-bottom-nav a.active,
    .floating-bottom-nav button.active {
      background: var(--brand-primary) !important;
      color: #ffffff !important;
      box-shadow: 0 4px 10px rgba(1, 45, 104, 0.3);
    }
    .floating-bottom-nav a.active svg,
    .floating-bottom-nav button.active svg {
      color: #ffffff !important;
    }
  }

  @media (max-width: 560px) {
    .patient-card {
      flex-direction: column;
      align-items: flex-start;
      gap: 14px;
    }
    .patient-actions {
      width: 100%;
    }
    .fields-dl {
      grid-template-columns: 1fr;
      gap: 4px;
    }
    .fields-dl dd {
      margin-bottom: 8px;
    }
    .vitals-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body>

<!-- SVG Sprite Icons -->
<svg width="0" height="0" style="position: absolute; width: 0; height: 0; overflow: hidden;" aria-hidden="true">
  <defs>
    <symbol id="i-home" viewBox="0 0 24 24"><path d="M3 11 12 3l9 8v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></symbol>
    <symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="3"/><path d="M8 3v4M16 3v4M3 10h18"/></symbol>
    <symbol id="i-doc" viewBox="0 0 24 24"><path d="M6 3h8l5 5v13H6z"/><path d="M14 3v5h5M9 13h6M9 17h6"/></symbol>
    <symbol id="i-set" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1"/></symbol>
    <symbol id="i-logout" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></symbol>
    <symbol id="i-bell" viewBox="0 0 24 24"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 0 0 4 0"/></symbol>
    <symbol id="i-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></symbol>
    <symbol id="i-cr" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></symbol>
    <symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol>
    <symbol id="i-heart" viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></symbol>
    <symbol id="i-history" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.7 9.7 0 0 0-6.7 2.7L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l3 2"/></symbol>
    <symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4M12 17h.01"/></symbol>
    <symbol id="i-lightning" viewBox="0 0 24 24"><path d="M13 2 3 14h9l-1 8 10-12h-9z"/></symbol>
    <symbol id="i-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
    <symbol id="i-edit" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></symbol>
    <symbol id="i-print" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></symbol>
    <symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol>
  </defs>
</svg>

<div class="app">
  <!-- ========== SIDEBAR (Gambar 1: Persis dengan Kalender & Beranda Pasien) ========== -->
  <aside class="sidebar">
    <div class="brand">
      <a href="{{ route('home') }}" title="Klinik Maristella">
        <img src="{{ asset('images/maristella-logo-horizontal.png') }}" alt="Klinik Maristella" />
      </a>
    </div>

    <!-- Navigasi Pasien Sesuai Gambar 1 -->
    <nav class="sidebar-nav" aria-label="Navigasi Utama">
      <!-- Beranda -->
      <a href="{{ route('home') }}" class="nav-item" title="Beranda">
        <svg><use href="#i-home"/></svg>
        <span>Beranda</span>
      </a>

      <!-- Jadwal -->
      <a href="{{ route('calendar') }}" class="nav-item" title="Jadwal">
        <svg><use href="#i-cal"/></svg>
        <span>Jadwal</span>
      </a>

      <!-- Rekam Medis (Active) -->
      <a href="{{ route('rekam-medis') }}" class="nav-item active" aria-current="page" title="Rekam Medis">
        <span class="active-indicator"></span>
        <svg><use href="#i-doc"/></svg>
        <span>Rekam Medis</span>
      </a>

      <!-- Profil Saya -->
      <a href="{{ route('profile.edit') }}" class="nav-item" title="Profil Saya">
        <svg><use href="#i-set"/></svg>
        <span>Profil Saya</span>
      </a>

      @auth
        @if (auth()->user()->isAdmin())
          <a href="{{ route('admin.dashboard') }}" class="nav-item nav-item-admin" title="Portal Admin">
            <svg><use href="#i-shield"/></svg>
            <span>Portal Admin</span>
          </a>
        @endif

        <!-- Keluar -->
        <form action="{{ route('logout') }}" method="POST" style="width: 100%; margin: 0;">
          @csrf
          <button type="submit" class="nav-item nav-item-logout" title="Keluar">
            <svg><use href="#i-logout"/></svg>
            <span>Keluar</span>
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="nav-item nav-item-login" title="Masuk">
          <svg><use href="#i-logout"/></svg>
          <span>Masuk</span>
        </a>
      @endauth
    </nav>
  </aside>

  <!-- ========== MAIN CONTENT AREA ========== -->
  <main class="main">
    <!-- Topbar -->
    <div class="topbar">
      <!-- Breadcrumbs -->
      <div class="crumbs">
        <a href="{{ route('home') }}">Klinik Maristella</a>
        <svg style="width:12px;height:12px"><use href="#i-cr"/></svg>
        <span class="cur">Rekam Medis Pasien</span>
      </div>

      <!-- Topbar Actions -->
      <div class="topbar-right">
        <button class="icon-btn" onclick="showToast('Tidak ada notifikasi baru.')" title="Notifikasi" aria-label="Notifikasi">
          <svg><use href="#i-bell"/></svg>
        </button>

        @auth
          <div class="user-badge">
            <div class="avatar-sm">{{ $user['initials'] ?? 'RA' }}</div>
            <div>
              <div class="u-name">{{ $user['name'] ?? 'Rina Anggraini' }}</div>
              <div class="u-role">{{ $user['role'] ?? 'Pasien' }}</div>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk / Login</a>
        @endauth
      </div>
    </div>

    <!-- Patient Header Card (Sesuai Prototype & Klinik Maristella Navy) -->
    <header class="patient-card">
      <div class="patient-avatar" id="mainAvatar">{{ $user['initials'] ?? 'RA' }}</div>
      <div class="patient-info">
        <h1 id="patientNameHeader">{{ $user['name'] ?? 'Rina Anggraini' }}</h1>
        <div class="patient-meta">
          <span>DOB: {{ $user['dob'] ?? '12-06-1992 (34 tahun)' }}</span>
          <span class="dot-sep"></span>
          <span>{{ $user['gender'] ?? 'Perempuan' }}</span>
          <span class="dot-sep"></span>
          <span class="mrn-tag">No. RM: {{ $user['mrn'] ?? '#GM-0214' }}</span>
          <span class="dot-sep"></span>
          <span>Gol. Darah: <b>O+</b></span>
          <span class="dot-sep"></span>
          <span class="badge green" style="margin: 0; padding: 1px 8px; font-size: 10.5px;">Pasien Aktif</span>
        </div>
      </div>
      <div class="patient-actions">
        <button class="btn" onclick="openEditPatientModal()" title="Ubah data pasien">
          <svg><use href="#i-edit"/></svg>
          <span>Ubah Data Pasien</span>
        </button>
        <button class="btn" onclick="window.print()" title="Cetak ringkasan">
          <svg><use href="#i-print"/></svg>
          <span>Cetak</span>
        </button>
        <button class="btn btn-primary" onclick="openConsultationModal()">
          <svg><use href="#i-plus"/></svg>
          <span>Buat Konsultasi</span>
        </button>
      </div>
    </header>

    <!-- 6 Tab Navigasi Klinis (Overview, Medical History, Allergies, Medications, Documents, Notes) -->
    <nav class="tabs-nav" role="tablist" aria-label="Bagian rekam medis">
      <button class="tab-btn active" data-tab="overview" onclick="switchTab('overview')">Ringkasan (Overview)</button>
      <button class="tab-btn" data-tab="history" onclick="switchTab('history')">Riwayat Medis</button>
      <button class="tab-btn" data-tab="allergies" onclick="switchTab('allergies')">Alergi (Allergies)</button>
      <button class="tab-btn" data-tab="medications" onclick="switchTab('medications')">Obat-obatan</button>
      <button class="tab-btn" data-tab="documents" onclick="switchTab('documents')">Dokumen &amp; Lab</button>
      <button class="tab-btn" data-tab="notes" onclick="switchTab('notes')">Catatan Dokter</button>
    </nav>

    <!-- ============================================== -->
    <!-- TAB 1: RINGKASAN (OVERVIEW)                     -->
    <!-- ============================================== -->
    <div id="tab-overview" class="tab-pane active">
      <div class="rm-grid">
        
        <!-- Kolom Kiri: Detail Pasien, Ringkasan Klinis, Riwayat Kunjungan -->
        <div class="stack">
          <!-- Detail Pasien Card -->
          <section class="card">
            <div class="card-header">
              <h2 class="card-title">
                <svg><use href="#i-user"/></svg>
                Detail Pasien
              </h2>
              <button class="link-btn" onclick="openEditPatientModal()">Perbarui Data</button>
            </div>
            <dl class="fields-dl">
              <dt>Nama Lengkap</dt>
              <dd id="d-fullname">{{ $user['name'] ?? 'Rina Anggraini' }}</dd>
              <dt>Tanggal Lahir</dt>
              <dd>{{ $user['dob'] ?? '12-06-1992 (34 tahun)' }}</dd>
              <dt>Jenis Kelamin</dt>
              <dd>{{ $user['gender'] ?? 'Perempuan' }}</dd>
              <dt>Nomor Telepon</dt>
              <dd id="d-phone">{{ $user['phone'] ?? '+62 812 5550 1920' }}</dd>
              <dt>Email</dt>
              <dd>{{ $user['email'] ?? 'rina.anggraini@aihealth.com' }}</dd>
              <dt>Alamat Tinggal</dt>
              <dd id="d-address">{{ $user['address'] ?? 'Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan, 12190' }}</dd>
              <dt>Kontak Darurat</dt>
              <dd>Budi Santoso (Suami) · +62 811 9888 2311</dd>
              <dt>Tinggi / Berat</dt>
              <dd>165 cm / 58 kg (BMI: 21.3 - Normal)</dd>
            </dl>
          </section>

          <!-- Ringkasan Klinis Card -->
          <section class="card">
            <div class="card-header">
              <h2 class="card-title">
                <svg><use href="#i-heart"/></svg>
                Ringkasan Klinis &amp; Diagnosis Aktif
              </h2>
            </div>
            <div class="clinical-block">
              <p class="lbl">Keluhan Utama Terakhir (Chief Complaint)</p>
              <div style="font-size:13px; color:var(--text-primary); font-weight:500;">
                Batuk berulang disertai meriang ringan selama 5 hari terakhir setelah perjalanan luar kota.
              </div>
            </div>
            <div class="clinical-block">
              <p class="lbl">Diagnosis Aktif (Problem List)</p>
              <div style="display:flex; flex-wrap:wrap; gap:6px; margin-top:4px;">
                <span class="badge blue">Hipertensi Primer Tahap 1 (ICD-10 I10)</span>
                <span class="badge blue">Dislipidemia Ringan (ICD-10 E78.5)</span>
                <span class="badge blue">Migrain Sesekali (ICD-10 G43.9)</span>
              </div>
            </div>
            <div class="clinical-block">
              <p class="lbl">Alergi Terdata</p>
              <span class="badge red">Penisilin (Reaksi Sedang)</span>
              <span class="badge red">Seafood / Udang (Reaksi Ringan)</span>
            </div>
            <div class="clinical-block">
              <p class="lbl">Terapi Obat Berjalan</p>
              <span class="badge blue">Amlodipine 5mg (1x sehari pagi)</span>
              <span class="badge blue">Atorvastatin 10mg (1x sehari malam)</span>
              <span class="badge blue">Paracetamol 500mg (prn bila demam/nyeri)</span>
            </div>
          </section>

          <!-- Riwayat Kunjungan Sebelumnya (Previous Encounters) -->
          <section class="card">
            <div class="card-header">
              <h2 class="card-title">
                <svg><use href="#i-history"/></svg>
                Kunjungan Sebelumnya (Previous Encounters)
              </h2>
              <button class="link-btn" onclick="switchTab('history')">Lihat Semua</button>
            </div>
            
            <button class="encounter-row" onclick="viewEncounterDetail('15 Agu 2026', 'Dr. Lina Wijaya', 'Spesialis Jantung', 'Kontrol rutin tekanan darah dan evaluasi irama jantung. Kondisi terkontrol stabil.')">
              <span class="encounter-ico"><svg><use href="#i-doc"/></svg></span>
              <span class="encounter-t">
                <b>15 Agu 2026 — Konsultasi Spesialis Jantung</b>
                <small>Dr. Lina Wijaya · Poliklinik Kardiologi</small>
              </span>
              <svg class="encounter-arrow"><use href="#i-cr"/></svg>
            </button>

            <button class="encounter-row" onclick="viewEncounterDetail('10 Jul 2026', 'Dr. Anaya Kapoor', 'Spesialis Saraf', 'Konsultasi sakit kepala sebelah/migrain. Terapi profilaksis efektif menurunkan frekuensi serangan.')">
              <span class="encounter-ico"><svg><use href="#i-doc"/></svg></span>
              <span class="encounter-t">
                <b>10 Jul 2026 — Evaluasi Neurologi &amp; Sakit Kepala</b>
                <small>Dr. Anaya Kapoor · Poliklinik Saraf</small>
              </span>
              <svg class="encounter-arrow"><use href="#i-cr"/></svg>
            </button>

            <button class="encounter-row" onclick="viewEncounterDetail('10 Jan 2026', 'Dr. Saran Mitchell', 'Spesialis Ortopedi', 'Fisioterapi pemulihan sendi lutut kanan pasca cedera ringan olahraga. Range of Motion kembali optimal.')">
              <span class="encounter-ico"><svg><use href="#i-doc"/></svg></span>
              <span class="encounter-t">
                <b>10 Jan 2026 — Fisioterapi Sendi Lutut</b>
                <small>Dr. Saran Mitchell · Poliklinik Ortopedi</small>
              </span>
              <svg class="encounter-arrow"><use href="#i-cr"/></svg>
            </button>
          </section>
        </div>

        <!-- Kolom Kanan: Peringatan Aktif, Janji Temu, Tanda Vital, Aksi Cepat -->
        <div class="stack">
          <!-- Active Alerts Card -->
          <section class="card" role="alert">
            <div class="card-header">
              <h2 class="card-title danger">
                <svg><use href="#i-alert"/></svg>
                Peringatan Klinis Aktif (Active Alerts)
              </h2>
            </div>
            
            <div class="alert-box">
              <svg><use href="#i-alert"/></svg>
              <div>
                <strong>Alergi Berat: Penisilin</strong>
                <p>Pasien memiliki riwayat reaksi hipersensitivitas terdokumentasi terhadap antibiotik golongan Beta-laktam. Hindari pemberian amoksisilin dan turunannya.</p>
              </div>
            </div>

            <div class="alert-box" style="background:#FFFBEB; border-color:#FDE68A; color:#92400E;">
              <svg style="color:#D97706;"><use href="#i-alert"/></svg>
              <div>
                <strong style="color:#B45309;">Alergi Makanan: Udang / Seafood</strong>
                <p>Reaksi urtikaria ringan dan rasa gatal di bibir. Dianjurkan sedia antihistamin oral.</p>
              </div>
            </div>
          </section>

          <!-- Next Appointment Card -->
          <section class="card">
            <div class="card-header">
              <h2 class="card-title">
                <svg><use href="#i-cal"/></svg>
                Janji Temu Berikutnya
              </h2>
            </div>
            <div class="appt-box">
              <div>
                <b>14 Agu 2026 — 14:30 WIB</b>
                <small>Dr. Saran Mitchell · Spesialis Ortopedi<br>Ruang Praktik 2B · Poliklinik Klinik Maristella</small>
              </div>
              <a href="{{ route('calendar') }}" class="btn btn-primary btn-sm">Buka Kalender</a>
            </div>
          </section>

          <!-- Tanda Vital Terakhir Card -->
          <section class="card">
            <div class="card-header">
              <h2 class="card-title">
                <svg><use href="#i-heart"/></svg>
                Tanda Vital Terakhir (15 Agu 2026)
              </h2>
              <span style="font-size:11px; color:var(--text-tertiary);">Pukul 14:10 WIB</span>
            </div>
            <div class="vitals-grid">
              <div class="vital-item">
                <div class="v-lbl">Tekanan Darah</div>
                <div class="v-val">125/82 <span style="font-size:11px; font-weight:500;">mmHg</span></div>
                <div class="v-sub">Status: Terkontrol</div>
              </div>
              <div class="vital-item">
                <div class="v-lbl">Detak Jantung</div>
                <div class="v-val">74 <span style="font-size:11px; font-weight:500;">bpm</span></div>
                <div class="v-sub">Irama: Reguler</div>
              </div>
              <div class="vital-item">
                <div class="v-lbl">Saturasi Oksigen</div>
                <div class="v-val">98% <span style="font-size:11px; font-weight:500;">SpO2</span></div>
                <div class="v-sub">Udara Ruangan</div>
              </div>
              <div class="vital-item">
                <div class="v-lbl">Suhu Tubuh</div>
                <div class="v-val">36.6 <span style="font-size:11px; font-weight:500;">°C</span></div>
                <div class="v-sub">Kondisi Afebris</div>
              </div>
            </div>
          </section>

          <!-- Quick Actions Card (Sesuai Prototype) -->
          <section class="card">
            <div class="card-header">
              <h2 class="card-title">
                <svg><use href="#i-lightning"/></svg>
                Aksi Cepat Medis (Quick Actions)
              </h2>
            </div>
            <button class="qa-btn" onclick="openQuickActionModal('Permintaan Uji Laboratorium', 'Contoh: Darah Lengkap, Profil Lipid, HbA1c...')">
              <span>Minta Permintaan Lab (Add Lab Request)</span>
              <svg><use href="#i-plus"/></svg>
            </button>
            <button class="qa-btn" onclick="openQuickActionModal('Permintaan Radiologi &amp; Imaging', 'Contoh: Rontgen Thorax, USG Abdomen, MRI...')">
              <span>Minta Pemeriksaan Radiologi (Imaging)</span>
              <svg><use href="#i-plus"/></svg>
            </button>
            <button class="qa-btn" onclick="openQuickActionModal('Surat Rujukan Dokter Spesialis', 'Contoh: Rujukan ke Konsultan Kardiologi Intervensi...')">
              <span>Minta Surat Rujukan (Add Referral)</span>
              <svg><use href="#i-plus"/></svg>
            </button>
            <button class="qa-btn" onclick="openQuickActionModal('Resep Ulang Obat Rutin', 'Contoh: Refill Amlodipine 5mg untuk 30 hari...')">
              <span>Resep Ulang Obat Rutin (Refill RX)</span>
              <svg><use href="#i-plus"/></svg>
            </button>
          </section>
        </div>

      </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 2: RIWAYAT MEDIS (MEDICAL HISTORY)         -->
    <!-- ============================================== -->
    <div id="tab-history" class="tab-pane">
      <section class="card">
        <div class="card-header">
          <h2 class="card-title">
            <svg><use href="#i-history"/></svg>
            Riwayat Medis Lengkap
          </h2>
          <button class="btn btn-primary btn-sm" onclick="openQuickActionModal('Tambah Riwayat Medis', 'Tuliskan riwayat penyakit, rawat inap atau tindakan medis...')">
            <svg><use href="#i-plus"/></svg>
            <span>Tambah Catatan Riwayat</span>
          </button>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Kategori &amp; Diagnosis</th>
                <th>Dokter Pemeriksa</th>
                <th>Fasilitas / Poli</th>
                <th>Tindakan / Terapi</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>15 Agu 2026</b></td>
                <td>Pemeriksaan Rutin Hipertensi Primer</td>
                <td>Dr. Lina Wijaya, Sp.JP</td>
                <td>Poli Jantung Klinik Maristella</td>
                <td>Lanjutan Amlodipine 5mg &amp; edukasi diet rendah garam</td>
                <td><span class="badge green">Terkontrol</span></td>
              </tr>
              <tr>
                <td><b>10 Jul 2026</b></td>
                <td>Evaluasi Migrain Episodik</td>
                <td>Dr. Anaya Kapoor, Sp.S</td>
                <td>Poli Saraf Klinik Maristella</td>
                <td>Peresepan profilaksis migrain &amp; sleep diary</td>
                <td><span class="badge green">Membaik</span></td>
              </tr>
              <tr>
                <td><b>10 Jan 2026</b></td>
                <td>Cedera Regangan Ligamen Lutut Kanan</td>
                <td>Dr. Saran Mitchell, Sp.OT</td>
                <td>Poli Ortopedi Klinik Maristella</td>
                <td>Sesi fisioterapi 4x &amp; kompres es</td>
                <td><span class="badge green">Sembuh</span></td>
              </tr>
              <tr>
                <td><b>14 Okt 2025</b></td>
                <td>Dislipidemia Ringan (Kolesterol Total 215)</td>
                <td>Dr. Lina Wijaya, Sp.JP</td>
                <td>Poli Jantung Klinik Maristella</td>
                <td>Atorvastatin 10mg malam hari</td>
                <td><span class="badge blue">Dalam Terapi</span></td>
              </tr>
              <tr>
                <td><b>22 Mar 2024</b></td>
                <td>Reaksi Alergi Penisilin (Ruam &amp; Pruritus)</td>
                <td>Dr. H. Mokena, Sp.PD</td>
                <td>Instalasi Gawat Darurat</td>
                <td>Injeksi Antihistamin &amp; pencatatan kartu alergi</td>
                <td><span class="badge red">Alergi Terverifikasi</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ============================================== -->
    <!-- TAB 3: ALERGI (ALLERGIES)                      -->
    <!-- ============================================== -->
    <div id="tab-allergies" class="tab-pane">
      <section class="card">
        <div class="card-header">
          <h2 class="card-title danger">
            <svg><use href="#i-alert"/></svg>
            Daftar Alergi &amp; Hipersensitivitas Pasien
          </h2>
          <button class="btn btn-primary btn-sm" onclick="openQuickActionModal('Catat Alergi Baru', 'Nama alergen (misal: Obat, Makanan, Debu, Lateks)...')">
            <svg><use href="#i-plus"/></svg>
            <span>Tambah Alergi Baru</span>
          </button>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Alergen</th>
                <th>Kategori</th>
                <th>Tingkat Keparahan</th>
                <th>Manifestasi Reaksi</th>
                <th>Tanggal Teridentifikasi</th>
                <th>Status Medis</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Penisilin (Penicillin G, Amoxicillin)</b></td>
                <td>Obat (Antibiotik Beta-laktam)</td>
                <td><span class="badge red">Berat / Tinggi</span></td>
                <td>Urtikaria sistemik, angioedema bibir, gatal intensif</td>
                <td>22 Mar 2024</td>
                <td><span class="badge red">KONTRAINDIKASI MUTLAK</span></td>
              </tr>
              <tr>
                <td><b>Udang &amp; Kepiting (Crustacea)</b></td>
                <td>Makanan Laut (Seafood)</td>
                <td><span class="badge red" style="background:#FFFBEB; border-color:#FDE68A; color:#B45309;">Ringan - Sedang</span></td>
                <td>Bibir terasa kebas dan kemerahan setelah konsumsi</td>
                <td>12 Sep 2018</td>
                <td><span class="badge blue">Hindari Konsumsi</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ============================================== -->
    <!-- TAB 4: OBAT-OBATAN (MEDICATIONS)               -->
    <!-- ============================================== -->
    <div id="tab-medications" class="tab-pane">
      <section class="card">
        <div class="card-header">
          <h2 class="card-title">
            <svg><use href="#i-doc"/></svg>
            Daftar Terapi Obat Aktif &amp; Riwayat Resep
          </h2>
          <button class="btn btn-primary btn-sm" onclick="openQuickActionModal('Resep Obat Baru', 'Nama obat, dosis dan aturan pakai...')">
            <svg><use href="#i-plus"/></svg>
            <span>Tambah Terapi Obat</span>
          </button>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama Obat</th>
                <th>Dosis &amp; Bentuk</th>
                <th>Aturan Pakai</th>
                <th>Indikasi Medis</th>
                <th>Dokter Penulis</th>
                <th>Periode Peresepan</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Amlodipine Besylate</b></td>
                <td>Tablet 5 mg</td>
                <td>1x sehari tiap pagi sesudah makan</td>
                <td>Hipertensi Primer</td>
                <td>Dr. Lina Wijaya</td>
                <td>15 Agu 2026 – 15 Sep 2026</td>
                <td><button class="btn btn-sm" onclick="showToast('Permintaan refill Amlodipine diajukan.')">Minta Refill</button></td>
              </tr>
              <tr>
                <td><b>Atorvastatin Calcium</b></td>
                <td>Tablet 10 mg</td>
                <td>1x sehari tiap malam hari</td>
                <td>Dislipidemia Ringan</td>
                <td>Dr. Lina Wijaya</td>
                <td>15 Agu 2026 – 15 Sep 2026</td>
                <td><button class="btn btn-sm" onclick="showToast('Permintaan refill Atorvastatin diajukan.')">Minta Refill</button></td>
              </tr>
              <tr>
                <td><b>Paracetamol</b></td>
                <td>Kaplet 500 mg</td>
                <td>1-2 tablet bila demam/nyeri (maks 3x/hari)</td>
                <td>Antipiretik / Analgetik</td>
                <td>Dr. H. Mokena</td>
                <td>Sesuai Kebutuhan (PRN)</td>
                <td><button class="btn btn-sm" onclick="showToast('Stok obat masih mencukupi.')">Cek Stok</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ============================================== -->
    <!-- TAB 5: DOKUMEN & LAB (DOCUMENTS)               -->
    <!-- ============================================== -->
    <div id="tab-documents" class="tab-pane">
      <section class="card">
        <div class="card-header">
          <h2 class="card-title">
            <svg><use href="#i-doc"/></svg>
            Berkas Rekam Medis, Hasil Lab &amp; Radiologi
          </h2>
          <button class="btn btn-primary btn-sm" onclick="showToast('Membuka dialog unggah berkas PDF/Gambar...')">
            <svg><use href="#i-plus"/></svg>
            <span>Unggah Dokumen Baru</span>
          </button>
        </div>
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nama Dokumen / Uji</th>
                <th>Kategori</th>
                <th>Tanggal Tes</th>
                <th>Penyedia / Lab</th>
                <th>Hasil / Kesimpulan</th>
                <th>Aksi Unduh</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Panel Profil Lipid Lengkap (Kolesterol, HDL, LDL, Trigliserida)</b></td>
                <td>Laboratorium Darah</td>
                <td>14 Agu 2026</td>
                <td>Lab Sentral Klinik Maristella</td>
                <td>Kolesterol 198 mg/dL (Normal Membaik)</td>
                <td><button class="btn btn-sm" onclick="showToast('Mengunduh hasil lab PDF...')">📄 Unduh PDF</button></td>
              </tr>
              <tr>
                <td><b>Hasil Rontgen Thorax PA Digital</b></td>
                <td>Radiologi</td>
                <td>10 Jan 2026</td>
                <td>Unit Radiologi Klinik Maristella</td>
                <td>Cor dan pulmo dalam batas normal, CTR < 50%</td>
                <td><button class="btn btn-sm" onclick="showToast('Mengunduh citra radiologi...')">📄 Unduh PDF</button></td>
              </tr>
              <tr>
                <td><b>Elektrokardiogram (EKG 12-Lead)</b></td>
                <td>Kardiologi Diagnostik</td>
                <td>15 Agu 2026</td>
                <td>Poli Jantung Klinik Maristella</td>
                <td>Sinus Rhythm normal, HR 74 bpm, no ST-changes</td>
                <td><button class="btn btn-sm" onclick="showToast('Mengunduh grafik EKG...')">📄 Unduh PDF</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ============================================== -->
    <!-- TAB 6: CATATAN DOKTER (NOTES)                  -->
    <!-- ============================================== -->
    <div id="tab-notes" class="tab-pane">
      <section class="card">
        <div class="card-header">
          <h2 class="card-title">
            <svg><use href="#i-doc"/></svg>
            Catatan Observasi Klinis Dokter (SOAP Notes)
          </h2>
          <button class="btn btn-primary btn-sm" onclick="openQuickActionModal('Tambah Catatan Klinis', 'Tuliskan observasi klinis perkembangan pasien...')">
            <svg><use href="#i-plus"/></svg>
            <span>Tambah Catatan Baru</span>
          </button>
        </div>
        
        <div style="display:flex; flex-direction:column; gap:14px;">
          <div style="background:var(--bg-muted); border:1px solid var(--border); border-radius:var(--radius-md); padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <b style="color:var(--text-primary); font-size:13.5px;">Dr. Lina Wijaya, Sp.JP (Spesialis Jantung)</b>
              <span style="color:var(--text-tertiary); font-size:11.5px;">15 Agu 2026 · 14:15 WIB</span>
            </div>
            <p style="color:var(--text-secondary); font-size:12.5px; line-height:1.6; margin:0;">
              "Tekanan darah pasien terkontrol sangat baik di angka 125/82 mmHg. Irama jantung sinus reguler tanpa murmur atau bising patologis. Keluhan sesak napas ringan setelah aktivitas berat sebelumnya disebabkan oleh adaptasi fisik. Terapi Amlodipine 5mg dilanjutkan dengan anjuran rutin olahraga jalan santai 30 menit setiap hari dan batasi natrium/garam."
            </p>
          </div>

          <div style="background:var(--bg-muted); border:1px solid var(--border); border-radius:var(--radius-md); padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
              <b style="color:var(--text-primary); font-size:13.5px;">Dr. Saran Mitchell, Sp.OT (Spesialis Ortopedi)</b>
              <span style="color:var(--text-tertiary); font-size:11.5px;">10 Jan 2026 · 10:45 WIB</span>
            </div>
            <p style="color:var(--text-secondary); font-size:12.5px; line-height:1.6; margin:0;">
              "Evaluasi pasca fisioterapi lutut kanan menunjukkan perbaikan yang signifikan. Range of Motion (ROM) kembali penuh tanpa hambatan, tidak ada pembengkakan atau efusi sendi. Pasien sudah diizinkan beraktivitas normal tanpa keluhan nyeri tekan."
            </p>
          </div>
        </div>
      </section>
    </div>

  </main>
</div>

<!-- ========================================== -->
<!-- FLOATING BOTTOM NAV (Mobile < 860px)       -->
<!-- ========================================== -->
<nav class="floating-bottom-nav" aria-label="Navigasi Bawah">
  <a href="{{ route('calendar') }}" aria-label="Jadwal">
    <svg><use href="#i-cal"/></svg>
  </a>
  <a href="{{ route('home') }}" aria-label="Beranda">
    <svg><use href="#i-home"/></svg>
  </a>
  <a href="{{ route('rekam-medis') }}" class="active" aria-label="Rekam Medis">
    <svg><use href="#i-doc"/></svg>
  </a>
  <button type="button" aria-label="Pengaturan" onclick="showToast('Tab Pengaturan...')">
    <svg><use href="#i-set"/></svg>
  </button>
</nav>

<!-- ========================================== -->
<!-- MODAL: EDIT DATA PASIEN                    -->
<!-- ========================================== -->
<div id="modalBackdrop" class="modal-backdrop" onclick="closeAllModals()"></div>

<div id="editPatientModal" class="modal">
  <div class="modal-head">
    <h3>Ubah Data Pasien</h3>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <form onsubmit="handleEditPatientSubmit(event)">
    <div class="form-group">
      <label>Nama Lengkap Pasien</label>
      <input type="text" id="editName" class="form-control" value="{{ $user['name'] ?? 'Rina Anggraini' }}" required>
    </div>
    <div class="form-group">
      <label>Nomor Telepon WhatsApp</label>
      <input type="text" id="editPhone" class="form-control" value="{{ $user['phone'] ?? '+62 812 5550 1920' }}" required>
    </div>
    <div class="form-group">
      <label>Alamat Domisili</label>
      <textarea id="editAddress" class="form-control" rows="3">{{ $user['address'] ?? 'Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan, 12190' }}</textarea>
    </div>
    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
      <button type="button" class="btn" onclick="closeAllModals()">Batal</button>
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>

<!-- ========================================== -->
<!-- MODAL: QUICK ACTION                        -->
<!-- ========================================== -->
<div id="quickActionModal" class="modal">
  <div class="modal-head">
    <h3 id="qaModalTitle">Aksi Cepat Medis</h3>
    <button class="modal-close" onclick="closeAllModals()">✕</button>
  </div>
  <form onsubmit="handleQuickActionSubmit(event)">
    <div class="form-group">
      <label>Pasien</label>
      <input type="text" class="form-control" value="{{ $user['name'] ?? 'Rina Anggraini' }} ({{ $user['mrn'] ?? '#GM-0214' }})" disabled>
    </div>
    <div class="form-group">
      <label id="qaLabel">Detail Permintaan</label>
      <input type="text" id="qaInput" class="form-control" placeholder="Tuliskan spesifikasi pemeriksaan / permohonan..." required>
    </div>
    <div class="form-group">
      <label>Catatan Tambahan</label>
      <textarea class="form-control" rows="2" placeholder="Catatan indikasi klinis dokter..."></textarea>
    </div>
    <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px;">
      <button type="button" class="btn" onclick="closeAllModals()">Batal</button>
      <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
    </div>
  </form>
</div>

<!-- Global Toast Notification -->
<div id="toast" class="toast" role="status"></div>

<!-- JavaScript Interaktif -->
<script>
// Tab Switching
function switchTab(tabId) {
  document.querySelectorAll('.tab-btn').forEach(function(b) {
    b.classList.toggle('active', b.getAttribute('data-tab') === tabId);
  });
  document.querySelectorAll('.tab-pane').forEach(function(p) {
    p.classList.toggle('active', p.id === 'tab-' + tabId);
  });
}

// Toast Notifier
var toastEl = document.getElementById('toast');
var toastTimer;
function showToast(msg) {
  if (!toastEl) return;
  toastEl.textContent = msg;
  toastEl.classList.add('show');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(function() {
    toastEl.classList.remove('show');
  }, 2400);
}

// Modal Handlers
function openEditPatientModal() {
  document.getElementById('modalBackdrop').classList.add('open');
  document.getElementById('editPatientModal').classList.add('open');
}

function handleEditPatientSubmit(e) {
  e.preventDefault();
  var newName = document.getElementById('editName').value.trim();
  var newPhone = document.getElementById('editPhone').value.trim();
  var newAddress = document.getElementById('editAddress').value.trim();

  if (newName) {
    document.getElementById('patientNameHeader').textContent = newName;
    document.getElementById('d-fullname').textContent = newName;
    var initials = newName.split(' ').map(function(w){return w[0];}).join('').substring(0,2).toUpperCase();
    document.getElementById('mainAvatar').textContent = initials;
  }
  if (newPhone) {
    document.getElementById('d-phone').textContent = newPhone;
  }
  if (newAddress) {
    document.getElementById('d-address').textContent = newAddress;
  }
  closeAllModals();
  showToast('✅ Data pasien berhasil diperbarui!');
}

function openQuickActionModal(title, placeholder) {
  document.getElementById('qaModalTitle').textContent = title;
  document.getElementById('qaInput').placeholder = placeholder;
  document.getElementById('modalBackdrop').classList.add('open');
  document.getElementById('quickActionModal').classList.add('open');
}

function handleQuickActionSubmit(e) {
  e.preventDefault();
  closeAllModals();
  showToast('✅ Permintaan berhasil diteruskan ke sistem medis klinik.');
}

function openConsultationModal() {
  openQuickActionModal('Buat Konsultasi Baru', 'Contoh: Keluhan flu, batuk, atau konsultasi dokter spesialis...');
}

function viewEncounterDetail(date, doctor, specialty, note) {
  openQuickActionModal('Detail Kunjungan: ' + date, doctor + ' (' + specialty + '): ' + note);
}

function closeAllModals() {
  document.getElementById('modalBackdrop').classList.remove('open');
  document.querySelectorAll('.modal').forEach(function(m) {
    m.classList.remove('open');
  });
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeAllModals();
  }
});

window.addEventListener('DOMContentLoaded', function() {
  if (new URLSearchParams(window.location.search).has('openConsultation')) {
    openConsultationModal();
  }
  const tabParam = new URLSearchParams(window.location.search).get('tab');
  if (tabParam) {
    switchTab(tabParam);
  }
});
</script>

</body>
</html>
