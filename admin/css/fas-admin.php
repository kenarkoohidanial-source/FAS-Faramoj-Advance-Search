<?php
/**
 * Admin Panel Custom Styles
 * Outputs CSS dynamic content
 */

// Define glassmorphic style tokens for the settings dashboard
?>
.fas-admin-wrap {
    max-width: 1200px;
    margin: 20px auto;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    background: radial-gradient(circle at 10% 20%, rgba(0, 102, 204, 0.04) 0%, rgba(100, 116, 139, 0.02) 90%) !important;
    padding: 24px !important;
    border-radius: 16px !important;
}

/* Glassmorphism top bar and card design */
.fas-top-bar, .fas-card {
    background: rgba(255, 255, 255, 0.65) !important;
    backdrop-filter: blur(25px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(25px) saturate(180%) !important;
    border: 1px solid rgba(255, 255, 255, 0.5) !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.04), inset 0 1px 1px rgba(255, 255, 255, 0.45) !important;
    box-sizing: border-box !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.fas-top-bar:hover, .fas-card:hover {
    background: rgba(255, 255, 255, 0.75) !important;
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.07), inset 0 1px 1px rgba(255, 255, 255, 0.6) !important;
    transform: translateY(-2px) !important;
}

/* Card titles alignment and line */
.fas-card h3 {
    margin-top: 0;
    font-size: 16px;
    font-weight: 700;
    border-bottom: 1px solid rgba(255, 255, 255, 0.4);
    padding-bottom: 12px;
    color: #0f172a;
}

/* Glassmorphic inputs and select boxes */
.fas-admin-wrap input[type="text"],
.fas-admin-wrap input[type="number"],
.fas-admin-wrap select {
    background: rgba(255, 255, 255, 0.5) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(203, 213, 225, 0.8) !important;
    border-radius: 8px !important;
    padding: 6px 12px !important;
    transition: all 0.2s ease !important;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
    color: #1e293b !important;
    outline: none !important;
}

.fas-admin-wrap input[type="text"]:focus,
.fas-admin-wrap input[type="number"]:focus,
.fas-admin-wrap select:focus {
    background: rgba(255, 255, 255, 0.85) !important;
    border-color: #0066cc !important;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15) !important;
}

/* Glassmorphic sortable drag handles */
.fas-sortable-item {
    background: rgba(255, 255, 255, 0.75) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.6) !important;
    border-radius: 20px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02) !important;
    transition: all 0.2s ease !important;
}

.fas-sortable-item:hover {
    background: rgba(255, 255, 255, 0.95) !important;
    transform: scale(1.03) !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04) !important;
}

/* Secondary & upload buttons styled as premium glass buttons */
.fas-upload-btn, .fas-admin-wrap .button-secondary {
    background: rgba(255, 255, 255, 0.45) !important;
    backdrop-filter: blur(10px) !important;
    -webkit-backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(203, 213, 225, 0.8) !important;
    border-radius: 6px !important;
    color: #475569 !important;
    transition: all 0.2s ease !important;
    padding: 6px 14px !important;
    height: auto !important;
    font-weight: 600 !important;
}

.fas-upload-btn:hover, .fas-admin-wrap .button-secondary:hover {
    background: rgba(255, 255, 255, 0.8) !important;
    border-color: #0066cc !important;
    color: #0066cc !important;
}

.fas-form-table th {
    width: 220px;
    padding: 15px 10px 15px 0;
    font-weight: 700;
    color: #334155;
}

.fas-form-table td {
    padding: 15px 10px;
}

.fas-description {
    color: #64748b;
    font-size: 11px;
    margin-top: 6px;
    display: block;
}

.fas-logo-badge {
    background: #0066cc;
    color: #fff;
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 14px;
    letter-spacing: 0.5px;
}
