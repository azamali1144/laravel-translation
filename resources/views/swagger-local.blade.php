<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Swagger UI - Local</title>
  <!-- Local Swagger UI CSS -->
  <link rel="stylesheet" href="{{ asset('swagger-ui.css') }}">
  <style>
    html, body { height: 100%; margin: 0; padding: 0; }
    #swagger-ui { height: 100%; }
  </style>
</head>
<body>
  <div id="swagger-ui"></div>

  <!-- Local Swagger UI JS (loaded in correct order) -->
  <script src="{{ asset('swagger-ui-standalone-preset.js') }}"></script>
  <script src="{{ asset('swagger-ui.js') }}"></script>
  <script src="{{ asset('swagger-ui-bundle.js') }}"></script>

  <script>
    window.addEventListener('load', function() {
      try {
        const ui = SwaggerUIBundle({
          url: "{{ asset('openapi.yaml') }}",
          dom_id: '#swagger-ui',
          presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset
          ],
          layout: 'BaseLayout',
          requestInterceptor: (req) => {
            return req;
          }
        });
        window.ui = ui;
      } catch (e) {
        const div = document.getElementById('swagger-ui');
        if (div) {
          div.innerHTML = "<pre style='padding:20px;font-family:monospace;'>Swagger UI could not initialize. Ensure you downloaded the full distribution and that paths are correct.</pre>";
        }
        console.error(e);
      }
    });
  </script>
</body>
</html>
