<?php
// ============================================================
//  includes/head-meta.php
//  Favicon + third-party chat widget, meant to be included
//  inside each public page's own <head>...</head>, e.g.:
//
//      <link rel="stylesheet" href="assets/css/style.css">
//      <?php include 'includes/head-meta.php'; 
// 
//
//  This used to live inside includes/header.php, but header.php
//  is included after <body> has already opened — which meant
//  every page shipped a second, invalid <head> nested inside its
//  <body>. Splitting it out here keeps this markup where browsers
//  (and search engines) actually expect it.
// ============================================================
?>
<link rel="shortcut icon" href="assets/images/ESD_69e8c39b15887.webp" type="image/x-icon">
<!-- Neexa Widget -->
<script>
  window.neexaAsyncInit = function () {
    window.neexa.init({
      agent_id: 'a2518be7-a25d-4235-8c87-19302452120b', mobile_mini_style: 'greeting_only',
    });
  };
</script>
<script async src="https://chat-widget.neexa.ai/main.js?nonce=1784711297610.682"></script>
<!-- End Neexa Widget -->
