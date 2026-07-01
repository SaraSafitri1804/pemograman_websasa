<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Reward System</title>
  <!-- Material Symbols -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <?php if (isset($include_chartjs) && $include_chartjs): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php endif; ?>
  <!-- Tailwind Config -->
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          "colors": {
              "on-secondary-fixed-variant": "#38485d",
              "inverse-primary": "#b4c5ff",
              "error-container": "#ffdad6",
              "surface-container-lowest": "#ffffff",
              "outline": "#737686",
              "tertiary-container": "#bc4800",
              "inverse-on-surface": "#f0f0fb",
              "surface-variant": "#e1e2ed",
              "on-secondary-fixed": "#0b1c30",
              "surface": "#faf8ff",
              "on-secondary-container": "#54647a",
              "tertiary-fixed-dim": "#ffb596",
              "secondary": "#505f76",
              "primary-container": "#2563eb",
              "surface-bright": "#faf8ff",
              "secondary-fixed-dim": "#b7c8e1",
              "error": "#ba1a1a",
              "surface-tint": "#0053db",
              "secondary-fixed": "#d3e4fe",
              "on-primary-fixed-variant": "#003ea8",
              "surface-dim": "#d9d9e5",
              "surface-container": "#ededf9",
              "on-background": "#191b23",
              "on-tertiary": "#ffffff",
              "primary-fixed": "#dbe1ff",
              "on-surface-variant": "#434655",
              "surface-container-high": "#e7e7f3",
              "secondary-container": "#d0e1fb",
              "on-tertiary-fixed": "#360f00",
              "on-primary": "#ffffff",
              "background": "#faf8ff",
              "on-surface": "#191b23",
              "on-tertiary-container": "#ffede6",
              "inverse-surface": "#2e3039",
              "surface-container-low": "#f3f3fe",
              "outline-variant": "#c3c6d7",
              "on-primary-container": "#eeefff",
              "on-secondary": "#ffffff",
              "tertiary-fixed": "#ffdbcd",
              "tertiary": "#943700",
              "on-tertiary-fixed-variant": "#7d2d00",
              "on-primary-fixed": "#00174b",
              "primary-fixed-dim": "#b4c5ff",
              "surface-container-highest": "#e1e2ed",
              "on-error": "#ffffff",
              "on-error-container": "#93000a",
              "primary": "#004ac6"
          },
          "borderRadius": {
              "DEFAULT": "0.25rem",
              "lg": "0.5rem",
              "xl": "0.75rem",
              "full": "9999px"
          },
          "spacing": {
              "margin-mobile": "16px",
              "stack-lg": "24px",
              "stack-md": "16px",
              "sidebar-width": "280px",
              "gutter": "24px",
              "container-max": "1440px",
              "stack-sm": "8px",
              "margin-desktop": "32px"
          },
          "fontFamily": {
              "body-md": ["Inter"],
              "body-lg": ["Inter"]
          },
          "fontSize": {
              "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.01em", "fontWeight": "600"}],
              "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
              "display-lg": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
              "label-sm": ["11px", {"lineHeight": "14px", "fontWeight": "500"}],
              "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
              "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
              "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
              "display-md": ["30px", {"lineHeight": "38px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
          }
        }
      }
    };
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    .material-symbols-outlined.filled { font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24; }

    /* ===== Sidebar Mobile Animation ===== */
    #sidebar {
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #sidebar-overlay {
      transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ===== Glass Card Utility ===== */
    .glass-card {
      background: #ffffff;
      border: 1px solid #c3c6d7;
      box-shadow: 0px 1px 3px rgba(0,0,0,0.05);
    }

    /* ===== Custom Scrollbar ===== */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #c3c6d7; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #737686; }

    /* ===== Responsive font for display on mobile ===== */
    @media (max-width: 640px) {
      .text-display-md { font-size: 22px !important; line-height: 30px !important; }
      .text-display-lg { font-size: 28px !important; line-height: 36px !important; }
      .text-headline-lg { font-size: 20px !important; line-height: 28px !important; }
    }
  </style>
</head>
<body class="bg-background text-on-background font-body-md antialiased flex">