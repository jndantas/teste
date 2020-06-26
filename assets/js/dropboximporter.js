
/*
 * Import file from dropbox
 */
	options = {
        success: function(files) {
          files.forEach(function(file) {
            saveDropboxFile(file);
          });
        },
        cancel: function() {
          //optional
        },
        linkType: "direct", 
        multiselect: false, 
        extensions: dropboxExtesions,
    };
    
    var button = Dropbox.createChooseButton(options);
    document.getElementById("dropbox-container").appendChild(button);