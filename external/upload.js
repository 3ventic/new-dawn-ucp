(function () {
    var inProgress = false;
    var req = new XMLHttpRequest();
    req.upload.addEventListener("progress", updateProgress, false);
    req.addEventListener("load", transferComplete, false);
    req.upload.addEventListener("error", transferError, false);
    req.upload.addEventListener("abort", transferAbort, false);

    function updateProgress(evt) {
        if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total;
            $('#progress_inner').width($('#progress_outer').width() * percentComplete);
            $('#progress_text').text((percentComplete * 100).toFixed(1) + "%");
        }
    }
    function transferComplete(evt) {
        console.log(evt);
        alert(req.responseText);
        inProgress = false;
    }
    function transferError(evt) {
        alert('Error!');
    }
    function transferAbort(evt) {
        alert('Aborted!');
    }
    document.getElementById('upload-button').onclick = function () {
        if (inProgress == false) {
            inProgress = true;
            req.open("post", "/upload", true);
            req.send(new FormData(document.getElementById("upload-form")));
        }
        else {
            alert("An upload is already in progress!");
        }
    }
}());