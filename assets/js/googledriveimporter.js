      // Scope to use to access user's photos.
      var scope = 'https://www.googleapis.com/auth/drive';

      var pickerApiLoaded = false;
      var oauthToken;

      // Use the API Loader script to load google.picker and gapi.auth.
      function onApiLoad() {
        gapi.load('auth2', onAuthApiLoad);
        gapi.load('picker', onPickerApiLoad);
      }

      function onAuthApiLoad() {
        var authBtn = document.getElementById('auth');
        authBtn.disabled = false;
        authBtn.addEventListener('click', function() {
          gapi.auth2.authorize({
            client_id: clientId,
            scope: scope
          }, handleAuthResult);
        });
      }

      function onPickerApiLoad() {
        pickerApiLoaded = true;
        createPicker();
      }

      function handleAuthResult(authResult) {
        if (authResult && !authResult.error) {
          oauthToken = authResult.access_token;
          createPicker();
        }
      }

      // Create and render a Picker object for picking user Photos.
      function createPicker() {
        if (pickerApiLoaded && oauthToken) {
          if (allowNonPDF === "Enabled") {
            var picker = new google.picker.PickerBuilder().
                addView(google.picker.ViewId.DOCUMENTS).
                addView(google.picker.ViewId.SPREADSHEETS).
                addView(google.picker.ViewId.PRESENTATIONS).
                addView(google.picker.ViewId.PDFS).
                setOAuthToken(oauthToken).
                setDeveloperKey(developerKey).
                setCallback(pickerCallback).
                build();
          }else{
            var picker = new google.picker.PickerBuilder().
                addView(google.picker.ViewId.PDFS).
                setOAuthToken(oauthToken).
                setDeveloperKey(developerKey).
                setCallback(pickerCallback).
                build();
          }
          picker.setVisible(true);
        }
      }

      // A simple callback implementation.
      function pickerCallback(data) {
        if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
          var doc = data[google.picker.Response.DOCUMENTS][0];
          saveGoogleDriveFile(doc.id, doc.name, doc.serviceId);
        }
        
      }