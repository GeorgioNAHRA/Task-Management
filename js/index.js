document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                window.location.href = 'index.php';
            } else {
                document.getElementById('error-message').innerText = response.message;
            }
        }
    };
    xhr.send(formData);
});